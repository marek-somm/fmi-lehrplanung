<?php
require('../src/input.php');
require('../src/session.php');

header('Access-Control-Allow-Origin: *');

$user = input::get('user', false);
$pwd = input::get('pwd', false);

$answer = array();
if ($user == "admin" && $pwd == "1234") {
	$answer = array("success" => true);
	session::login();
} else {
	$answer = array("success" => false);
}

header('Content-Type: application/json');
echo (json_encode($answer, true));
