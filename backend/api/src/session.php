<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class session {
	public static function isAlive() {
		return session::getSecurityLevel() > 0;
	}

	public static function getSecurityLevel() {
		session_start();
		if(isset($_SESSION['prfa'])) {
			return 2;
		}
		if(isset($_SESSION['lehre'])) {
			return 1;
		}
		return 0;
	}

	public static function login($roll) {
		session::logout();
		session_start();
		$_SESSION[$roll] = 1;
	}

	public static function logout() {
		session_start ();
		session_destroy();
		//header("location:/");
	}
}
