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

function formatString($str) {
    $str = strtolower($str);
    $str = str_replace("-", "", $str);
    return $str;
}

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

foreach (array_keys($data) as &$item) {
	$push = true;
    foreach (explode(" ", $name) as &$elem) {
        if (!str_contains(formatString($item), formatString($elem))) {
            $push = false;
        }
    }
    if($push) {
        array_push($answer, $data[$item]);
    }
}

if (input::get('name', false) != false) {
	header('Content-Type: application/json');
	echo(json_encode($answer, true));
} else {
	header("Location: /");
	die();
}

?>