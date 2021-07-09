<?php
require('../src/session.php');

log::info("TEST", "Querry results - success:" . (session::getVar("level") > 0) . ", level:" . session::getVar("level") . ", uid:" . session::getVar("uid"));
$answer = array("success" => session::isAlive(), "level" => session::getVar("level"), "uid" => session::getVar("uid"));

header('Content-Type: application/json');
echo (json_encode($answer, true));
