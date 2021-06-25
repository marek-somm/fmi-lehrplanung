<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class log {
	public static function database($reason, $call, $str) {
		log::writeDatabaseLog("$call> $reason: \t" . $str);
	}

	public static function start($call, $str) {
		log::write("$call> START: \t" . $str);
	}

	public static function info($call, $str) {
		log::write("$call> INFO: \t" . $str);
	}

	public static function error($call, $str) {
		log::write("$call> ERROR: \t" . $str);
	}

	public static function warn($call, $str) {
		log::write("$call> WARN: \t" . $str);
	}

	private static function write($str) {
		$fp = fopen("/var/www/html/api/src/b279a2cb6ccb5264a78c7e4c31044f44.log", "a");
		fwrite($fp, log::time() . " | " . log::ip() . "> " . $str . "\n");
		fclose($fp);
	}

	private static function writeDatabaseLog($str) {
		$fp = fopen("/var/www/html/api/src/5f25ba0f7c01dba542f1661467a7ec63.log", "a");
		fwrite($fp, log::time() . " | " . log::ip() . "> " . $str . "\n");
		fclose($fp);
	}

	public static function time() {
		return date('d-m-Y H:i:s');
	}

	public static function ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
