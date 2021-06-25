<?php
require('../src/session.php');

if(basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

function in_dict($str, $dict) {
	foreach($dict as &$item) {
		if($item == $str) {
			return true;
		}
	}
	return false;
}

class ldap {
	public static function login($user, $pwd) {
		log::start("LDAP>login", "LOGIN ATTEMPT $user");
		if ($conn = ldap::connect()) {
			log::info("LDAP>LOGIN", "Connection to Server (ldaps://ldap1.uni-jena.de:636) established");
			$dn = "uid=$user,ou=users,dc=uni-jena, dc=de";
			if(ldap_bind($conn, $dn, $pwd)) {
				log::info("LDAP>login", "Authentication successfull");
				$data = ldap::getData($conn, $dn);
				session::setVar("level", 0);
				if(in_dict("Student", $data["edupersonaffiliation"])) {
					log::info("LDAP>login", "Asign Roll - Student [-1]");
					session::setVar("level", -1);
				} elseif(in_dict("Mitarbeiter", $data["edupersonaffiliation"])) {
					if(in_dict("Studien-/Prüf.amt", $data["ou"])) {
						log::info("LDAP>login", "Asign Roll - Prüfungsamt [2]");
						session::setVar("level", 2);
					} else {
						log::info("LDAP>login", "Asign Roll - Lehrperson [1]");
						session::setVar("level", 1);
					}
				}
				if(in_dict("go74dir", $data["uid"]) || in_dict("lo83gag", $data["uid"])) {
					log::info("LDAP>login", "Asign Roll - Admin [3]");
					session::setVar("level", 3);
				}
				return true;
			} else {
				log::info("LDAP>login", "Authentication failed");
				return false;
			}
			ldap_close($conn);
		} else {
			log::error("LDAP>login", "Connection to Server (ldaps://ldap1.uni-jena.de:636) failed");
		}
	}

	private static function getData($conn, $dn) {
		$filter = "(objectclass=*)";
		$result = ldap_read($conn, $dn, $filter);
		$entries = ldap_get_entries($conn, $result);
		$json = json_encode($entries, JSON_UNESCAPED_UNICODE);

		$uid = $entries["0"]["uid"]["0"];

		file_put_contents("data/".$uid.".json", $json);
		session::setVar("uid", $uid);

		return $entries["0"];
	}

	private static function connect() {
		$conn = ldap_connect("ldaps://ldap1.uni-jena.de:636");
		ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		return $conn;
	}
}