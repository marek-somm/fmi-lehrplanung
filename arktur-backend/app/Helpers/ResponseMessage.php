<?php

namespace App\Helpers;

class ResponseMessage {
	static array $wrong_credentials = ['Anmeldedaten' => ['Benutzername und/oder Passwort sind nicht korrekt.']];
	static array $insufficient_permissions = ['Berechtigung' => ['Sie besitzen nicht über ausreichende Rechte um auf diese Resource zuzugreifen. Bei Fragen kontaktieren Sie bitte den Seiten-Administrator.']];

	static array $required_data = [
		'uid' => 'Benutzername ist erforderlich.',
		'password' => 'Passwort ist erforderlich.'
	];
}
