<?php namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Class Jwt
 * @package App\Services
 */
class Jwt
{

    /**
     * Creating a json web token.
     *
     * @param $payload
     *
     * @return string
     */
    public static function encode($payload)
    {
        $payload += ['exp' => strtotime("+2 hours")];

        return \JWT::encode($payload, env('APP_KEY'));
    }

    /**
     * Decodes json web token.
     *
     * @param $token
     *
     * @return object
     */
    public static function decode($token)
    {
        return \JWT::decode($token, env('APP_KEY'));
    }

    /**
     * Login user with json web token.
     *
     * @param string $token
     *
     * @return bool
     */
    public static function auth($token = '')
    {
        try {
            $user = Self::decode($token);
            Auth::loginUsingId($user->id);
            if (Auth::user()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
