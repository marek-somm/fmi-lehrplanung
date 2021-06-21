<?php
require('../src/session.php');

header('Access-Control-Allow-Origin: *');

$answer = array("success" => session::getSecurityLevel() > 0, "level" => session::getSecurityLevel());

header('Content-Type: application/json');
echo (json_encode($answer, true));