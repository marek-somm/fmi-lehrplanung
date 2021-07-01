<?php
require('../src/input.php');
require('../src/database.php');
require('../src/session.php');
require('new.php');
require('change.php');

header('Access-Control-Allow-Origin: *');

if (!session::isAlive()) {
	header('Content-Type: application/json');
	log::database("WARN", "UPDATE", "Invalid Session!");
	echo (json_encode(array("error" => "Invalid Session"), true));
	die();
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$typ = input::get('typ', NULL);

if ($typ == 'n') {
	$answer = create();
} else if ($typ == 'c') {
	$answer = change();
} else {
	header("Location: /");
	die();
}

header('Content-Type: application/json');
echo (json_encode($answer));
