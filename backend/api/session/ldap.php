<?php
require('../src/session.php');

if(basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class ldap {
	public static function login($user, $pwd) {
		if ($conn = ldap::connect()) {
			$dn = "uid=$user,ou=users,dc=uni-jena, dc=de";
			if(ldap_bind($conn, $dn, $pwd)) {
				session::login("lehre");
				$status = ldap::getData($conn, $dn);
				return $status;
			} else {
				return false;
			}
			ldap_close($conn);
		}
	}

	private static function getData($conn, $dn) {
		$filter = "(objectclass=*)";
		$result = ldap_read($conn, $dn, $filter);
		$entries = ldap_get_entries($conn, $result);
		$json = json_encode($entries, true);

		$uid = $entries["0"]["uid"]["0"];

		file_put_contents("data/".$uid.".json", $json);
		session::setVar("uid", $uid);
		return end($entries["0"]["edupersonaffiliation"]);
	}

	private static function connect() {
		$conn = ldap_connect("ldaps://ldap1.uni-jena.de:636");
		ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		return $conn;
	}
}