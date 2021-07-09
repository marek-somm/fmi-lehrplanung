<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

function create() {
	$in = input::get('data', NULL);
	$data = json_decode($in, true);
	$answer = array("answer" => -1, "message" => "Empty Input");

	if(!$in) {
		return $answer;
	}

	$vnr = $data['data']['veranstaltungsnummer'];
	$sem = $data['data']['semester'];

	$answer = array("answer" => -1, "message" => "Unhandled Error");

	if (veranstaltungExists($vnr, $sem)) {
		$answer = array("status" => 1, "message" => "Entry already exists");
		log::database("ERROR", "UPDATE>new", "vnr:$vnr, sem:$sem already exists");
	} else {
		try {
			createVeranstaltung($data);
			$answer = array("status" => 0, "message" => "Entry successfully created");
			log::database("INFO", "UPDATE>new", "vnr:$vnr, sem:$sem successfully created");
		} catch (Exception) {
			log::database("ERROR", "UPDATE>new", "vnr:$vnr, sem:$sem unhandled Error");
		}
	}

	return $answer;
}

function veranstaltungExists($vnr, $sem) {
	$db = connectDatabase();

	$count = $db->fetchFirst(<<<EOF
		SELECT count(*) as count
		FROM Lehrveranstaltung_Info
		WHERE veranstaltungsnummer=$vnr AND semester=$sem
	EOF)['count'];

	$db->close();

	return $count != 0;
}

function personExists($name, $surename) {
	$db = connectDatabase();

	$count = $db->fetchFirst(<<<EOF
		SELECT count(*) as count
		FROM Person
		WHERE vorname='$name' AND nachname='$surename'
	EOF)['count'];

	$db->close();

	return (int)$count != 0;
}

function createPerson($name, $surename, $degree, $fid) {
	$name = $name ? "'$name'" : 'NULL';
	$surename = $surename ? "'$surename'" : 'NULL';
	$degree = $degree ? "'$degree'" : 'NULL';
	$fid = $fid ? $fid : 'NULL';


	$db = connectDatabase();

	$db->execute(<<<EOF
		INSERT INTO Person (friedolinID, vorname, nachname, grad)
		VALUES ($fid, $name, $surename, $degree)
	EOF);

	$db->close();
}

function createBridgeLvPe($vid, $pid) {
	$db = connectDatabase();

	$db->execute(<<<EOF
		INSERT INTO BRIDGE_Lehrveranstaltung_Person
		VALUES ($vid, $pid)
	EOF);

	$db->close();
}

function examExists($vnr, $modulcode, $descritption) {
	$modulcode = $modulcode ? "'$modulcode'" : 'NULL';
	$descritption = $descritption ? "'$descritption'" : 'NULL';

	$db = connectDatabase();

	$count = $db->fetchFirst(<<<EOF
		SELECT count(*) as count
		FROM Pruefung
		WHERE venr=$vnr AND modulcode=$modulcode AND beschreibung=$descritption 
	EOF)['count'];

	$db->close();

	return (int)$count != 0;
}


function createVeranstaltung($data) {
	$titel = $data['data']['titel'];
	$vnr = $data['data']['veranstaltungsnummer'];
	$sem = $data['data']['semester'];
	$fid = $data['data']['friedolinID'] != null ? $data['data']['friedolinID'] : "NULL";
	$aktiv = $data['data']['aktiv'];
	$sws = $data['data']['sws'];
	$turnus = $data['data']['turnus'];
	$art = $data['data']['art'];

	$zielgruppe = $data['content']['Zielgruppe'] != null ? $data['content']['Zielgruppe'] : "NULL";
	$people = $data['people'][''];
	$exams = $data['exams'][''];

	$db = connectDatabase();

	$db->execute(<<<EOF
		INSERT INTO Lehrveranstaltung_Info (veranstaltungsnummer, semester, titel, friedolinID, aktiv, sws, art)
		VALUES ($vnr, $sem, '$titel', $fid, $aktiv, $sws, '$art')
	EOF);

	$vid = $db->fetchFirst(<<<EOF
		SELECT lehrvID 
		FROM Lehrveranstaltung_Info
		WHERE veranstaltungsnummer=$vnr AND semester=$sem
	EOF)['lehrvID'];

	$db->execute(<<<EOF
		INSERT INTO Lehrveranstaltung_Inhalt (lehrvID, zielgruppe)
		VALUES ($vid, $zielgruppe)
	EOF);

	foreach ($people as $person) {
		$fid = $person['friedolinID'];
		$name = $person['vorname'];
		$surename = $person['nachname'];
		$degree = $person['grad'];

		if ($name && $surename) {
			if (!personExists($name, $surename)) {
				createPerson($name, $surename, $degree, $fid);
			}

			$pid = $db->fetchFirst(<<<EOF
				SELECT personenID
				FROM Person
				WHERE vorname='$name' AND nachname='$surename'
			EOF)['personenID'];

			createBridgeLvPe($vid, $pid);
		}
	}


	foreach ($exams as $exam) {
		$pnr = $exam['pnr'];
		$modulcode = $exam['Modulcode'];
		$descritption = array_key_exists('Beschreibung', $exam) ? $exam['Beschreibung'] : Null;
		$title = $exam['titel'];

		if ($modulcode || $descritption) {

			$pnr = $pnr ? $pnr : 'NULL';
			$modulcode = $modulcode ? "'$modulcode'" : 'NULL';
			$descritption = $descritption ? "'$descritption'" : 'NULL';
			$title = $title ? "'$title'" : 'NULL';

			$db->execute(<<<EOF
				INSERT INTO Pruefung (lehrvID, pnr, modulcode, beschreibung, titel)
				VALUES ($vid, $pnr, $modulcode, $descritption, $title)
			EOF);
		}
	}

	$db->close();
}
