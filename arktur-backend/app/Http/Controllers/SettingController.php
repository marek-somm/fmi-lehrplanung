<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
	public static function getSetting(string $key) {
		$setting = Setting::where('key', $key)
			->select('key', 'value')
			->get()->first();

		if (!$setting) {
			return null;
		}

		return [$setting['key'] => $setting['value']];
	}

	public function setSetting(Request $request) {
		$request->validate(
			[
				'key' => ['string', 'required'],
				'value' => ['required']
			],
			[
				'key.required' => 'key is required.',
				'value.required' => 'value us required.'
			]
		);

		if (!AuthController::isPrfAmt()) {
			return response(['error' => 'No permissions.',], 403);
		}

		$setting = Setting::where('key', $request->key)
			->select('id', 'key', 'value')
			->get()->first();

		if (!$setting) {
			return response(['error' => 'The key ' . $request->key . ' does not exist.',], 400);
		}

		$id = $setting['id'];

		Setting::where('id', $id)
			->update(['value' => $request->value]);

		$setting = Setting::where('key', $request->key)
			->select('id', 'key', 'value')
			->get()->first();

		return response([$setting['key'] => $setting['value']], 200);
	}

	public function getSemester() {
		$semester = SettingController::getSetting('semester');

		if (!$semester) {
			return response("semester setting not found.", 500);
		}

		return response($semester, 200);
	}
}
