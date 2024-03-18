<?php

// cSpell:ignore edupersonaffiliation fsucompletesurname fsufirstname thuedusalutation

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LDAP;
use App\Helpers\ResponseMessage;
use App\Rules\Username;


/**
 * Handles all authentication related requests. Creates users in the database if they have sufficient permissions to login.
 */
class AuthController extends Controller {
	/**
	 * Request handler for logins.
	 * 
	 * @param Request $request A Request Object containing uid (e.g. ab12xyz) and password
	 * 
	 * @author Marek Sommerfeld
	 * @return Response HTTP response [200] on success, [401] on failure.
	 */
	public function login(Request $request): Response {
		$request->validate(
			[
				'uid' => ['required', new Username],
				'password' => ['required']
			],
			[
				'uid.required' => ResponseMessage::$required_data['uid'],
				'password.required' => ResponseMessage::$required_data['password']
			]
		);

		$username = $request->input('uid');
		$password = $request->input('password');

		// admin login - username pattern "<uid_own>:<uid_other>"
		if (str_contains($username, ":") && count(explode(":", $username)) == 2) {
			return $this->loginAdmin($username, $password);
		}

		// try to authenticate LDAP user
		$ldap = new LDAP();
		$ldap->connect();
		$authObject = $ldap->auth($username, $password);

		// if username + password combination does not match
		if (!$authObject) {
			return self::loginFailure(ResponseMessage::$wrong_credentials);
		}

		// retrieve LDAP data and close LDAP connection
		$ldap_data = $ldap->directory_entry($username);
		$ldap->save_data($username); // save ldap data for debugging purposes //! remove before release
		$ldap->close();

		// get assigned ldap roles
		$roles = $ldap_data["edupersonaffiliation"];
		if (!is_array($roles)) {
			$roles = [$roles];
		}

		// if user doesn't have "Mitarbeiter" role, they are not eligible to login/be created
		if (!in_array("Mitarbeiter", $roles)) {
			return self::loginFailure(ResponseMessage::$insufficient_permissions);
		}

		// get/create user object and login
		$user = $this->getUser($username, $ldap_data);
		return $this->loginUser($user);
	}

	/**
	 * Request handler for logouts: Invalidates session.
	 * 
	 * @param Request $request Necessary to invalidate session.
	 * @return Response HTTP response [200].
	 */
	public function logout(Request $request): Response {
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return response("");
	}

	/**
	 * Handles queries for user authentication statues.
	 * 
	 * @return Response HTTP response [200] on successful authentication, [401] on failure.
	 */
	public function authenticationCheck(): Response {
		if (self::isAuthenticated()) {
			return self::loginSuccess();
		}
		return self::loginFailure();
	}


	/**
	 * Login as admin.
	 * 
	 * Checks if level of the user logging in is 3 (admin) or higher and simulates a login with a different user from the database.
	 * 
	 * @param string $usernames Contains two usernames separated by ':'. The first username is the admin, the second the simulated user to login.
	 * @param string $password The password of the admin user account.
	 * @return Response HTTP response [200] on login success, [401] on failure.
	 */
	private function loginAdmin(string $usernames, string $password): Response {
		if (!str_contains($usernames, ":") || count(explode(":", $usernames)) < 2) {
			return self::loginFailure(ResponseMessage::$wrong_credentials);
		}

		$userNameOwn = explode(":", $usernames)[0];
		$userNameOther = explode(":", $usernames)[1];

		$userOwn = User::where('uid', $userNameOwn)->first();
		$userOther = User::where('uid', $userNameOther)->first();

		if ($userOwn == null || $userOther == null || $userOwn->level < 3) {
			return self::loginFailure(ResponseMessage::$wrong_credentials);
		}

		// try to authenticate LDAP user
		$ldap = new LDAP();
		$ldap->connect();
		$authObject = $ldap->auth($userNameOwn, $password);

		$isLocalhost = true; //! remove before release

		// if username (admin) + password (admin) combination does not match
		if (!$authObject && !$isLocalhost) {
			return self::loginFailure(ResponseMessage::$wrong_credentials);
		}

		return $this->loginUser($userOther);
	}

	/**
	 * Login a user.
	 * 
	 * @param User $user User object to login.
	 * @return Response HTTP response [200].
	 */
	private function loginUser(User $user): Response {
		Auth::login($user);
		return self::loginSuccess();
	}


	/**
	 * Finds a user and returns it. Creates a user in the database if it doesn't exist.
	 * 
	 * @param string $uid Unique identifier of the user the following format: ab12xyz.
	 * @param array $ldap_data User data stored in LDAP.
	 * 
	 * @return User User object from the database.
	 */
	private function getUser(string $uid, array $ldap_data): User {
		// find user in database
		$user = User::where('uid', $uid)->first();

		if ($user == null) {
			return $this->createUser($uid, $ldap_data);
		}

		return $user;
	}

	/**
	 * Creates or completes (if the surname and forename are present) a user in the database
	 * 
	 * @param string $uid Unique identifier of the user the following format: ab12xyz.
	 * @param array $ldap_data User data stored in LDAP.
	 * 
	 * @return User User object from the database.
	 */
	private function createUser(string $uid, array $ldap_data): User {
		// some entries have only forename + surname - search for such an entry
		$user = User::where('surname', $ldap_data['fsucompletesurname'])
			->where('forename', $ldap_data['fsufirstname'])
			->first();

		// user exists with forename and surname -> fill other information
		if ($user != null) {
			$user->uid = $ldap_data["uid"];
			$user->email = $ldap_data["mail"];
			$user->salutation = $ldap_data['thuedusalutation'];
			$user->displayname = $ldap_data['displayname'];
			$user->save();

			return $user;
		}

		// create user and find it by uid
		User::create([
			'uid' => $ldap_data["uid"],
			'email' => $ldap_data["mail"],
			'forename' => $ldap_data['fsufirstname'],
			'surname' => $ldap_data['fsucompletesurname'],
			'salutation' => $ldap_data['thuedusalutation'],
			'displayname' => $ldap_data['displayname']
		]);

		$user = User::where('uid', $uid)->first();
		return $user;
	}



	/**
	 * Checks if a user is authenticated.
	 * 
	 * @return Response true if user is authenticated, false if not.
	 */
	public static function isAuthenticated(): bool {
		return Auth::check();
	}

	/**
	 * Checks if a user has examination office statues.
	 * 
	 * @return Response true if user is examination office, false if not.
	 */
	public static function isExaminationOffice(): bool {
		$user = Auth::user();

		if (!$user) {
			return false;
		}

		return $user->level >= 2;
	}

	/**
	 * Uniformed response for successful login attempts.
	 * 
	 * @return Response HTTP response [200], uid, user level and current semester.
	 */
	private static function loginSuccess(): Response {
		return response([
			'uid' => Auth::user()->uid,
			'level' => Auth::user()->level,
			'currentSemester' => SettingController::getSetting('semester')
		], 200);
	}

	/**
	 * Uniformed response for failed login attempts.
	 * 
	 * @param array $errors Optional. Contains all error messages passed to the client. Default empty.
	 * @return Response HTTP response [401] and optional error messages.
	 */
	private static function loginFailure(array $errors = []): Response {
		if ($errors == []) {
			return response(['currentSemester' => SettingController::getSetting('semester')], 401);
		}

		return response([
			'errors' => $errors
		], 401);
	}
}
