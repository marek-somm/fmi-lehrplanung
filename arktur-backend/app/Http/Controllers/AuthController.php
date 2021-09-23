<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LDAP;
use App\Rules\Username;

class AuthController extends Controller {
    public function login(Request $request) {
        $request->validate([
            'name' => ['required', new Username],
            'password' => ['required']
        ],
        [
            'name.required' => 'Login ist erforderlich.',
            'password.required' => 'Passwort ist erforderlich.'
        ]);
        //PRODUCTION $ldap = new LDAP();
        //PRODUCTION $authObject = $ldap->auth($request->input('name'), $request->input('password'));
        $authObject = true;
        if ($authObject) {
            $user = User::where('name', '=', $request->input('name'))->first();
            if ($user == null) {
                $user = User::create([
                    'name' => $request->input('name')
                ]);
            }
            Auth::login($user);
            return response([
                'success' => true,
                'level' => 1,
                'uid' => Auth::user()->name
            ], 200);
        }
        return response([
            'errors' => [
                'Anmeldedaten' => ['Login und/oder Passwort sind nicht korrekt.']
            ]
        ], 401);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response('sucess', 200);
    }

    public function check() {
        if (Auth::check()) {
            return response([
                'success' => true,
                'level' => 1,
                'uid' => Auth::user()->name
            ], 200);
        }
        return response([
            'success' => false,
            'level' => 0,
            'uid' => ''
        ], 200);
    }

    public function test() {
        return response('Test', 200);
    }
}
