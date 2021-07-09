<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class veranstaltung {
	public static function searchAll($titel, $limit) {
		log::database("INFO", "SEARCH>VERANSTALTUNG>searchAll", "titel:$titel, limit:$limit");
		$db = connectDatabase();
		$allSemester = array();
		$answer = array();

		$ret = $db->fetchData(<<<EOF
			SELECT semester
			FROM Lehrveranstaltung_Info
			WHERE titel LIKE '%$titel%'
			GROUP BY semester
			ORDER BY semester DESC
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			array_push($allSemester, $row['semester']);
		}

		$i = $limit;
		foreach ($allSemester as &$sem) {
			if ($i != 0) {
				if ($sem % 10 == 0) {
					$semStr = "SoSe " . (int)($sem / 10);
				} else {
					$semStr = "WiSe " . (int)($sem / 10);
				}
				$answer["data"]["$semStr"] = array();
			}
			
			$ret = $db->fetchData(<<<EOF
				SELECT veranstaltungsnummer nr, titel, semester, aktiv
				FROM lehrveranstaltung_info
				WHERE titel LIKE '%$titel%' AND semester=$sem
				LIMIT $i
			EOF);
			while (($row = $ret->fetchArray(SQLITE3_ASSOC)) && $i != 0) {
				$i--;
				array_push($answer["data"]["$semStr"], $row);
			}
		}

		$answer['count'] = $limit - $i;
		
		log::database("INFO", "SEARCH>VERANSTALTUNG>searchAll", "Found " . $answer['count'] . " Results");
		header('Content-Type: application/json');
		echo (json_encode($answer, true));

		$db->close();
	}

	public static function search($vnr, $semester) {
		log::database("INFO", "SEARCH>VERANSTALTUNG>search", "vnr:$vnr, sem:$semester");
		$db = connectDatabase();
		$answer = array();
		$exams = array();
		$people = array();
		// $rolls = array('verantwortlich', 'begleitend', 'organisatorisch');

		$ret = $db->fetchData(<<<EOF
			SELECT inf.titel, inf.veranstaltungsnummer, semester, friedolinID, aktiv, sws, name turnus, art
			FROM Lehrveranstaltung_Info inf
			JOIN Lehrveranstaltung l ON inf.veranstaltungsnummer=l.veranstaltungsnummer 
			JOIN Lehrveranstaltung_Rhytmus r ON l.rhythmusID=r.rhythmusID
			WHERE inf.veranstaltungsnummer=$vnr AND inf.semester=$semester
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			$answer['data'] = $row;
		}

		$ret = $db->fetchData(<<<EOF
		SELECT zielgruppe Zielgruppe
			FROM Lehrveranstaltung_Info inf
			JOIN Lehrveranstaltung_Inhalt inh ON inf.lehrvID=inh.lehrvID
			WHERE inf.veranstaltungsnummer=$vnr AND inf.semester=$semester
		EOF);
		// SELECT kommentar Kommentar, literatur Literatur, bemerkung Bemerkung, zielgruppe Zielgruppe, lerninhalte Lerninhalte, leistungsnachweis Leistungsnachweis

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			$answer['content'] = $row;
		}

		//foreach ($rolls as &$roll) {

		$ret = $db->fetchData(<<<EOF
			SELECT p.vorname, p.nachname, p.grad, p.friedolinID
			FROM Lehrveranstaltung_Info i
			JOIN BRIDGE_Lehrveranstaltung_Person blp ON blp.lehrvID=i.lehrvID
			JOIN Person p ON blp.personenID=p.personenID
			WHERE i.veranstaltungsnummer=$vnr AND i.semester=$semester
		EOF);
		// AND blp.rolle='$roll'

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			array_push($people, $row);
		}

		// $roll = ucfirst($roll);
		// $answer["people"]["$roll"] = $people;
		if (!empty($people)) {
			$answer['people'][''] = $people;
		}

		$ret = $db->fetchData(<<<EOF
			SELECT pr.titel, pnr, modulcode Modulcode, beschreibung Beschreibung 
			FROM Lehrveranstaltung_Info li
			JOIN Pruefung pr ON li.lehrvID=pr.lehrvID
			WHERE li.veranstaltungsnummer=$vnr AND li.semester=$semester
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			array_push($exams, $row);
		}

		if (!empty($exams)) {
			$answer['exams'][''] = $exams;
		}

		log::database("INFO", "SEARCH>VERANSTALTUNG>search", "Found " . sizeof($answer) . " Results");
		header('Content-Type: application/json');
		echo (json_encode($answer, true));

		$db->close();
	}
}
