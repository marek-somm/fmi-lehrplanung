<?php
require('../src/input.php');
require('../src/database.php');
require('../src/session.php');

header('Access-Control-Allow-Origin: *');

if(!session::isAlive() && false) {
	header('Content-Type: application/json');
	echo (json_encode(array("data" => "Invalid Session"), true));
   die();
}

$in = input::get('data', NULL);

$db = connectDatabase();
$answer = array("answer" => -1, "message" => "Unhandled Error");
$data = json_decode($in, true);

if($data != Null) {
	$vnr = $data['data']['veranstaltungsnummer'];
	$sem = $data['data']['semester'];
	$titel = $data['data']['titel'];
	$fid = $data['data']['friedolinID'];
	$aktiv = $data['data']['aktiv'];
	$sws = $data['data']['sws'];

	$ret = $db->fetchData(<<<EOF
		SELECT veranstaltungsnummer vnr, semester sem
		FROM Lehrveranstaltung_Info
		WHERE vnr=$vnr AND sem=$sem
	EOF);

	$results = array();

	while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
		array_push($results, $row);
	}

	// enter if no match with vnr and sem found
	if(!$results) {
		$db->execute(<<<EOF
			INSERT INTO Lehrveranstaltung_Info (veranstaltungsnummer, semester, titel, friedolinID, aktiv, sws)
			VALUES ($vnr, $sem, '$titel', $fid, $aktiv, $sws)
		EOF);

		$answer = array("status" => 0, "message" => "Entry successfully created");
	} else {
		$answer = array("status" => 1, "message" => "Entry already exists");
	}
}

array_push($answer, array("res" => $results));

header('Content-Type: application/json');
echo(json_encode($answer, true));