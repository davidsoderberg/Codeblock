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
			$method = new \ReflectionMethod($action[0], $action[1]);
			$annotationService = new AnnotationService($action[0], '@permission');
			$permissions = $annotationService->getValues();
			if(count($permissions) > 0 && array_key_exists($method->getName(), $permissions)) {
				$permission = $permissions[$method->getName()];
			}else{
				return $response;
			}
		}

		if (Auth::check() == false || Auth::check() && !Auth::user()->hasPermission($permission)){
			return Redirect::to('/');
		}

		return $next($request);
    }
}
