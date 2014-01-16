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

    public function getUserId() {
        return $this->userId;
    }
    
    public function createBacklog($backlogName, $backlogtitle, $isPublicViewable, $projectId, $isProjectDefault=false) {
        $insData = ['backlogname' => $backlogName,
                    'backlogtitle' => $backlogtitle,
                    'is_public_viewable' => $isPublicViewable,
                    'owner_id' => $this->userId, 
                    'project_id' => $projectId,
                    'is_project_default' => $isProjectDefault,
                    'created' => date('Y-m-d H:i:s',time())];

        return $this->db->insert($insData, 'backlog');
    }

    /**
     * Returns all Backlogs
     */
    public function getBacklogs($projectname) {
        if ($projectname)
            return $this->db->fetchRows('SELECT backlog.id, backlogname, backlogtitle, backlog.is_public_viewable, owner_id, project_id, backlog.created, is_project_default FROM backlog WHERE project_id = ? ORDER BY backlogtitle', [$this->getProjectId($projectname)]);
        else
            return $this->db->fetchRows('SELECT id, backlogname, backlogtitle, is_public_viewable, owner_id, project_id, created FROM backlog ORDER BY backlogtitle');
    }

    /**
     * Returns the Backlog
     */
    public function getBacklog($projectname, $backlogName) {
        return $this->db->fetchRow('SELECT * FROM backlog where project_id = ? AND backlogname = ?', [$this->getProjectId($projectname), $backlogName]);
    }


    /**
     * Returns the name of the fedault backlog for a project
     */
    public function getDefaultBacklogName($projectname) {
        return $this->db->fetchCell('SELECT backlogname FROM backlog where is_project_default = 1 AND project_id = ?', [$this->getProjectId($projectname)]);
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
    public function createItem($projectname, $backlogName, $itemData) {

        $detailDefault = <<<EOT
            <p><strong>Requirements</strong></p><ul><li>...</li></ul>
            <p><strong>Constraints</strong></p><ul><li>...</li></ul>
            <p><strong>User Acceptance Criteria</strong></p><ul><li>...</li></ul>
            <p><strong>User Acceptance Tests</strong></p><ul><li>...</li></ul>
            <p>&nbsp;</p>
EOT;

        $insData =  [
                     'backlog_id' => $this->getBacklogIdByName($projectname, $backlogName) , 
                     'type' => $this->prop($itemData, 'type', 'story'), 
                     'title' => $this->prop($itemData, 'title'), 
                     'text' => $this->prop($itemData, 'text'), 
                     'detail' => $this->prop($itemData, 'detail', $detailDefault), 
                     'points' => $this->prop($itemData, 'points'), 
                     'status' => $this->prop($itemData, 'status', 'open'),
                     'backlogorder' => $this->getMinBacklogOrder($projectname, $backlogName) - 1,
                     'author_id' => $this->userId,
                     'created' => date('Y-m-d H:i:s',time())
                     ];

        return $this->db->insert($insData, 'item');
    }

    public function updateItem($projectname, $backlogName, $id, $itemData) {
        $fields = ['type', 'title', 'text', 'detail', 'status', 'points'];
        $udapteData = ['changed' => date('Y-m-d H:i:s',time())];
            
        foreach ($fields as $field) {
            if (property_exists($itemData, $field)) {
                $udapteData[$field] = $itemData->$field;
            }
        }

        if (isset($udapteData['points']) && $udapteData['points'] === '') {
            $udapteData['points'] = Null;
        }

        if (property_exists($itemData, 'status')) {
            if ($itemData->status == 'done') {
                $udapteData['done'] = date('Y-m-d H:i:s',time());
            } else {
                $udapteData['done'] = null;
            }
        }

        $this->db->update($udapteData, 'item', ['backlog_id' => $this->getBacklogIdByName($projectname, $backlogName), 'id' => $id]);
    }
        
    public function moveItemToBegin($projectname, $backlogName, $id) {
        $this->db->update(['backlogorder' => ($this->getMinBacklogOrder($projectname, $backlogName) -1)],
                      'item',
                      ['backlog_id' => $this->getBacklogIdByName($projectname, $backlogName),
                       'id' => $id]);
    }

    /**
     * Places the supplied item behind the previousItem item.
     * To achive this, the stories backlogorder are set to the same value
     * and then all stories after the 'previousItem' are advanced by one.
     */
    public function moveItemBehind($projectname, $backlogName, $id, $previousItemId) {
        $previousItem = $this->getItem($projectname, $backlogName, $previousItemId);
        
        // moving the item at the same position
        $this->db->update(['backlogorder' => $previousItem['backlogorder']],
                          'item',
                          ['backlog_id' => $this->getBacklogIdByName($projectname, $backlogName), 'id' => $id]);
        
        // moving all following stories one step down
        $this->db->execute('UPDATE item SET backlogorder = backlogorder +1 WHERE backlog_id = ? AND not id = ? AND backlogorder >= ?', 
                                 [$this->getBacklogIdByName($projectname, $backlogName), $previousItemId, $previousItem['backlogorder']]);
    }

    /**
     * Returns the id of a project
     */
    public function getProjectId($projectname) {
        return $this->db->fetchCell('SELECT id from project WHERE name = ?', [$projectname]);
    }
    
    /**
     * Retuns the minimal value for a backlog order within a given backlog.
     */
    public function getMinBacklogOrder($projectname, $backlogName) {
        return $this->db->fetchCell('SELECT MIN(backlogorder) FROM item WHERE backlog_id = ?', [$this->getBacklogIdByName($projectname, $backlogName)]);
    }

    public function getBacklogIdByName($projectname, $backlogName) {
        if ($backlogName == 'default') {
            return $this->db->fetchCell('SELECT id FROM backlog WHERE is_project_default = 1 AND project_id = ?', [$this->getProjectId($projectname)]);
        } else {
            return $this->db->fetchCell("SELECT id FROM backlog WHERE project_id = ? AND backlogname = ?", [$this->getProjectId($projectname), $backlogName]);
        }
    }

    /**
     * returns an array with rights of the current user for the supplied backlog
     */
    public function getRights($projectname) {
        $userRights = Null;

        if ($this->userId && $this->getProjectId($projectname) ) {
            $userRights = $this->db->fetchRow("SELECT '1' as \"read\", is_owner as owner, can_write as \"write\" FROM user_project WHERE user_id = ? AND project_id = ?", 
                                              [$this->userId, $this->getProjectId($projectname)]);
        }

        if (!$userRights) {
            $userRights = ['read' => 0,
                           'owner' => 0,
                           'write' => 0];
        }

        if (!$userRights['read']) {
            $userRights['read'] = $this->db->fetchCell('SELECT is_public_viewable from project WHERE name = ?', [$projectname]);
        }

        return $userRights;
    }

    /**
     * Returns all Stories within the backlog
     */
    public function getItems($projectname, $backlogName) {
        return $this->db->fetchRows('SELECT id, type, title, text, detail, status, backlogorder, points, done, created, changed FROM item WHERE backlog_id = ? ORDER BY backlogorder', 
                                          [$this->getBacklogIdByName($projectname, $backlogName)]);
    }

    /**
     * Returns one Item within the backlog
     */
    public function getItem($projectname, $backlogName, $id) {
        return $this->db->fetchRow('SELECT id, type, title, text, detail, status, backlogorder, points, done, created, changed FROM item WHERE backlog_id = ? AND id = ?',
                                         [$this->getBacklogIdByName($projectname, $backlogName), $id]);
    }

    /**
     * Delete one Item within the backlog
     */
    public function deleteItem($projectname, $backlogName, $id) {
        $this->db->delete('item', ['backlog_id' => $this->getBacklogIdByName($projectname, $backlogName), 'id' => $id]);
    }
}

?>