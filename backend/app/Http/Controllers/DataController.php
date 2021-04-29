<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller {
    private $data = [
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
    ];

    private function formatString($str) {
        $str = strtolower($str);
        $str = str_replace("-", "", $str);
        return $str;
    }

    public function test(Request $request) {
        $name = $request->input('name');
        $answer = [];

        error_log($request->name);

        foreach (array_keys($this->data) as &$item) {
            $push = true;
            foreach (explode(" ", $name) as &$elem) {
                if (!str_contains($this->formatString($item), $this->formatString($elem))) {
                    $push = false;
                }
            }
            if($push) {
                array_push($answer, $this->data[$item]);
            }
        }

        return response()->json($answer, 200);
    }
}
