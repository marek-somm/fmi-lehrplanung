<?php
require('../src/session.php');

header('Access-Control-Allow-Origin: *');

$answer = array();
if(session::isAlive()) {
	$answer = array("success" => true);
} else {
	$answer = array("success" => false);
}
header('Content-Type: application/json');
echo (json_encode($answer, true));