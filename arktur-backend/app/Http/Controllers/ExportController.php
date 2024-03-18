<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\FieldOfStudy;
use App\Models\Module;
use App\Models\Subject;
use Shuchkin\SimpleXLSXGen;
use Excel;

class ExportController extends Controller {

	private function gatherEvents($semester) {
		$events = Event::select('*')
			->where('semester', $semester)
			->where('events.changed', true)
			->orderBy('events.updated_at', 'desc')
			->get();

		$field_of_studies = [];

		foreach ($events as &$event) {
			$modules = $event
				->modules()
				->select('code')
				->get()
				->toArray();

			foreach ($modules as &$module) {
				$module_categories = Module::where('code', $module["code"])
					->get()->first()
					->categories()
					->select('field_of_study_id', 'parent_id', 'name', 'obligational')
					->get();

				foreach ($module_categories as $module_category) {
					$field_of_study = FieldOfStudy::select('degree_id', 'subject_id', 'po_version', 'name', 'name_short')
						->where('id', $module_category["field_of_study_id"])
						->get()->first();

					$field_of_study = Subject::select('name', 'name_short')
						->where('id', $field_of_study["subject_id"])
						->get()->first()->name_short;

					if (!in_array($field_of_study, $field_of_studies)) {
						array_push($field_of_studies, $field_of_study);
					}
				}
				
			}
			
			$event["modules"] = $modules;
			$event["field_of_studies"] = $field_of_studies;

			$event["people"] = $event
				->users()
				->select('forename', 'surname', 'email')
				->get()
				->toArray();
		}



		return $events->toArray();
	}

	// 350ms
	public function getEvents(Request $request) {
		$request->validate([
			'semester' => ['required'],
		]);

		$events = self::gatherEvents($request->semester);

		return response($events, 200);
	}

	public function export(Request $request) {
		$request->validate([
			'semester' => ['required'],
		]);

		$events = self::gatherEvents($request->semester);
		$export = [
			['Vorlesungsnr.', 'Modul', 'Semester', 'Titel', 'SWS', 'Typ', 'Extra', 'Aktualisiert'],
		];
		foreach ($events as &$event) {
			foreach ($event['modules'] as &$module) {
				$row = [];
				array_push($row, $event['vnr']);
				array_push($row, $module['code']);
				array_push($row, $event['semester']);
				array_push($row, $event['title']);
				array_push($row, $event['sws']);
				array_push($row, $event['type']);
				array_push($row, $event['extra']);
				array_push($row, $event['updated_at']);

				array_push($export, $row);
			}
		}
		$xlsx = SimpleXLSXGen::fromArray($export);
		// $path = "export/"

		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$hash = bin2hex(random_bytes(8));
		$name = $request->semester . "_" . date("h_i_s");
		$path = "download/" . $name . "_" . $hash . ".xlsx";
		$xlsx->saveAs($path); //$xlsx->saveAs('books.xlsx'); // or downloadAs('books.xlsx') or $xlsx_content = (string) $xlsx

		return response(["path" => $path, "filename" => $name . ".xlsx"]);
	}
}
