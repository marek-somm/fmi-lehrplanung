<?php
require('../src/input.php');
require('../src/database.php');
require('../src/session.php');
require('new.php');
require('change.php');
require('applied.php');

//header('Access-Control-Allow-Origin: *');     // only for localhost debugging

if (!session::isAlive()) {
	header('Content-Type: application/json');
	log::database("WARN", "UPDATE", "Invalid Session!");
	echo (json_encode(array("error" => "Invalid Session"), true));
	die();
}

$typ = input::get('typ', NULL);

if ($typ == 'n') {
	$answer = create();
} else if ($typ == 'c') {
	$answer = change();
} else if ($typ == 'a') {
	$answer = applied();
} else {
	header("Location: /");
	die();
}

header('Content-Type: application/json');
echo (json_encode($answer));
