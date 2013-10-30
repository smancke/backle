<?php

/**
 * TODO: Prevent from SQL Injection e.g. with prepared statements
 */
class Backlog {

    protected $db;

    protected $user;
    protected $userId;
   
    public function __construct($db) {
        $this->db = $db;
    }                                           

    /**
     * set the user/owner for all queries
     * If it does not exist, we create it within the database.
     */
    public function setAndCreateUserIfNotExists($username, $origin) {

        $this->username = $username;
        
        $user = $this->db->fetchRow("SELECT * FROM user WHERE username = ? AND origin = ?", [$username, $origin]);
        if ($user) {
            
            $this->userId = $user['id'];

        } else {
            $insData = ['username' => $username,
                        'origin' => $origin, 
                        'created' => date('Y-m-d H:i:s',time())];
            $result = $this->db->insert($insData, 'user');
            $this->userId = mysql_insert_id($this->db);
        }

    }

    public function setUser($username) {
        $this->username = $username;
        $this->userId = 42;
    }
    
    public function createBacklog($backlogName) {
        $insData = ['backlogname' => $backlogName,
                    'owner_id' => $this->userId, 
                    'created' => date('Y-m-d H:i:s',time())];
        
        return $this->db->insert($insData, 'backlog');
    }

    /**
     * Returns all Backlogs
     */
    public function getBacklogs() {
        return $this->db->fetchRows('SELECT backlogname FROM backlog');
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
     * Check, if a given backlog is readeable for a user.
     * Since wo don't have private backlogs yet, 
     * this only checks, if the backlog exists.
     */
    public function isReadeableForUser($backlogName) {
        if ($this->getBacklogIdByName($backlogName)) {
            return true;
        } else {
            return false;
        }
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
        $this->db->delete('item', ['backlog_id' => $this->getBacklogIdByName($backlogName), id => $id]);
    }
}

?>