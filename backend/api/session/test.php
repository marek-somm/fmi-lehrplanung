<?php
require('../src/session.php');

header('Access-Control-Allow-Origin: *');

$answer = array("success" => session::getVar("level") > 0, "level" => session::getVar("level"), "uid" => session::getVar("uid"));

header('Content-Type: application/json');
echo (json_encode($answer, true));