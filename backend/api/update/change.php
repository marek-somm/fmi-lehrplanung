<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

function change() {
	$vnr = input::get('vnr', NULL);
	$sem = input::get('semester', NULL);


	$answer = array("answer" => -1, "message" => "Unhandled Error");

	try {
		deleteVeranstaltung($vnr, $sem);
		create();
		$answer = array("status" => 0, "message" => "Entry successfully changed");
		log::database("INFO", "UPDATE>change", "vnr:$vnr, sem:$sem successfully changed");
	} catch (Exception) {
		log::database("ERROR", "UPDATE>change", "vnr:$vnr, sem:$sem unhandled Error");
	}

	return $answer;
}

function deleteVeranstaltung($vnr, $sem) {
	$db = connectDatabase();

	$vid = $db->fetchFirst(<<<EOF
		SELECT *
		FROM Lehrveranstaltung_Info
		WHERE veranstaltungsnummer=$vnr AND semester=$sem
	EOF)['lehrvID'];

	$db->execute(<<<EOF
		DELETE FROM Lehrveranstaltung_Info
		WHERE lehrvID=$vid
	EOF);

	$db->execute(<<<EOF
		DELETE FROM Lehrveranstaltung_Inhalt
		WHERE lehrvID=$vid
	EOF);

	$db->execute(<<<EOF
		DELETE FROM Pruefung
		WHERE lehrvID=$vid
	EOF);

	$db->execute(<<<EOF
		DELETE FROM BRIDGE_Lehrveranstaltung_Person
		WHERE lehrvID=$vid
	EOF);

	$db->close();
}
