<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function test(Request $request) {
        $name = $request->input('name');
        $data = [
            "name" => $name,
            "info" => "Some Info send by the Server"
        ];
        
        return response()->json($data, 200);
	}
}
