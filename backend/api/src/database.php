<?php

class Database extends SQLite3 {
    function __construct() {
        $this->open('/var/www/html/api/src/dc5a2a51976a32643a33ef6746dbf45a.db');
    }

    function fetchData($sql) {
        $ret = $this->query($sql);
        return $ret;
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