<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\Module;

class ExportController extends Controller {

	public function getEvents(Request $request) {
		$request->validate([
			'limit' => ['integer'],
			'filter'
		]);

		$events = Event::select('*')
			->where('events.changed', true)
			->orderBy('events.updated_at', 'desc')
			->get();

		foreach ($events as &$event) {
			$modules = Event::find($event->id)
				->modules()
				->select('code')
				->get()
				->toArray();
			
			$event["modules"] = $modules;

		}

		return response($events->toArray(), 200);
	}
}
