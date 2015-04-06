<?php namespace App\Http\Middleware;

use App\Services\PermissionAnnotation;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

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
		if(array_key_exists('permission', $actions)) {
			$permission = $actions['permission'];
		}else{
			$action = explode('@', $actions['uses']);
			$permissionAnnotation = New PermissionAnnotation($action[0], $action[1]);
			$permission = $permissionAnnotation->getPermission(true);
		}

		if (Auth::check() && !Auth::user()->hasPermission($permission)){
			return Redirect::to('/');
		}

		return $response;
    }
}
