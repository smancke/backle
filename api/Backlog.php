<?php

/**
 * TODO: Prevent from SQL Injection e.g. with prepared statements
 */
class Backlog {

    protected $db;

    protected $user;
    protected $userId;
   
    public function open($cfg) {
        $this->db = mysql_connect($cfg['dbhost'], $cfg['dbuser'], $cfg['dbpassword']);
        $this->checkResult($this->db);
        $res = mysql_select_db($cfg['dbname'], $this->db);
        $this->checkResult($res);
    }

    /**
     * set the user/owner for all queries
     */
    public function setUser($user) {
        $this->user = $user;
        $this->userId = 42;
    }
    
    /**
     * Checks a database result or handle.
     * Raises an exception, if it evaluates to false
     */
    protected function checkResult($result) {
        if (! $result) {
            throw new RuntimeException('DB error '. mysql_errno($this->db) . ": " . mysql_error($this->db) );
        }
    }

    /**
     * executes a database query 
     * and raises an exception, if an error occurs.
     * Returns the result.
     */
    protected function query($sql) {
        //echo $sql ."\n";
        $result = mysql_query($sql, $this->db);
        $this->checkResult($result);
        return $result;
    }

    protected function fetchAll($result) {
        $table = array();
        while($row = mysql_fetch_assoc($result)) {
            array_push($table, $row);
        }
        return $table;
    }
            
    public function createBacklog($backlogName) {
        $result = $this->query("INSERT INTO backlog (backlogname, owner_id, created) VALUES ('$backlogName', '". $this->userId ."', NOW())", $this->db);
        return mysql_insert_id($this->db);
    }

    /**
     * Returns all Backlogs
     */
    public function getBacklogs() {
        $result = $this->query('SELECT backlogname FROM backlog', $this->db);
        return $this->fetchAll($result);
    }

    /**
     * savely returns the property or '' if the object does not have the named property
     */
    private function prop($object, $propName) {
        if (property_exists($object, $propName)) {
            return $object->$propName;
        }
        return '';
    }

    public function createItem($backlogName, $itemData) {
        $order = $this->getMinBacklogOrder($backlogName) - 1;
        if (! property_exists($itemData, 'status') || !$itemData->status )
            $itemData->status = 'open';

        if (! property_exists($itemData, 'type') || !$itemData->type )
            $itemData->type = 'story';

        if (! property_exists($itemData, 'detail') || !$itemData->detail )
            $itemData->detail = "<p><strong>Requirements</strong></p><ul><li>...</li></ul><p><strong>Constraints</strong></p><ul><li>...</li></ul><p><strong>User Acceptance Criteria</strong></p><ul><li>...</li></ul><p><strong>User Acceptance Tests</strong></p><ul><li>...</li></ul><p>&nbsp;</p>";

        $result = mysql_query("INSERT INTO item (backlog_id, type, title, text, detail, points, status, backlogorder, created) VALUES ('"
                              . $this->getBacklogIdByName($backlogName) 
                              ."', '".$this->prop($itemData, 'type')
                              ."', '".$this->prop($itemData, 'title')
                              ."', '".$this->prop($itemData, 'text')
                              ."', '".$this->prop($itemData, 'detail')
                              ."', '".$this->prop($itemData, 'points')
                              ."', '".$this->prop($itemData, 'status')
                              ."', '$order', NOW())", $this->db);
        return mysql_insert_id($this->db);
    }

    public function updateItem($backlogName, $id, $itemData) {
        $fields = ['type', 'title', 'text', 'detail', 'status', 'points'];
        $q = "UPDATE item SET ";
        foreach ($fields as $field) {
            if (property_exists($itemData, $field)) {
                $q .= $field ." = '" . $itemData->$field ."', ";
            }
        }
        $q .= "changed = NOW()";
        if (property_exists($itemData, 'status')) {
            if ($itemData->status == 'done') {
                $q .= ", done = NOW()";
            } else {
                $q .= ", done = null";
            }
        }
        $q .= " WHERE backlog_id = ".$this->getBacklogIdByName($backlogName) ." AND id = ".$id;
        error_log($q);
        $result = mysql_query($q);
    }

    public function moveItemToBegin($backlogName, $id) {
        $this->query("UPDATE item SET backlogorder = ".($this->getMinBacklogOrder($backlogName) -1)." WHERE backlog_id = ".$this->getBacklogIdByName($backlogName) ." AND id = ".$id, $this->db);
    }

    /**
     * Places the supplied item behind the previousItem item.
     * To achive this, the stories backlogorder are set to the same value
     * and then all stories after the 'previousItem' are advanced by one.
     */
    public function moveItemBehind($backlogName, $id, $previousItemId) {
        $previousItem = $this->getItem($backlogName, $previousItemId);
        
        // moving the item at the same position
        $this->query("UPDATE item SET backlogorder = '".$previousItem['backlogorder']."' WHERE backlog_id = ".$this->getBacklogIdByName($backlogName) ." AND id = ".$id, $this->db);
        
        // moving all following stories one step down
        $this->query("UPDATE item SET backlogorder = backlogorder +1 WHERE backlog_id = ".$this->getBacklogIdByName($backlogName) ." AND not id = ". $previousItemId ." AND backlogorder >= ".$previousItem['backlogorder'], $this->db);
    }
    
    /**
     * Retuns the minimal value for a backlog order within a given backlog.
     */
    public function getMinBacklogOrder($backlogName) {
        $result = $this->query('SELECT MIN(backlogorder) FROM item WHERE backlog_id = '.$this->getBacklogIdByName($backlogName));
        $row = mysql_fetch_row($result);
        return $row[0];
    }

    public function getBacklogIdByName($backlogName) {
        $result = $this->query("SELECT id FROM backlog WHERE backlogname = '".$backlogName."'");
        $row = mysql_fetch_row($result);
        return $row[0];
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
        $result = $this->query('SELECT id, type, title, text, status, backlogorder, points, done, created, changed FROM item WHERE backlog_id = '.$this->getBacklogIdByName($backlogName).' ORDER BY backlogorder', $this->db);
        return $this->fetchAll($result);
    }

    /**
     * Returns one Item within the backlog
     */
    public function getItem($backlogName, $id) {
        $result = $this->query('SELECT id, type, title, text, detail, status, backlogorder, points, done, created, changed FROM item WHERE backlog_id = '.$this->getBacklogIdByName($backlogName) .' AND id = '.$id, $this->db);
        return mysql_fetch_assoc($result);
    }


    /**
     * Delete one Item within the backlog
     */
    public function deleteItem($backlogName, $id) {
        $result = $this->query('DELETE FROM item WHERE backlog_id = '.$this->getBacklogIdByName($backlogName) .' AND id = '.$id, $this->db);
    }

    public function close() {
        mysql_close($this->db);
    }
}


?>