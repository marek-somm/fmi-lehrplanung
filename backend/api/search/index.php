<?php
require('../src/input.php');
require('../src/database.php');

header('Access-Control-Allow-Origin: *');

function formatString($str) {
    $str = strtolower($str);
    $str = str_replace("-", "", $str);
    return $str;
}

$vnr = input::get('vnr', Null);
$semester = input::get('semester', Null);
$titel = input::get('titel', Null);
$limit = input::get('limit', 20);

if ($titel && trim($titel) != "") {
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
        if($i != 0) {
            if($sem % 10 == 0) {
                $semStr = "SoSe ".(int)($sem/10);
            } else {
                $semStr = "WiSe ".(int)($sem/10);
            }
            $answer["data"]["$semStr"] = array();
        }
        $ret = $db->fetchData(<<<EOF
            SELECT veranstaltungsnummer vnr, semester, titel, aktiv
            FROM lehrveranstaltung_info
            WHERE titel LIKE '%$titel%' AND semester=$sem
        EOF);
        while(($row = $ret->fetchArray(SQLITE3_ASSOC)) && $i != 0) {
            $i--;
            array_push($answer["data"]["$semStr"], $row);
        }
    }

    $answer['count'] = $limit - $i;

    header('Content-Type: application/json');
    echo (json_encode($answer, true));

    $db->close();
} else if ($vnr && $semester) {
    $db = connectDatabase();
    $answer = array();
    $exams = array();
    $rolls = array('verantwortlich', 'begleitend','organisatorisch');

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
        SELECT kommentar Kommentar, literatur Literatur, bemerkung Bemerkung, zielgruppe Zielgruppe, lerninhalte Lerninhalte, leistungsnachweis Leistungsnachweis
        FROM Lehrveranstaltung_Info inf
        JOIN Lehrveranstaltung_Inhalt inh ON inf.lehrvID=inh.lehrvID
        WHERE inf.veranstaltungsnummer=$vnr AND inf.semester=$semester
    EOF);

    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        $answer['content'] = $row;
    }

    foreach($rolls as &$roll) {
        $ret = $db->fetchData(<<<EOF
            SELECT p.vorname, p.nachname, p.grad, blp.rolle, p.friedolinID
            FROM Lehrveranstaltung_Info i
            JOIN BRIDGE_Lehrveranstaltung_Person blp, Person p ON blp.lehrvID=i.lehrvID AND blp.personenID=p.personenID
            WHERE i.veranstaltungsnummer=$vnr AND i.semester=$semester AND blp.rolle='$roll'
        EOF);

        $people = array();

        while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            array_push($people, $row);
        }

        $roll = ucfirst($roll);
        $answer["people"]["$roll"] = $people;
    }
    
    $ret = $db->fetchData(<<<EOF
        SELECT pnr, modulcode, pr.titel
        FROM Lehrveranstaltung_Info i
        JOIN BRIDGE_Lehrveranstaltung_Pruefung blp, Pruefung pr ON blp.lehrvID=i.lehrvID AND blp.VENR=pr.VENR
        WHERE i.veranstaltungsnummer=$vnr AND i.semester=$semester
    EOF);

    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        array_push($exams, $row);
    }

    $answer['exams'] = $exams;

    header('Content-Type: application/json');
    echo (json_encode($answer, true));

    $db->close();
} else {
    header("Location: /");
    die();
}

/*
$data = array(
    "Einführung in die Bildinformatik" => [
        "name" => "Einführung in die Bildinformatik",
        "type" => "Informatik",
        "lp" => 6,
        "sws" => 4,
        "info" => "Die Studierenden kennen grundlegende Verfahren der Bildinformatik, d.h. speziell der Bildverarbeitung (Bildverbesserung, Segmentierung und Interpretation von Bildinformation durch den Rechner), der Computergrafik (Datenstrukturen zur Repräsentation 3D Szenen und Rendering Pipeline) sowie der Visualisierung (Visualisierungspipeline).\nDie Studierenden sind danach auch in der Lage, den Zusammenhang zwischen den drei Gebieten herzustellen und einfache, kleine Systeme selber zu implementieren."
    ],
    "Einführung in die Künstliche Intelligenz" => [
        "name" => " Einführung in die Künstliche Intelligenz",
        "type" => "Informatik",
        "lp" => 6,
        "sws" => 4,
        "info" => "Vertrautheit mit grundlegenden Konzepten und Methoden symbolischer Informationsverarbeitung zur Modellierung kognitivver Leistungen und Lösung technischer Probleme. Einsicht in Möglichkeiten und Grenzen der symbolischen KI."
    ],
    "Experimentelle Hardware-Projekte" => [
        "name" => "Experimentelle Hardware-Projekte",
        "type" => "Informatik",
        "lp" => 3,
        "sws" => 3,
        "info" => "Die Studierenden erwerben die Fähigkeit, kleine Aufgabenstellungen aus den Bereichen Schaltfunktionen, Prozessoren, Datenübertragung, Parallelität und MOS-Transistoren zu lösen und in konkrete Hardware umzusetzen. Diese Aufgabenstellungen werden in Gruppen bearbeitet, so dass erfolgreiches Teamwork ein weiteres Ziel ist."
    ]
);

$name = input::get('name', false);

$answer = array();

if ($name != "") {
    foreach (array_keys($data) as &$item) {
        $push = true;
        foreach (explode(" ", $name) as &$elem) {
            if (!str_contains(formatString($item), formatString($elem))) {
                $push = false;
            }
        }
        if ($push) {
            array_push($answer, $data[$item]);
        }
    }
}

if (input::get('name', false) != false) {
    header('Content-Type: application/json');
    echo (json_encode($answer, true));
} else {
    header("Location: /");
    die();
}
*/