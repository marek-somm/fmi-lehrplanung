<?php

class modul {
	public static function searchAll($titel, $limit) {
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

		header('Content-Type: application/json');
		echo (json_encode($answer, true));

		$db->close();
	}

	public static function search($modulcode) {
		$db = connectDatabase();
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

		$answer['modulcode'] = $modulcode;

		header('Content-Type: application/json');
		echo (json_encode($answer, true));

		$db->close();
	}
}
