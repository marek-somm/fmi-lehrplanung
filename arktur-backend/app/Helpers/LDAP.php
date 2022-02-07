<?php

namespace App\Helpers;

use Exception;

class LDAP {
	private $conn, $config, $dn;

	public function __construct() {
		$this->config = [
			'host' => env('LDAP_HOST', 'ldaps://ldap1.uni-jena.de'),
			'base_dn' => env('LDAP_BASE_DN', 'ou=users,dc=uni-jena,dc=de'),
			'account_suffix' => env('LDAP_ACCOUNT_SUFFIX', ''),
			'port' => env('LDAP_PORT', 636)
		];
	}

	public function connect() {
		$this->conn = ldap_connect($this->config['host'], $this->config['port']);
	}

	public function auth($uname, $pwd) {
		$bindDn = "uid=$uname," . $this->config['base_dn'];
		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		try{
			ldap_bind($this->conn, $bindDn, $pwd);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}

	public function directory_entry($username) {
		$filter = "(objectclass=*)";
		$columns = ['uid', 'mail', 'fsufirstname', 'fsucompletesurname', 'thuedusalutation', 'displayname', 'edupersonaffiliation'];
		$result = ldap_read($this->conn, "uid=$username," . $this->config['base_dn'], $filter);
		$data = [];
		if ($result) {
			$entries = ldap_get_entries($this->conn, $result);
			if ($entries['count'] > 0) {
				for ($i = 0; $i < $entries['count']; $i++) {
					foreach ($columns as $col) {
						if(array_key_exists($col, $entries[$i])) {
							if($entries[$i][$col]['count'] == 1) {
								$data[$col] = $entries[$i][$col];
							} else {
								$data[$col] = array();
								for($i=0; $i<$entries[$i][$col]['count']; $i++) {
									$data[$col][] = $entries[$i][$col];
								}
							}
						}						
					}
				}
			}
		}
		return $data;
	}

	public function save_data($username) {
		    //$result = ldap_read($conn, $dn, $filter);
            //$entries = ldap_get_entries($conn, $result);
		    //$json = json_encode($entries, JSON_UNESCAPED_UNICODE);
            //file_put_contents("data/".$uid.".json", $json);
		$filter = "(objectclass=*)";
		$result = ldap_read($this->conn, "uid=$username," . $this->config['base_dn'], $filter);
		if ($result) {
			$entries = ldap_get_entries($this->conn, $result);
			$json = json_encode($entries, JSON_UNESCAPED_UNICODE);

			file_put_contents("data/".$username.".json", $json);
		}
	}

	public function close() {
		ldap_close($this->conn);
	}
}
