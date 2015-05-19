<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Class Permission
 * Checks if current user has permission for current page.
 * @package App\Http\Middleware
 */
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
		$response = $next($request);
		$actions = $request->route()->getAction();

		// Fetch permission for current controller method.
		if(array_key_exists('permission', $actions)) {
			$permission = $actions['permission'];
		}else{
			$action = explode('@', $actions['uses']);
			$permissionAnnotation = New \App\Services\Annotation\Permission($action[0]);
			$permission = $permissionAnnotation->getPermission($action[1], true);
		}

		// Checks if user has that permission.
		if (Auth::check() && !Auth::user()->hasPermission($permission)){
			return Redirect::to('/')->with('error', 'You do not have the correct permission for that url.');
		}

		return $response;
    }
}
