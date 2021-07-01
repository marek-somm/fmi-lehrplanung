<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class Database extends SQLite3 {
	function __construct() {
		$this->open('/var/www/html/api/src/dc5a2a51976a32643a33ef6746dbf45a.db');
	}

	function fetchData($sql) {
		$ret = $this->query($sql);
		return $ret;
	}

	function fetchFirst($sql) {
		return $this->fetchData($sql)->fetchArray(SQLITE3_ASSOC);
	}

	function execute($sql) {
		try {
			$this->exec($sql);
		} catch (Exception $e) {
			log::database("ERROR", "DATABASE>execute", "$e $sql");
			throw $e;
		}
	}
}

function connectDatabase() {
	$db = new Database();
	if (!$db) {
		die($db->lastErrorMsg());
	} else {
		return $db;
	}
}
