<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Permission
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
		$route = $request->route();
		$actions = $route->getAction();

		if(array_key_exists('permission', $actions)) {
			if (Auth::check() && !Auth::user()->hasPermission($actions['permission'])){
				return Redirect::to('/');
			}else{
				return $next($request);
			}
		}else{
			Throw new \Exception('You have not specified a permission');
		}
    }
}
