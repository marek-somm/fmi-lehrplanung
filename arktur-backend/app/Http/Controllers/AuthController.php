<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LDAP;
use App\Helpers\General;
use App\Rules\Username;

class AuthController extends Controller {

    public function login(Request $request) {
        $request->validate(
            [
                'uid' => ['required', new Username],
                'password' => ['required']
            ],
            [
                'uid.required' => 'Login ist erforderlich.',
                'password.required' => 'Passwort ist erforderlich.'
            ]
        );

        $uid = $request->input('uid');

        # admin login - username pattern "<uid_own>:<uid_other>"
        if (str_contains($uid, ":") && count(explode(":", $uid)) == 2) {
            $uid_own = explode(":", $uid)[0];
            $uid_other = explode(":", $uid)[1];

            $user_own = User::where('uid', $uid_own)->first();
            $user_other = User::where('uid', $uid_other)->first();

            if ($user_own == null || $user_other == null || $user_own->level < 2) {
                return $this->login_failure([
                    'Anmeldedaten' => ['Login und/oder Passwort sind nicht korrekt.']
                ]);
            }

            return $this->login_user($user_other);
        }

        # try to authenticate ldap user
        $ldap = new LDAP();
        $ldap->connect();
        $authObject = $ldap->auth($request->uid, $request->password);

        if ($authObject) { # if uid + password combination is correct
            # save ldap data for debugging purposes
            $ldap_data = $ldap->directory_entry($request->uid);
            $ldap->save_data($request->uid);
            $ldap->close();

            # get assigned ldap roles
            $roles = $ldap_data["edupersonaffiliation"];
            if (!is_array($roles)) {
                $roles = [$roles];
            }

            # if user has "Mitarbeiter" role, they are eligible to login/be created
            if (in_array("Mitarbeiter", $roles)) {
                $user = $this->get_user($uid, $ldap_data);

                return $this->login_user($user);
            } else {
                return $this->login_failure([
                    'Berechtigung' => ['Sie besitzen nicht über ausreichende Rechte um auf diese Resource zuzugreifen. Bei Fragen kontaktieren Sie bitte den Seiten-Administrator.']
                ]);
            }
        }

        # ldap login not successful
        return $this->login_failure([
            'Anmeldedaten' => ['Login und/oder Passwort sind nicht korrekt.']
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response('sucess', 200);
    }

    public function check() {
        if (Auth::check()) {
            return $this->login_success();
        }
        return $this->login_failure();
    }



    # global respone for login success
    private function login_success() {
        return response([
            'success' => true,
            'level' => Auth::user()->level,
            'uid' => Auth::user()->uid,
            'currentSemester' => General::get_current_semester()
        ], 200);
    }

    # global respone for login failure or error
    private function login_failure($errors = null) {
        if ($errors != null) {
            return response([
                'success' => false,
                'errors' => $errors
            ], 200);
        }

        return response([
            'success' => false,
            'level' => 0,
            'uid' => '',
            'currentSemester' => General::get_current_semester(),
        ], 401);
    }

    private function login_user($user) {
        Auth::login($user);
        return $this->login_success();
    }

    # returns a user, if no user exists - creates it
    private function get_user($uid, $ldap_data) {
        $user = User::where('uid', $uid)->first(); # find user in database

        if ($user == null) { # if uid was not found
            # some entries have only forename + surname - find that
            $user = User::where('surname', $ldap_data['fsucompletesurname'])
                ->where('forename', $ldap_data['fsufirstname'])
                ->first();

            if ($user != null) { # user exists with forename and surname -> fill other informations
                $user->uid = $ldap_data["uid"];
                $user->email = $ldap_data["mail"];
                $user->salutaion = $ldap_data['thuedusalutation'];
                $user->displayname = $ldap_data['displayname'];
                $user->save();
            } else { # create user and find it by uid
                User::create([
                    'uid' => $ldap_data["uid"],
                    'email' => $ldap_data["mail"],
                    'forename' => $ldap_data['fsufirstname'],
                    'surname' => $ldap_data['fsucompletesurname'],
                    'salutaion' => $ldap_data['thuedusalutation'],
                    'displayname' => $ldap_data['displayname']
                ]);

                $user = User::where('uid', $uid)->first();
            }
        }

        return $user;
    }
}
