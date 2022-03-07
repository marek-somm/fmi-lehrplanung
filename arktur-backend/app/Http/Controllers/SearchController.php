<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Module;
use App\Models\User;
use App\Rules\Search;
use App\Rules\Username;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller {
    private function semester_to_string($semester) {
        if ($semester % 10 == 0) {
            return "SoSe " . (int)($semester / 10);
        } else {
            return "WiSe " . (int)($semester / 10);
        };
    }

    private function group_by($key, $data) {
        $result = array();

        foreach ($data as $model) {
            $semester = $model[$key];
            if (!array_key_exists($semester, $result)) {
                $result[$semester] = array($model);
            } else {
                array_push($result[$semester], $model);
            }
        }

        return $result;
    }

    private function in_sub_array($needle, $haystack, $identifier) {
        foreach ($haystack as $elem) {
            if ($needle == $elem[$identifier]) {
                return true;
            }
        }
        return false;
    }

    private function in_module_array($module, $array) {
        foreach ($array as $elem) {
            if ($module['modulecode'] == $elem['modulecode']) {
                return true;
            }
        }
        return false;
    }

    private function in_event_array($event, $array) {
        foreach ($array as $elem) {
            if ($event["vnr"] == $elem["vnr"]) {
                return true;
            }
        }
        return false;
    }

    public function getEvent(Request $request) {
        $request->validate([
            'vnr' => ['required', 'integer'],
            'semester' => ['required', 'integer']
        ]);

        $event = Event::select('id', 'active', 'rotation', 'semester', 'sws', 'title', 'type', 'vnr')
            ->where('vnr', $request->vnr)
            ->where('semester', $request->semester)
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

        $user = Auth::user()->uid;

        $event["own"] = false;
        if ($this->in_sub_array($user, $users, 'uid')) {
            $event["own"] = true;
        }

        $modules = Event::find($event->id)
            ->modules()
            ->select('modulecode')
            ->get();

        $response = [
            'content' => $event,
            'people' => $people,
            'modules' => $modules
        ];

        return response($response, 200);
    }

    public function searchEvent(Request $request) {
        $request->validate([
            'value' => [new Search],
            'limit' => ['integer'],
            'filter'
        ]);

        $filter = json_decode($request->filter, true);

        if ($filter["inactive"] == False) {
            $events = Event::select('active', 'semester', 'title', 'vnr')
                ->where('active', "1")
                ->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->value . '%')
                        ->orWhere('vnr', 'LIKE', '%' . $request->value . '%');
                })
                ->limit($request->limit)
                ->orderBy('semester', 'desc')
                ->get();
        } else {
            $events = Event::select('active', 'semester', 'title', 'vnr')
                ->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->value . '%')
                        ->orWhere('vnr', 'LIKE', '%' . $request->value . '%');
                })
                ->limit($request->limit)
                ->orderBy('semester', 'desc')
                ->get();
        }

        $response = $this->group_by("semester", $events);

        return response($response, 200);
    }

    public function getModule(Request $request) {
        $request->validate([
            'modulecode' => ['required', 'regex:/^[\w\d\-]*$/'],
        ]);

        $module = Module::select('id', 'modulecode', 'title_de', 'title_en', 'active', 'rotation', 'composition', 'ects', 'presence_time', 'workload', 'type', 'prior_knowledge', 'content', 'required_creditpoints', 'requirement_exam', 'requirement_admission', 'additional_info', 'literature')
            ->where('modulecode', $request->modulecode)
            ->get()
            ->first();

        $people = Module::find($module->id)
            ->users()
            ->select('displayname', 'forename', 'surname', 'uid')
            ->get();

        $events = Module::find($module->id)
            ->events()
            ->select('vnr', 'semester')
            ->orderBy('semester', 'desc')
            ->get();

        $events = $this->group_by("semester", $events);

        $response = [
            'content' => $module,
            'people' => $people,
            'events' => $events
        ];

        return response($response, 200);
    }

    public function searchModule(Request $request) {
        $request->validate([
            'value' => [new Search],
            'limit' => ['integer']
        ]);

        $modules = Module::select('active', 'title_de', 'title_en', 'modulecode')
            ->where('title_de', 'LIKE', '%' . $request->value . '%')
            ->orWhere('title_en', 'LIKE', '%' . $request->value . '%')
            ->orWhere('modulecode', 'LIKE', '%' . $request->value . '%')
            ->limit($request->limit)
            ->get();

        $response = $modules;

        return response($response, 200);
    }

    public function getNewEntries() {
        $changed_events = Event::select('*')
            ->where('changed', true)
            ->get();

        $response = [];
        $changed_modules = [];

        foreach ($changed_events as &$event) {
            $modules = Event::find($event->id)
                ->modules()
                ->get()
                ->toArray();

            foreach ($modules as &$module) {
                if (!$this->in_module_array($module, $changed_modules)) {
                    array_push($changed_modules, $module);
                }
            }
        }

        foreach ($changed_modules as &$module) {
            $events = Module::find($module['id'])
                ->events()
                ->get();

            $pnrs = [];
            $pnrObj = [];
            foreach ($events as &$event) {
                $pnr = $event->pivot->pnr;
                if (!in_array($pnr, $pnrs)) {
                    array_push($pnrs, $pnr);
                    $item['pnr'] = $pnr;
                    $item['events'] = Module::find($module['id'])
                        ->events()
                        ->where('pivot_pnr', $pnr)
                        ->where('events.changed', true)
                        ->get();
                    array_push($pnrObj, $item);
                }
            }

            $item = [];
            $item['module'] = $module['modulecode'];
            $item['pnr'] = $pnrObj;

            array_push($response, $item);
        }

        return response($response, 200);
    }

    public function searchPerson(Request $request) {
        $request->validate([
            'name' => [new Search],
        ]);

        $persons = User::select('*')
            ->where('forename', 'LIKE', '%' . $request->name . '%')
            ->orWhere('surname', 'LIKE', '%' . $request->name . '%')
            ->get();

        return response($persons, 200);
    }

    public function getUserEvents(Request $request) {
        $request->validate([
            'user' => [new Username],
            'currentSem' => ['string', 'required']
        ]);

        $events_all = User::where('uid', $request->user)
            ->firstOrFail()
            ->events()
            ->get()
            ->toArray();

        $events_current = User::where('uid', $request->user)
            ->firstOrFail()
            ->events()
            ->where('semester', $request->currentSem)
            ->get()
            ->toArray();

        $events_past = User::where('uid', $request->user)
            ->firstOrFail()
            ->events()
            ->where('semester', '<', $request->currentSem)
            ->get()
            ->toArray();


        $past = $this->group_by("semester", $events_past);
        $current = $this->group_by("semester", $events_current);
        $future = $this->generate_future_events($events_all, $request->currentSem);

        return response(["future" => $future, "current" => $current, "past" => $past], 200);
    }

    private function generate_future_events($events, $current_semester) {
        # remove duplicats
        $filtered_events = [];

        foreach (array_reverse($events) as $event) {
            if (!$this->in_event_array($event, $filtered_events) && $event["active"] == true && $event["rotation"] > 0 && $event["semester"] > ($current_semester - 20)) {
                $event["semester_org"] = $event["semester"];
                $event["own"] = true;

                if ($event["rotation"] == 2) {
                    if ($event["semester"] % 10 == 0) {
                        $event["semester"] = $current_semester + ($current_semester % 10 == 0 ? 10 : 9);
                    } else {
                        $event["semester"] = $current_semester + ($current_semester % 10 == 0 ? 1 : 10);
                    }
                    $event["active"] = $this->event_exists($event) ? $event["active"] : false;
                    $filtered_events[] = $event;
                } else {
                    if ($current_semester % 10 == 0) {
                        $event["semester"] = $current_semester + 1;
                        $event["active"] = $this->event_exists($event) ? $event["active"] : false;
                        $filtered_events[] = $event;
                    } else {
                        $event["semester"] = $current_semester + 9;
                        $event["active"] = $this->event_exists($event) ? $event["active"] : false;
                        $filtered_events[] = $event;
                    }
                    $event["semester"] = $current_semester + 10;
                    $event["active"] = $this->event_exists($event) ? $event["active"] : false;
                    $filtered_events[] = $event;
                }
            }
        }

        return $this->group_by("semester", $filtered_events);
    }

    private function event_exists($event) {
        return Event::where("vnr", $event["vnr"])
            ->where("semester", $event["semester"])
            ->get()
            ->first() != null;
    }
}
