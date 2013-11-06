<?php

/**
 * TODO: Prevent from SQL Injection e.g. with prepared statements
 */
class Backlog {

    protected $db;

    protected $userId;
   
    public function __construct($db) {
        $this->db = $db;
    }                                           

    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function createBacklog($backlogName, $backlogtitle, $isPublicViewable, $projectId) {
        $insData = ['backlogname' => $backlogName,
                    'backlogtitle' => $backlogtitle,
                    'is_public_viewable' => $isPublicViewable,
                    'owner_id' => $this->userId, 
                    'created' => date('Y-m-d H:i:s',time())];

        if ($projectId != null) {
            $insData['project_id'] = $projectId;
        }
            
        return $this->db->insert($insData, 'backlog');
    }

    /**
     * Returns all Backlogs
     */
    public function getBacklogs() {
        return $this->db->fetchRows('SELECT backlogname FROM backlog');
    }

    /**
     * Returns the Backlog
     */
    public function getBacklog($backlogName) {
        return $this->db->fetchRow('SELECT * FROM backlog where backlogname = ?', [$backlogName]);
    }

    /**
     * savely returns the property or $default if the object does not have the named property
     */
    private function prop($object, $propName, $default=null) {
        if (property_exists($object, $propName) && $object->$propName != '') {
            return $object->$propName;
        }
        return $default;
    }

    /**
     * Creates an item
     */
    public function createItem($backlogName, $itemData) {

        $detailDefault = <<<EOT
            <p><strong>Requirements</strong></p><ul><li>...</li></ul>
            <p><strong>Constraints</strong></p><ul><li>...</li></ul>
            <p><strong>User Acceptance Criteria</strong></p><ul><li>...</li></ul>
            <p><strong>User Acceptance Tests</strong></p><ul><li>...</li></ul>
            <p>&nbsp;</p>
EOT;

        $insData =  [
                     'backlog_id' => $this->getBacklogIdByName($backlogName) , 
                     'type' => $this->prop($itemData, 'type', 'story'), 
                     'title' => $this->prop($itemData, 'title'), 
                     'text' => $this->prop($itemData, 'text'), 
                     'detail' => $this->prop($itemData, 'detail', $detailDefault), 
                     'points' => $this->prop($itemData, 'points'), 
                     'status' => $this->prop($itemData, 'status', 'open'),
                     'backlogorder' => $this->getMinBacklogOrder($backlogName) - 1,
                     'created' => date('Y-m-d H:i:s',time())
                     ];

        return $this->db->insert($insData, 'item');
    }

    public function updateItem($backlogName, $id, $itemData) {
        $fields = ['type', 'title', 'text', 'detail', 'status', 'points'];
        $udapteData = ['changed' => date('Y-m-d H:i:s',time())];
            
        foreach ($fields as $field) {
            if (property_exists($itemData, $field)) {
                $udapteData[$field] = $itemData->$field;
            }
        }

        if ($udapteData['points'] === '') {
            $udapteData['points'] = Null;
        }

        if (property_exists($itemData, 'status')) {
            if ($itemData->status == 'done') {
                $udapteData['done'] = date('Y-m-d H:i:s',time());
            } else {
                $udapteData['done'] = null;
            }
        }
        $this->db->update($udapteData, 'item', ['backlog_id' => $this->getBacklogIdByName($backlogName), 'id' => $id]);
    }
        
    public function moveItemToBegin($backlogName, $id) {
        $this->db->update(['backlogorder' => ($this->getMinBacklogOrder($backlogName) -1)],
                      'item',
                      ['backlog_id' => $this->getBacklogIdByName($backlogName),
                       'id' => $id]);
    }

    /**
     * Places the supplied item behind the previousItem item.
     * To achive this, the stories backlogorder are set to the same value
     * and then all stories after the 'previousItem' are advanced by one.
     */
    public function moveItemBehind($backlogName, $id, $previousItemId) {
        $previousItem = $this->getItem($backlogName, $previousItemId);
        
        // moving the item at the same position
        $this->db->update(['backlogorder' => $previousItem['backlogorder']],
                                'item',
                                ['backlog_id' => $this->getBacklogIdByName($backlogName), 'id' => $id]);
        
        // moving all following stories one step down
        $this->db->execute('UPDATE item SET backlogorder = backlogorder +1 WHERE backlog_id = ? AND not id = ? AND backlogorder >= ?', 
                                 [$this->getBacklogIdByName($backlogName), $previousItemId, $previousItem['backlogorder']]);
    }
    
    /**
     * Retuns the minimal value for a backlog order within a given backlog.
     */
    public function getMinBacklogOrder($backlogName) {
        return $this->db->fetchCell('SELECT MIN(backlogorder) FROM item WHERE backlog_id = ?', [$this->getBacklogIdByName($backlogName)]);
    }

    public function getBacklogIdByName($backlogName) {
        return $this->db->fetchCell("SELECT id FROM backlog WHERE backlogname = ?", [$backlogName]);
    }

    /**
     * returns an array with rights of the current user for the supplied backlog
     */
    public function getRights($backlogName) {
        $backlog = $this->getBacklog($backlogName);
        $isOwner = $backlog['owner_id'] == $this->userId;
        $rights = [
                   'read' => $isOwner || $backlog['is_public_viewable'],
                   'write' => $isOwner
                   ];
        return $rights;
    }

    /**
     * Returns all Stories within the backlog
     */
    public function getItems($backlogName) {
        return $this->db->fetchRows('SELECT id, type, title, text, status, backlogorder, points, done, created, changed FROM item WHERE backlog_id = ? ORDER BY backlogorder', 
                                          [$this->getBacklogIdByName($backlogName)]);
    }

    /**
     * Returns one Item within the backlog
     */
    public function getItem($backlogName, $id) {
        return $this->db->fetchRow('SELECT id, type, title, text, detail, status, backlogorder, points, done, created, changed FROM item WHERE backlog_id = ? AND id = ?',
                                         [$this->getBacklogIdByName($backlogName), $id]);
    }

    /**
     * Delete one Item within the backlog
     */
    public function deleteItem($backlogName, $id) {
        $this->db->delete('item', ['backlog_id' => $this->getBacklogIdByName($backlogName), 'id' => $id]);
    }
}

?>