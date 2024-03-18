<?php

// cSpell:ignore modulecode

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

use App\Models\Event;

class EventController extends Controller {
	/**
	 * Finds an Event by its id.
	 * 
	 * @param Request $request
	 * 
	 * @return Response HTTP response [200] and all necessary data of the event on success, [404] if the event was not found.
	 */
	public function getEvent(Request $request): Response {
		$request->validate([
			'id' => ['required', 'integer'],
		]);

		if (!AuthController::isAuthenticated()) {
			return $this->getEventLight($request);
		}

		$event = Event::select('id', 'active', 'rotation', 'semester', 'sws', 'title', 'type', 'vnr', 'extra', 'room', 'time', 'exam')
			->where('id', $request->id)
			->get()
			->first();

		if (!$event) {
			return response("Event not found", 404);
		}

		$people = Event::find($event->id)
			->users()
			->select('displayname', 'forename', 'surname', 'email')
			->get();

		$users = Event::find($event->id)
			->users()
			->select('uid')
			->get()
			->toArray();


		if (Auth::user()) {
			$user = Auth::user()->uid;

			$event["own"] = false;
			if ($this->in_sub_array($user, $users, 'uid')) {
				$event["own"] = true;
			}
		}

		$modules = Event::find($event->id)
			->modules()
			->select('code as modulecode')
			->get();

		$response = [
			'information' => $event,
			'people' => $people,
			'modules' => $modules
		];

		return response($response, 200);
	}

	private function getEventLight(Request $request) {
		$request->validate([
			'id' => ['required', 'integer'],
		]);

		$event = Event::select('id', 'active', 'rotation', 'semester', 'sws', 'title', 'type', 'vnr', 'extra')
			->where('id', $request->id)
			->get()
			->first();

		if (!$event) {
			return response("Event not found", 404);
		}

		$people = Event::find($event->id)
			->users()
			->select('displayname', 'forename', 'surname')
			->get();

		$modules = Event::find($event->id)
			->modules()
			->select('code as modulecode')
			->get();

		$response = [
			'information' => $event,
			'people' => $people,
			'modules' => $modules
		];

		return response($response, 200);
	}

	private function in_sub_array($needle, $haystack, $identifier) {
		foreach ($haystack as $elem) {
			if ($needle == $elem[$identifier]) {
				return true;
			}
		}
		return false;
	}
}
