<?php
/*
dbFacile - A Database abstraction that should have existed from the start
Version 0.4.3
See LICENSE for license details.
*/

abstract class dbFacile {
	protected $connection; // handle to Database connection
	protected $logFile;
	protected $fullSQL; // holds previously executed SQL statement
	protected $_queryCount = 0;

	// implement these methods to create driver subclass
	public abstract function affectedRows($result = null);
	//public function beginTransaction();
	public abstract function close();
	//public function commitTransaction();
	public abstract function error();
	public abstract function escapeString($string);
	public abstract function lastID($table = null);
	public abstract function numberRows($result);
	//public abstract function open();
	//public function quoteField($field);
	public abstract function rewind($result);
	//public function rollbackTransaction();

	protected abstract function _fetch($result);
	protected abstract function _fetchAll($result);
	protected abstract function _fetchRow($result);
	//protected abstract function _fields($table);
	// Should return a result handle, or false
	protected abstract function _query($sql);

	public function __construct($handle = null) {
		$this->connection = $handle;
	}

        public function setLogile($logFile) {
            $this->logFile = fopen($logFile, "a");
        }

	/*
	 * Performs a query using the given string.
	 * Used by the other _query functions.
	 * */
	public function execute($sql, $parameters = array()) {
		$this->fullSQL = $this->makeQuery($sql, $parameters);

                //		/*
		if($this->logFile)
			$time_start = microtime(true);
                //		*/

		$result = $this->_query($this->fullSQL); // sets $this->result
		$this->_queryCount++;

                //		/*
		if($this->logFile) {
			$time_end = microtime(true);
			fwrite($this->logFile, date('Y-m-d H:i:s') . "\n" . $this->fullSQL . "\n" . number_format($time_end - $time_start, 8) . " seconds\n\n");
		}

		if(!$result && (error_reporting() & 1))
			trigger_error('dbFacile - Error in query: ' . $this->fullSQL . ' : ' . $this->error(), E_USER_WARNING);
                //		*/

		// I know getting a real true or false is handy,
		// but returning the result handle gives more flexibility
		// and honestly, many oof the convenience functions check result anyway, so just pass it to them
		return $result;
	}

	public function previousQuery() {
		return $this->fullSQL;
	}
	public function queryCount() {
		return $this->_queryCount;
	}

	/*
	 * Passed an array and a table name, it attempts to insert the data into the table.
	 * Check for boolean false to determine whether insert failed
	 * */
	public function insert($data, $table) {
		$fields = array_map( array($this,'quoteField'), array_keys($data) );
		$values = array_map( array($this,'quoteEscapeString'), array_values($data) );
		$sql = 'insert into ' . $this->quoteField($table) . ' (' . implode(',', $fields) . ') values(' . implode(',', $values) . ')';
		$result = $this->execute($sql);
		if(!$result) {
			// Error
			return false;
		}
		$id = $this->lastID($table);
		if ($id === false) return true; // no id generated by insert
		return $id;
	}

	/*
	 * Passed an array, table name, where clause, and placeholder parameters, it attempts to update a record.
	 * Returns the number of affected rows
	 * */
	public function update($data, $table, $where = null, $parameters = array()) {
		$sql = 'update ' . $this->quoteField($table) . ' set ';
		foreach($data as $key => $value) {
			$sql .= $this->quoteField($key) . '=' . $this->quoteEscapeString($value) . ',';
		}
		$sql = substr($sql, 0, -1); // strip off last comma

		if($where) {
			$sql .= $this->whereHelper($where, $parameters);
		}
		$result = $this->execute($sql, $parameters);
		return $this->affectedRows($result);
	}

	public function delete($table, $where = null, $parameters = array()) {
		$sql = 'DELETE FROM ' . $this->quoteField($table);
		if($where) $sql .= $this->whereHelper($where, $parameters);
		$result = $this->execute($sql, $parameters);
		return $this->affectedRows($result);
	}

	/*
	 * This is intended to be the method used for large result sets.
	 * It is intended to return an iterator, and act upon buffered data.
	 * */
	public function fetch($sql, $parameters = array()) {
		$result = $this->execute($sql, $parameters);
		return $this->_fetch($result);
	}

