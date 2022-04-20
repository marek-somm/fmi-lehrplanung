<?php

namespace App\Http\Controllers;

use App\Helpers\General;
use App\Models\Category;
use App\Models\Event;
use App\Models\FieldOfStudy;
use App\Models\Module;
use App\Models\Subject;
use App\Models\User;
use App\Rules\Search;
use App\Rules\Username;
use Illuminate\Http\Request;

class SearchController extends Controller {

    private function group_by_semester($data) {
        $result = array();

        foreach ($data as $model) {
            $semester = $model["semester"];
            if (!array_key_exists($semester, $result)) {
                $result[$semester] = array($model);
            } else {
                array_push($result[$semester], $model);
            }
        }

        return $result;
    }

    private function in_module_array($module, $array) {
        foreach ($array as $elem) {
            if ($module['code'] == $elem['code']) {
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

    private function in_category_array($category, $array) {
        foreach ($array as $elem) {
            if ($category["id"] == $elem["id"]) {
                return true;
            }
        }
        return false;
    }


    public function searchEvent(Request $request) {
        $request->validate([
            'value' => [new Search],
            'limit' => ['integer'],
            'filter'
        ]);

        $filter = json_decode($request->filter, true);

        if (count($filter) > 0 && $filter["inactive"] == False) {
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

        $response = $this->group_by_semester($events);

        return response($response, 200);
    }

    public function getModule(Request $request) {
        $request->validate([
            'modulecode' => ['required', 'regex:/^[\w\d\-]*$/'],
        ]);

        $module = Module::select('id', 'code as modulecode', 'title_de', 'title_en', 'active', 'rotation', 'composition', 'ects', 'presence_time', 'workload', 'type', 'prior_knowledge', 'content', 'required_creditpoints', 'requirement_exam', 'requirement_admission', 'additional_info', 'literature')
            ->where('code', $request->modulecode)
            ->get()->first();

        $module_categories = Module::where('code', $request->modulecode)
            ->get()->first()
            ->categories()
            ->select('field_of_study_id', 'parent_id', 'name', 'obligational')
            ->get();

        $module_extra = [];
        foreach ($module_categories as $module_category) {
            $field_of_study = FieldOfStudy::select('degree_id', 'subject_id', 'po_version', 'name', 'name_short')
                ->where('id', $module_category["field_of_study_id"])
                ->get()->first();

            $field_of_study = Subject::select('name', 'name_short')
                ->where('id', $field_of_study["subject_id"])
                ->get()->first()->name;

            $parent = Category::select('name', 'parent_id')
                ->where('id', $module_category["parent_id"])
                ->get()->first();

            $module_category["field_of_study"] = $field_of_study;
            if ($parent != null) {
                while ($parent->parent_id != null) {
                    $parent = Category::select('name', 'parent_id')
                        ->where('id', $parent["parent_id"])
                        ->get()->first();
                }
                $module_category["parent"] = $parent->name;
            }

            unset($module_category["pivot"]);
            unset($module_category["field_of_study_id"]);
            unset($module_category["parent_id"]);

            array_push($module_extra, $module_category);
        }

        $people = Module::find($module->id)
            ->users()
            ->select('displayname', 'forename', 'surname', 'uid')
            ->get();

        $events = Module::find($module->id)
            ->events()
            ->select('vnr', 'semester')
            ->orderBy('semester', 'desc')
            ->get();

        $events = $this->group_by_semester($events);

        $response = [
            'content' => $module,
            'extra' => $module_extra,
            'people' => $people,
            'events' => $events
        ];

        return response($response, 200);
    }

    public function searchModule(Request $request) {
        $request->validate([
            'value' => [new Search],
            'limit' => ['integer'],
            'filter'
        ]);

        $modules = Module::select('active', 'title_de', 'title_en', 'code as modulecode')
            ->where('title_de', 'LIKE', '%' . $request->value . '%')
            ->orWhere('title_en', 'LIKE', '%' . $request->value . '%')
            ->orWhere('code', 'LIKE', '%' . $request->value . '%')
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
            $item['module'] = $module['code'];
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


        $past = $this->group_by_semester($events_past);
        $current = $this->group_by_semester($events_current);
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

        return $this->group_by_semester($filtered_events);
    }

    private function event_exists($event) {
        return Event::where("vnr", $event["vnr"])
            ->where("semester", $event["semester"])
            ->get()
            ->first() != null;
    }

    public function getStudentEvents_old() {
        $events = Event::where('semester', General::get_current_semester())
            ->where('active', True)
            ->get();

        foreach ($events as &$event) {
            $modules = $event->modules()->get();
            $categories = array();

            foreach ($modules as &$module) {
                $module_categories = $module
                    ->categories()
                    ->select('categories.id', 'field_of_study_id', 'parent_id', 'name', 'obligational')
                    ->get();
                foreach ($module_categories as &$category) {
                    if (!$this->in_category_array($category, $categories)) {
                        $field_of_study = $category->field_of_study()
                            ->get()->first();

                        $subject = $field_of_study->subject()
                            ->get()->first()->name;

                        $field_of_study = $field_of_study->name;

                        $parent = $category->parent()
                            ->get()->first();

                        $category["field_of_study"] = $field_of_study;
                        $category["subject"] = $subject;

                        if ($parent != null) {
                            while ($parent->parent_id != null) {
                                $parent = $parent->parent()
                                    ->get()->first();
                            }
                            $category["parent"] = $parent->name;
                        }

                        unset($category["pivot"]);
                        unset($category["field_of_study_id"]);
                        unset($category["parent_id"]);
                        array_push($categories, $category);
                    }
                }
            }

            $event["categories"] = $categories;

            unset($event["active"]);
            unset($event["id"]);
            unset($event["semester"]);
        }

        return response(['current' => $events], 200);
    }

    public function getStudentEvents(Request $request) {
        $request->validate([
            'subject' => ['string'],
            'fieldOfStudy' => ['string'],
            'category' => ['string'],
            'semester' => ['int', 'required']
        ]);

        if ($request->input('category')) {
            $events = Event::where('semester', $request->input("semester"))
                ->select('id', 'changed', 'rotation', 'sws', 'title', 'type')
                ->where('active', True)
                ->whereHas('modules', function ($q) use ($request) {
                    $q->whereHas('categories', function ($q) use ($request) {
                        $q->whereHas('field_of_study', function ($q) use ($request) {
                            $q->whereHas('subject', function ($q) use ($request) {
                                $q->where('name', $request->input('subject'));
                            })->where('name', 'LIKE', '%' . $request->input('fieldOfStudy') . '%');
                        })->where('name', $request->input('category'));
                    });
                })
                ->get();
                
            return response($events, 200);
        } else if ($request->input('fieldOfStudy')) {
            $events = Event::where('semester', $request->input("semester"))
                ->select('id', 'changed', 'rotation', 'sws', 'title', 'type')
                ->where('active', True)
                ->whereHas('modules', function ($q) use ($request) {
                    $q->whereHas('categories', function ($q) use ($request) {
                        $q->whereHas('field_of_study', function ($q) use ($request) {
                            $q->whereHas('subject', function ($q) use ($request) {
                                $q->where('name', $request->input('subject'));
                            })->where('name', 'LIKE', '%' . $request->input('fieldOfStudy') . '%');
                        });
                    });
                })
                ->get();
                
            return response($events, 200);
        } else if($request->input('subject')) {
            $events = Event::where('semester', $request->input("semester"))
                ->select('id', 'changed', 'rotation', 'sws', 'title', 'type')
                ->where('active', True)
                ->whereHas('modules', function ($q) use ($request) {
                    $q->whereHas('categories', function ($q) use ($request) {
                        $q->whereHas('field_of_study', function ($q) use ($request) {
                            $q->whereHas('subject', function ($q) use ($request) {
                                $q->where('name', $request->input('subject'));
                            });
                        });
                    });
                })
                ->get();
                
            return response($events, 200);
        }
    }
}
