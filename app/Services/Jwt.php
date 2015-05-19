<?php namespace App\Services;

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

}