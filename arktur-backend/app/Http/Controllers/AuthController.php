<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LDAP;

class AuthController extends Controller {
    public function login(Request $request) {
        //$ldap = new LDAP();
        //$authObject = $ldap->auth($request->input('name'), $request->input('password'));
        $authObject = true;
        if ($authObject) {
            $user = User::where('name', '=', $request->input('name'))->first();
            if ($user == null) {
                $user = User::create([
                    'name' => $request->input('name')
                ]);
            }
            Auth::login($user);
            return response('success', 200);
        }
        return response('falied', 401);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response('sucess', 200);
    }

    public function test() {
        return response('Test', 200);
    }
}
