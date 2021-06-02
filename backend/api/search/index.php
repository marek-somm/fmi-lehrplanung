<?php
require('../src/input.php');
require('../src/database.php');
require('veranstaltung.php');
require('modul.php');

header('Access-Control-Allow-Origin: *');

function formatString($str) {
    $str = strtolower($str);
    $str = str_replace("-", "", $str);
    return $str;
}

$typ = input::get('typ', NULL);
$vnr = input::get('vnr', Null);
$semester = input::get('semester', Null);
$titel = input::get('titel', Null);
$limit = input::get('limit', 20);
$modulcode = input::get('modulcode', Null);

if ($typ == 'v') {
    if($vnr && $semester) {
        veranstaltung::search($vnr, $semester);
    } else {
        veranstaltung::searchAll($titel, $limit);
    }
} else if ($typ == 'm') {
    if($titel) {
        modul::searchAll($titel, $limit);
    } else {
        modul::search($modulcode);
    }
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