<?php namespace App\Http\Middleware;

use App\Services\AnnotationService;
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
		$permission = null;
		$route = $request->route();

		if($route) {
			$actions = $route->getAction();
			if(array_key_exists('permission', $actions)) {
				$permission = $actions['permission'];
			}else{
				Throw new \Exception('You have not specified a permission');
			}
		}else{
			$response = $next($request);
			$Routeaction = $request->route()->getAction()['uses'];
			$action = explode('@', $Routeaction);
			$annotationService = new AnnotationService($action[0], '@permission');
			$permission = $annotationService->getValues($action[1]);
			if($permission != '') {
				$permission = explode(':', $permission);
				$permission = $permission[0];
				if(isset($permission[1]) && strtolower($permission[1]) == 'optional') {
					$permission = '';
				}
			}
		}

		if (Auth::check() == false || Auth::check() && !Auth::user()->hasPermission($permission)){
			return Redirect::to('/');
		}

		return $next($request);
    }
}
