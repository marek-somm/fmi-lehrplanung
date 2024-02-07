<?php

namespace App\Helpers;

class General {
	static int $current_semester = 20221;
	
	static function get_current_semester() {
		return General::$current_semester;
	}
}
