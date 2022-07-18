<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FieldOfStudy;
use App\Models\Subject;
use Illuminate\Http\Request;

class GetController extends Controller {
    private function substr_r($str, $search) {
        $pos = strrpos($str, $search);
        if ($pos) {
            $str = trim(substr($str,0,$pos));
        }
        return $str;
    }

    public function getSubjects() {
        $subjects = Subject::pluck('name')->toArray();

        sort($subjects);

        return response($subjects, 200);
    }

    public function getFieldOfStudies(Request $request) {
        $request->validate([
            'subject' => ['string', 'required'],
        ]);


        $field_of_studies = FieldOfStudy::whereHas('subject', function ($q) use ($request) {
                $q->where('name', $request->input('subject'));
            })
            ->pluck('name')
            ->toArray();

        foreach($field_of_studies as &$field_of_study) {
            $field_of_study = $this->substr_r($field_of_study, "(");
        }

        sort($field_of_studies);

        $field_of_studies = array_merge(array("Alle"), array_unique($field_of_studies));

        return response($field_of_studies, 200);
    }

    public function getCategories(Request $request) {
        $request->validate([
            'fieldOfStudy' => ['string', 'required'],
        ]);


        $categories = Category::whereHas('field_of_study', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->input('fieldOfStudy') . '%');
            })
            ->whereNotNull('parent_id')
            ->whereNotIn('id', 
                Category::select('parent_id')
                    ->whereNotNull('parent_id')
                    ->groupBy('parent_id')
                    ->get()
                    ->toArray()
            )
            ->pluck('name')
            ->toArray();

        sort($categories);

        $categories = array_merge(array("Alle"), array_unique($categories));

        return response($categories, 200);
    }
}
