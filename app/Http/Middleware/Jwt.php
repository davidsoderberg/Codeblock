<?php namespace App\Http\Middleware;

use Closure;
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
		if(\App\Services\Jwt::auth($request->get('token'))) {
			return $next($request);
		}
		return Response::json(array('message' => 'We could not authenticate you.'), 401);
    }
}
