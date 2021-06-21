<?php
require('../src/input.php');
require('../src/session.php');

header('Access-Control-Allow-Origin: *');

$user = input::get('user', false);
$pwd = input::get('pwd', false);

$answer = array();
if ($user == "prüfungsamt" && $pwd == "1234") {
	session::login("prfa");
	$answer = array("success" => session::getSecurityLevel() == 2, "level" => session::getSecurityLevel());
} else if ($user == "lehre" && $pwd == "1234") {
	session::login("lehre");
	$answer = array("success" => session::getSecurityLevel() == 1, "level" => session::getSecurityLevel());
} else {
	$answer = array("success" => false, "level" => session::getSecurityLevel());
}

header('Content-Type: application/json');
echo (json_encode($answer, true));
