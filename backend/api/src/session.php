<?php
require("../src/log.php");

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class session {
	public static function isAlive() {
		session::start();
		return session::getVar("level") > 0;
	}

	public static function setVar($var, $val) {
		session::start();
		log::info("SESSION>setVar", "var:$var, val:$val");
		$_SESSION[$var] = $val;
	}

	public static function getVar($var) {
		session::start();
		return $_SESSION[$var];
	}

	public static function login($roll) {
		log::error("SESSION", "Empty Login function");
	}

	public static function logout() {
		log::info("SESSION>logout", session::getVar("uid"));
		session::start();
		session_destroy();
		//header("location:/");
	}

	private static function start() {
		if(session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
	}
}
