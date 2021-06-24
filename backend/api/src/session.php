<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class session {
	public static function isAlive() {
		return session::getVar("level") > 0;
	}

	public static function setVar($var, $val) {
		session_start();
		$_SESSION[$var] = $val;
	}

	public static function getVar($var) {
		session_start();
		return $_SESSION[$var];
	}

	public static function login($roll) {
		session::logout();
		session_start();
			$_SESSION["level"] = 0;
		if($roll == "lehre") {
			$_SESSION["level"] = 1;
		}
		if($roll == "prfa") {
			$_SESSION["level"] = 2;
		}
	}

	public static function logout() {
		session_start ();
		session_destroy();
		//header("location:/");
	}
}
