<?php

namespace App\Helpers;

use Exception;

class LDAP {
	public $conn, $config, $dn;

	public function __construct() {
		$this->config = [
			'host' => env('LDAP_HOST', 'ldaps://ldap1.uni-jena.de'),
			'base_dn' => env('LDAP_BASE_DN', 'ou=users,dc=uni-jena,dc=de'),
			'account_suffix' => env('LDAP_ACCOUNT_SUFFIX', ''),
			'port' => env('LDAP_PORT', 636)
		];
	}
	public function auth($uname, $pwd) {
		$this->conn = ldap_connect($this->config['host'], $this->config['port']);
		$bindDn = "uid=$uname," . $this->config['base_dn'];
		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		try{
			ldap_bind($this->conn, $bindDn, $pwd);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}

	public function directoryEntry($username) {
		$filter = "(objectclass=*)";
		$columns = ['uid', 'mail', 'fsufirstname', 'fsucompletesurname', 'thuedusalutation', 'displayname'];
		$result = ldap_read($this->conn, "uid=$username," . $this->config['base_dn'], $filter);
		$data = [];
		if ($result) {
			$entries = ldap_get_entries($this->conn, $result);
			if ($entries['count'] > 0) {
				for ($i = 0; $i < $entries['count']; $i++) {
					foreach ($columns as $col) {
						$data[$col] = array_key_exists($col, $entries[$i]) ? $entries[$i][$col][0] : '';
					}
				}
			}
		}
		ldap_close($this->conn);
		return $data;
	}
}
