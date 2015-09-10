<?php namespace App\Services;

use Illuminate\Support\Facades\Auth;

class Jwt{

	// Creating a json web token.
	public static function encode($payload){
		$payload += array('exp' => strtotime("+2 hours"));
		return \JWT::encode($payload, env('APP_KEY'));
	}

	// Decodes json web token.
	public static function decode($token){
		return \JWT::decode($token, env('APP_KEY'));
	}

	public static function auth($token = ''){
		try {
			$user = Self::decode($token);
			Auth::loginUsingId($user->id);
			if(Auth::user()) {
				return true;
			}
		} catch (\Exception $e){}
		return false;
	}

}