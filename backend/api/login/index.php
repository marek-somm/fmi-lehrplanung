<?php

class input {
	public static function get($key, $value = false) {
		return (!empty($_GET[$key])) ? $_GET[$key] : $value;
	}

	public static function post($key, $value = false) {
		return (!empty($_POST[$key])) ? $_POST[$key] : $value;
	}

	public static function cookie($key, $value = false) {
		return (!empty($_COOKIE[$key])) ? $_COOKIE[$key] : $value;
	}
}

$user = input::get('user', false);
$pwd = input::get('pwd', false);

$answer = array();
if($user == "test" && $pwd == "test") {
	$answer = array("success" => true);
} else {
	$answer = array("success" => false);
}
header('Content-Type: application/json');
echo(json_encode($answer, true));