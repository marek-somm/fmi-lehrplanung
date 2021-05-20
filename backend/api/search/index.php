<?php

header('Access-Control-Allow-Origin: *');

class input {

    public static function get($key, $value = false) {
        return (!empty($_GET[$key])) ? $_GET[$key] : $value;
    }

    public static function post($key, $value = false) {
        return (!empty($_POST[$key])) ? $_POST[$key] : $value;
    }

    public static function cookie($key, $value = false) {
        return (!empty($_COOKIE[$key])) ? $_COOKIE[$key] : $value;
    }
}

class MyDB extends SQLite3 {
    function __construct() {
        $this->open('dc5a2a51976a32643a33ef6746dbf45a.db');
    }

    function fetchData($sql) {
        $ret = $this->query($sql);
        return $ret;
    }
}

function formatString($str) {
    $str = strtolower($str);
    $str = str_replace("-", "", $str);
    return $str;
}

function connectDatabase() {
    $db = new MyDB();
    if (!$db) {
        die($db->lastErrorMsg());
    } else {
        return $db;
    }
}

$modulcode = input::get('modulcode', Null);
$titel_de = input::get('titel_de', Null);

if ($titel_de) {
    $db = connectDatabase();
    $answer = array();

    $ret = $db->fetchData(<<<EOF
        SELECT titel_de, modulcode, lp
        FROM modul
        WHERE titel_de LIKE '%$titel_de%'
    EOF);

    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        array_push($answer, $row);
    }
    
    header('Content-Type: application/json');
    echo (json_encode($answer, true));

    $db->close();
}

if ($modulcode) {
    $db = connectDatabase();
    $answer = array();

    $ret = $db->fetchData(<<<EOF
        SELECT modulcode Modulcode, ects ECTS, praesenzzeit Präsenzzeit, workload Workload, lp LP, t.name Turnus, titel_de TitelDE, titel_en TitelEN, zusammensetzung Zusammensetzung, art Art, inhalte Inhalte, vorkentnisse Vorkentnisse, vor_lp "Vorraussetzungen Leistungspunkte", vor_pruefungen "Vorraussetzungen Prüfungen", vor_zulassung "Vorraussetzungen Zulassung", literatur Literatur, zusatzinfos Zusatzinfos
        FROM modul m
        JOIN modul_turnus t ON m.turnusID=t.turnusID
        WHERE modulcode="$modulcode"
    EOF);

    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        array_push($answer, $row);
    }

    header('Content-Type: application/json');
    echo (json_encode($answer, true));

    $db->close();
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