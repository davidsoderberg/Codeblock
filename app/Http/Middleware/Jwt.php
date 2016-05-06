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
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (\App\Services\Jwt::auth($request->headers->get('X-Auth-Token')) || Auth::check()) {
			return $next($request);
		}

		return Response::json(['message' => 'We could not authenticate you.'], 401);
	}
}
