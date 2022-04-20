<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventModule;
use App\Models\EventUser;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateController extends Controller {
    function addEvent(Request $request) {
        $request->validate([
            'sem' => ['integer', 'required'],
            'title' => ['string', 'required'],
            'type' => ['string', 'required'],
        ]);

        if ($request->vnr) {
            $exists = count(Event::where("vnr", $request->vnr)
                ->where("semester", $request->sem)
                ->get());
        } else {
            $exists = count(Event::where("id", $request->id)
                ->where("semester", $request->sem)
                ->get());
        }

        if ($exists) {
            return response("Element already exists", 422);
        }

        $event_id = Event::create([
            'vnr' => $request->vnr,
            'semester' => $request->sem,
            'title' => $request->title,
            'active' => 1,
            'sws' => $request->sws ? $request->sws : Null,
            'type' => $request->type,
            'rotation' => $request->rotation ? $request->rotation : Null,
            'changed' => 1
        ])->id;

        foreach ($request->exams as $exam) {
            $module_id = Module::select("id")
                ->where("code", $exam["modulecode"])
                ->get()
                ->first();

            if ($module_id) {
                $module_id = $module_id->id;
            }

            EventModule::create([
                'event_id' => $event_id,
                'module_id' => $module_id,
                'pnr' => $exam["pnr"],
                'description' => $exam["description"],
                'title' => $exam["title"],
                'changed' => 1
            ]);
        }

        foreach ($request->people as $person) {
            $split = explode(", ", $person);
            $forename = $split[1];
            $surname = $split[0];

            $user = User::select("id")
                ->where("forename", $forename)
                ->where("surname", $surname)
                ->get()
                ->first();

            if ($user) {
                EventUser::create([
                    'event_id' => $event_id,
                    'user_id' => $user->id
                ]);
            }
        }

        return response("success", 200);
    }

    function updateEvent(Request $request) {
        $request->validate([
            'id' => ['integer', 'required'],
            'sem' => ['integer', 'required'],
            'title' => ['string', 'required'],
            'sws' => ['integer', 'required'],
            'rotation' => ['integer', 'required'],
            'type' => ['string', 'required'],
            'people' => ['array', 'required'],
        ]);

        $event = Event::where("id", $request->id)
            ->get()
            ->first();

        if (!$event) {
            return response("Element does not exist", 422);
        }

        EventUser::where("event_id", $request->id)
            ->delete();

        EventModule::where("event_id", $request->id)
            ->delete();

        Event::where("id", $request->id)
            ->delete();

        return $this->addEvent($request);
    }

    function removeEvent(Request $request) {
        $request->validate([
            'id' => ['integer', 'required']
        ]);

        $exists = count(Event::where("id", $request->id)
            ->get());

        if (!$exists) {
            return response("Element does not exists", 422);
        }

        EventUser::where("event_id", $request->id)
            ->delete();

        EventModule::where("event_id", $request->id)
            ->delete();

        Event::where("id", $request->id)
            ->delete();

        $exists = count(Event::where("id", $request->id)
            ->get());

        if (!$exists) {
            return response("success", 200);
        }

        return response("Something went wrong", 400);
    }
}
