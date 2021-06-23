<?php
require('../src/input.php');
require('../src/session.php');
require('ldap.php');

header('Access-Control-Allow-Origin: *');

$user = input::get('user', false);
$pwd = input::get('pwd', false);

$answer = array();
if($status = ldap::checkAccess($user, $pwd)) {
	session::login("lehre");
	$answer = array("success" => session::getSecurityLevel() == 1, "level" => session::getSecurityLevel());
} else {
	$answer = array("success" => false, "level" => 0);
}

header('Content-Type: application/json');
echo (json_encode($answer, true));
