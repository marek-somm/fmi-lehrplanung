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
            'vnr' => ['integer','required'],
            'sem' => ['integer','required'],
            'title' => ['string', 'required'],
            'type' => ['string', 'required'],
            'exams' => ['array', 'required']
        ]);

        $exists = count(Event::where("vnr", $request->vnr)
            ->where("semester", $request->sem)    
            ->get());

        if($exists) {
            return response("Element already exists", 422);
        }

        $event_id = Event::create([
            'vnr' => $request->vnr,
            'semester' => $request->sem,
            'title' => $request->title,
            'active' => 1,
            'sws' => $request->sws ? $request->sws : Null,
            'type' => $request->type,
            'targets' => $request->targets ? $request->target : Null,
            'rotation' => $request->rotation ? $request->rotation : Null,
            'changed' => 1
        ])->id;

        foreach($request->exams as $exam) {
            $module_id = Module::select("id")
                ->where("modulecode", $exam["modulecode"])
                ->get()
                ->first();
            
            if($module_id) {
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

        foreach($request->people as $person) {
            $split = explode(", ", $person);
            $forename = $split[1];
            $surname = $split[0];

            $user = User::select("id")
                ->where("forename", $forename)
                ->where("surname", $surname)
                ->get()
                ->first();
            
            if($user) {
                EventUser::create([
                    'event_id' => $event_id,
                    'user_id' => $user->id
                ]);
            }
        }

        return response("success", 200);
    }

    function removeEvent(Request $request) {
        $request->validate([
            'vnr' => ['integer','required'],
            'sem' => ['integer','required']
        ]);

        $exists = count(Event::where("vnr", $request->vnr)
            ->where("semester", $request->sem)    
            ->get());

        if(!$exists) {
            return response("Element does not exists", 422);
        }

        $event_id = Event::select('id')
            ->where("vnr", $request->vnr)
            ->where("semester", $request->sem)
            ->get()
            ->first()
            ->id;
        
        EventUser::where("event_id", $event_id)
            ->delete();
        
        EventModule::where("event_id", $event_id)
            ->delete();
        
        Event::where("id", $event_id)
            ->delete();

        $exists = count(Event::where("vnr", $request->vnr)
            ->where("semester", $request->sem)    
            ->get());

        if(!$exists) {
            return response("success", 200);
        }

        return response("Something went wrong", 400);
    }
}