	/*
	 * Fetches all of the rows where each is an associative array.
	 * Tries to use unbuffered queries to cut down on execution time and memory usage,
	 * but you'll only see a benefit with extremely large result sets.
	 * */
	public function fetchAll($sql, $parameters = array()) {
		$result = $this->execute($sql, $parameters, false);
		if($result)
			return $this->_fetchAll($result);
		return array();
	}
	// Sometimes I get confused. This is explicit ... "give me the ROWS"
	public function fetchRows($sql, $parameters = array()) {
		return $this->fetchAll($sql, $parameters);
	}

	/*
	 * Fetches the first cell from the first row returned by the query
	 * */
	public function fetchCell($sql, $parameters = array()) {
		$result = $this->execute($sql, $parameters);
		if($result) {
			$row = $this->_fetchRow($result);
			if (!$row) return null;
			return array_shift($row); // shift first field off first row
		}
		return null;
	}

	/*
	 * This method is quite different from fetchCell(), actually
	 * It fetches one cell from each row and places all the values in 1 array
	 * */
	public function fetchColumn($sql, $parameters = array()) {
		$result = $this->execute($sql, $parameters);
		if($result) {
			$cells = array();
			foreach($this->_fetchAll($result) as $row) {
				$cells[] = array_shift($row);
			}
			return $cells;
		} else {
			return array();
		}
	}

	/*
	 * Should be passed a query that fetches two fields
	 * The first will become the array key
	 * The second the key's value
	 */
	public function fetchKeyValue($sql, $parameters = array()) {
		$result = $this->execute($sql, $parameters);
		if(!$result) return array();

		$data = array();
		foreach($this->_fetchAll($result) as $row) {
			$key = array_shift($row);
			if(sizeof($row) == 1) { // if there were only 2 fields in the result
				// use the second for the value
				$data[ $key ] = array_shift($row);
			} else { // if more than 2 fields were fetched
				// use the array of the rest as the value
				$data[ $key ] = $row;
			}
		}
		return $data;
	}

	/*
	 * Like fetch(), accepts any number of arguments
	 * The first argument is an sprintf-ready query stringTypes
	 * */
	public function fetchRow($sql = null, $parameters = array()) {
		$result = $this->execute($sql, $parameters);
		// not all results look like resources, so I don't think is_resource($result) is portable
		if($result)
			return $this->_fetchRow($result);
		return null;
	}


	// These are defaults, since these statements are common across a few DBMSes
	// Override in driver class if they are incorrect
	public function beginTransaction() {
		// need to return true or false
		$this->_query('begin');
	}

	public function commitTransaction() {
		$this->_query('commit');
	}

	public function rollbackTransaction() {
		$this->_query('rollback');
	}

	// Fill in question mark and pound (#) placeholders. No more named placeholders.
	protected function makeQuery($sql, $parameters) {
		// bypass extra logic if we have no parameters
		if(sizeof($parameters) == 0) {
			return $sql;
		}

		$parts = explode('?', $sql);
		$query = '';
		while(sizeof($parameters)) {
			$part = array_shift($parts);
			// now placeholders for parameters that are to be inserted as-is
			$asis = explode('#', $part);
			$query .= array_shift($asis);
			while (sizeof($asis)) {
				//$query .= array_shift($asis) . array_shift($parameters);
				$query .= array_shift($parameters) . array_shift($asis);
			}

			if ($parameters) $query .= "'" . $this->escapeString( array_shift($parameters) ) . "'";
		}
		$query .= array_shift($parts);
		return $query;
	}

	public function quoteField($field) {
            return '`' . $field . '`';
	}

	public function quoteFields($fields) {
		return array_map(array($this, 'quoteField'), $fields);
	}

	public function quoteEscapeString($value) {
		return "'" . $this->escapeString($value) . "'";
	}

	// Prepare a hash to be used as SET or in a WHERE clause
	protected function prepareHash($data) {
		$out = array();
		foreach($data as $key => $value) {
			$out[] = $this->quoteField($key) . '=' . $this->quoteEscapeString($value); 
		}
		return $out;
	}

	protected function whereHelper($where, $parameters) {
		// make sure it's a string
		$sql = ' WHERE ';
		if(is_array($where)) {
			$sql .= implode(' AND ', $this->prepareHash($where));
			
		} elseif(is_string($where)) {
			$sql .= $where;
		}
		return $sql;
	}
}

