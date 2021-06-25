<?php
require('../src/input.php');
require('ldap.php');

header('Access-Control-Allow-Origin: *');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$user = input::get('user', false);
$pwd = input::get('pwd', false);

$answer = array();
if (ldap::login($user, $pwd)) {
	$answer = array("success" => session::getVar("level") > 0, "level" => session::getVar("level"), "uid" => session::getVar("uid"));
} else {
	$answer = array("success" => false, "level" => 0);
}

header('Content-Type: application/json');
echo (json_encode($answer, true));
