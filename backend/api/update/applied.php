<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

function applied() {
	$vnr = input::get('vnr', NULL);
	$sem = input::get('sem', NULL);
	$modulcode = input::get('modulcode', NULL);
	$pnr = input::get('pnr', NULL);

	$answer = array("answer" => -1, "message" => "Unhandled Error");

	if(!$vnr || !$sem || !$modulcode || !$pnr) {
		return array("answer" => -1, "message" => "Empty Input");
	}

	try {
		$db = connectDatabase();

		$db->execute(<<<EOF
			UPDATE Pruefung
			SET friedolinAdded=1
			WHERE EXISTS (
				SELECT 1
				FROM Lehrveranstaltung_Info li
				WHERE lehrvID=li.lehrvID AND modulcode='$modulcode' AND pnr=$pnr AND li.veranstaltungsnummer=$vnr AND li.semester=$sem
			)
		EOF);
		$answer = array("status" => 0, "message" => "successfully saved applied status $modulcode $pnr $vnr $sem");
		log::database("INFO", "UPDATE>applied", "successfully saved applied status");
		
		$db->close();
	} catch (Exception) {
		log::database("ERROR", "UPDATE>applied", "applied status unhandled Error");
	}

	return $answer;
}