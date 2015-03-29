<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

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
			$user = \JWT::decode(Input::get('token'), env('APP_KEY'));
			Auth::loginUsingId($user->id);
			if(Auth::user()) {
				return $next($request);
			}
		} catch (\Exception $e){
			dd($e);
		}
		return Response::json(array('message' => 'We could not authenticate you.'), 401);
    }
}
