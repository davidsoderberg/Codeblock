<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * Class Jwt
 * Checks if the json web token is valid.
 * @package App\Http\Middleware
 */
class Jwt
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		try {
			$user = \App\Services\Jwt::decode($request->get('token'));
			Auth::loginUsingId($user->id);
			if(Auth::user()) {
				return $next($request);
			}
		} catch (\Exception $e){
			return Response::json(array('error' => $e->getMessage()), 500);
		}
		return Response::json(array('message' => 'We could not authenticate you.'), 401);
    }
}
