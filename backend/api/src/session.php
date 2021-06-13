<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class session {
	public static function isAlive() {
		session_start();
		return isset($_SESSION["login"]);
	}

	public static function login() {
		session_start();
		$_SESSION["login"] = 1;
	}

	public static function logout() {
		session_start ();
		session_destroy();
		//header("location:/");
	}
}
