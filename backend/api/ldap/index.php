<?php
require('../src/input.php');

$user = input::get('user', NULL);
$pwd = input::get('pwd', NULL);

$conn = ldap_connect("ldaps://ldap1.uni-jena.de:636");

ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($conn) {
	// bind 

	$dn = "uid=$user,ou=users,dc=uni-jena, dc=de";
	$filter = "(objectclass=*)";

	if(ldap_bind($conn, $dn, $pwd)) {
		//echo("true");
		$result = ldap_read($conn, $dn, $filter);
		$entries = ldap_get_entries($conn, $result);

		header('Content-Type: application/json');
		echo(json_encode($entries, true));
	} else {
		echo("Invalid Credentials!");
	}

	if (false) {

		// prepare data 
		$dn = "cn=go74dir,ou=users,dc=uni-jena, dc=de";
		$value = "4g8R54536QcPCrhjgFr6";
		$attr = "password";

		// compare value
		//$r = ldap_list($ds, $dn, "ou=*", $justthese);;
		//$r = ldap_compare($ds, $dn, $attr, $value);

		$r = ldap_compare($ds, $dn, $attr, $value);

		echo("dn: " . $dn . "<br>");

		echo("Results: " . $r . "<br>");

		if ($r === -1) {
			echo "Error: " . ldap_error($ds) . " (" . ldap_errno($ds) . ")";
		} elseif ($r === TRUE) {
			echo "Password correct.";
		} elseif ($r === FALSE) {
			echo "Request returned FALSE";
		}
	} else {
		//echo "Unable to bind to LDAP server.";
	}

	ldap_close($conn);
} else {
	echo "Unable to connect to LDAP server.";
}