<?php
require('../src/input.php');
require('../src/database.php');
require('../src/session.php');
require('veranstaltung.php');
require('modul.php');

header('Access-Control-Allow-Origin: *');

if(!session::isAlive()) {
    header('Content-Type: application/json');
    log::database("WARN", "SEARCH", "Invalid Session!");
	echo (json_encode(array("error" => "Invalid Session"), true));
    die();
}

function formatString($str) {
    $str = strtolower($str);
    $str = str_replace("-", "", $str);
    return $str;
}

$typ = input::get('typ', NULL);
$vnr = input::get('vnr', Null);
$semester = input::get('semester', Null);
$titel = input::get('titel', Null);
$limit = input::get('limit', 20);
$modulcode = input::get('modulcode', Null);

if ($typ == 'v') {
    if($vnr && $semester) {
        veranstaltung::search($vnr, $semester);
    } else {
        veranstaltung::searchAll($titel, $limit);
    }
} else if ($typ == 'm') {
    if($modulcode) {
        modul::search($modulcode);
    } else {
        modul::searchAll($titel, $limit);
    }
} else {
    header("Location: /");
    die();
}