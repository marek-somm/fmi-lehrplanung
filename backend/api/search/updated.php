<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	header("Location: /");
	die();
}

class updated {
	public static function search() {
		$db = connectDatabase();
		$allSemester = array();
		$allModules = array();
		$answer = array();

		$retSem = $db->fetchData(<<<EOF
			SELECT semester
			FROM Lehrveranstaltung_Info li
			JOIN Pruefung p ON li.lehrvID=p.lehrvID
			WHERE friedolinAdded = 0
			GROUP BY semester
			ORDER BY semester DESC
		EOF);

		while ($row = $retSem->fetchArray(SQLITE3_ASSOC)) {
			array_push($allSemester, $row['semester']);
		}

		$retPruef = $db->fetchData(<<<EOF
			SELECT p.modulcode, p.pnr
			FROM Pruefung p
			WHERE p.friedolinAdded = 0
		EOF);

		while ($row = $retPruef->fetchArray(SQLITE3_ASSOC)) {
			if (!in_array($row, $allModules))
				array_push($allModules, $row);
		}

		foreach ($allSemester as &$sem) {
			if ($sem % 10 == 0) {
				$semStr = "SoSe " . (int)($sem / 10);
			} else {
				$semStr = "WiSe " . (int)($sem / 10);
			}
			$answer["data"]["$semStr"] = array();

			foreach ($allModules as &$modul) {
				$modulcode = $modul["modulcode"];
				$pnr = $modul["pnr"];
				$retData = $db->fetchData(<<<EOF
					SELECT li.lehrvID, li.veranstaltungsnummer, li.titel, li.sws, inh.zielgruppe, li.art
					FROM lehrveranstaltung_info li
					JOIN Pruefung p ON li.lehrvID=p.lehrvID
					JOIN Lehrveranstaltung_Inhalt inh ON li.lehrvID=inh.lehrvID
					WHERE p.friedolinAdded=0 AND semester=$sem AND p.modulcode='$modulcode'
				EOF);

				$veranstaltungen = array();

				$persons = array();
				
				$retPers = $db->fetchData(<<<EOF
					SELECT p.vorname, p.nachname, p.friedolinID
					FROM Person p
					JOIN BRIDGE_Modul_Person bmp ON p.personenID=bmp.personenID
					WHERE bmp.modulcode='$modulcode'
				EOF);

				while ($row = $retPers->fetchArray(SQLITE3_ASSOC)) {
					$person = array();
					$person["vorname"] = $row["vorname"];
					$person["nachname"] = $row["nachname"];
					$person["friedolinID"] = $row["friedolinID"];
					array_push($persons, $person);
				}

				while ($row = $retData->fetchArray(SQLITE3_ASSOC)) {
					$toadd = $row;
					$toadd["uebertragen"] = true;
					$toadd["lehrpersonal"] = array();
					$toadd["lehrpersonal"] = $persons;
					if (!in_array($toadd, $veranstaltungen)) {
						array_push($veranstaltungen, $toadd);
					}
				}

				$output = array();
				$output["Modulcode"] = $modulcode;
				$output["pnr"] = $pnr;
				$output["veranstaltungen"] = array();
				$output["veranstaltungen"] = $veranstaltungen;
				array_push($answer["data"]["$semStr"], $output);
			}
		}

		header('Content-Type: application/json');
		echo (json_encode($answer));

		$db->close();
	}
}
