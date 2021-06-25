<?php

if(basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class modul {
	public static function searchAll($titel, $limit) {
		log::database("INFO", "SEARCH>MODUL>searchAll", "titel:$titel, limit:$limit");
		$db = connectDatabase();
		$all = array();
		$answer = array();

		$ret = $db->fetchData(<<<EOF
			SELECT titel_de titel, modulcode nr, 1 aktiv
			FROM modul
			WHERE titel_de LIKE '%$titel%' OR modulcode LIKE '%$titel%'
			LIMIT $limit
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			array_push($all, $row);
		}

		$answer['data'][''] = $all;
		$answer['count'] = sizeof($all);

		log::database("INFO", "SEARCH>MODUL>searchAll", "Found " . $answer['count'] . " Results");
		header('Content-Type: application/json');
		echo (json_encode($answer, true));

		$db->close();
	}

	public static function search($modulcode) {
		log::database("INFO", "SEARCH>MODUL>search", "modulcode:$modulcode");
		$db = connectDatabase();
		$people = array();
		$allSemester = array();
		$exams = array();
		$answer = array();

		$ret = $db->fetchData(<<<EOF
			SELECT praesenzzeit, workload, lp, t.name turnus, titel_de, titel_en, zusammensetzung
			FROM modul m
			JOIN modul_turnus t ON m.turnusID=t.turnusID
			WHERE modulcode="$modulcode"
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			$answer['data'] = $row;
		}

		$ret = $db->fetchData(<<<EOF
			SELECT art Art, inhalte Inhalte, vorkentnisse Vorkentnisse, vor_lp "Vorraussetzungen Leistungspunkte", vor_pruefungen "Vorraussetzungen Prüfungen", vor_zulassung "Vorraussetzungen Zulassung", literatur Literatur, zusatzinfos Zusatzinfos
			FROM modul m
			JOIN modul_turnus t ON m.turnusID=t.turnusID
			WHERE modulcode="$modulcode"
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			$answer['content'] = $row;
		}

		$ret = $db->fetchData(<<<EOF
			SELECT p.vorname, p.nachname, p.grad, p.friedolinID
			FROM modul m
			JOIN BRIDGE_Modul_Person bmp ON bmp.modulcode=m.modulcode
			JOIN Person p ON bmp.personenID=p.personenID
			WHERE m.modulcode="$modulcode"
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			array_push($people, $row);
		}



		$ret = $db->fetchData(<<<EOF
			SELECT li.semester
			FROM modul m
			JOIN Pruefung p ON m.modulcode=p.modulcode
			JOIN BRIDGE_Lehrveranstaltung_Pruefung blp ON p.venr=blp.venr
			JOIN Lehrveranstaltung_Info li ON blp.lehrvID=li.lehrvID
			WHERE m.modulcode="$modulcode"
			GROUP BY li.semester
			ORDER BY li.semester DESC
		EOF);

		while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
			array_push($allSemester, $row['semester']);
		}

		foreach ($allSemester as &$sem) {
			if ($sem % 10 == 0) {
				$semStr = "SoSe " . (int)($sem / 10);
			} else {
				$semStr = "WiSe " . (int)($sem / 10);
			}
			$answer["exams"]["$semStr"] = array();
			$ret = $db->fetchData(<<<EOF
				SELECT p.titel, p.pnr, li.veranstaltungsnummer Vnr, li.semester
				FROM modul m
				JOIN Pruefung p ON m.modulcode=p.modulcode
				JOIN BRIDGE_Lehrveranstaltung_Pruefung blp ON p.venr=blp.venr
				JOIN Lehrveranstaltung_Info li ON blp.lehrvID=li.lehrvID
				WHERE m.modulcode="$modulcode" AND li.semester=$sem
			EOF);
			while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
				array_push($answer["exams"]["$semStr"], $row);
			}
		}

		if(!empty($people)) {
			$answer['people'][''] = $people;
		}

		if(!empty($answer)) {
			$answer['modulcode'] = $modulcode;
		}

		log::database("INFO", "SEARCH>MODUL>search", "Found " . !empty($answer) . " Results");
		header('Content-Type: application/json');
		echo (json_encode($answer, true));

		$db->close();
	}
}