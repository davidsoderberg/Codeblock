<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Router;
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
	 * Constructor for Permission.
	 *
	 * @param Router $router
	 */
	public function __construct(Router $router){
		$this->router = $router;
	}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
	{
		$action = $this->router->getRoutes()->match($request)->getAction()['uses'];

		// Fetch permission for current controller method.
		$action = explode('@', $action);
		$permissionAnnotation = New \App\Services\Annotation\Permission($action[0], false);
		$permission = $permissionAnnotation->getPermission($action[1], true);

		// Checks if user has that permission.
		if (Auth::check() && !Auth::user()->hasPermission($permission)){
			return Redirect::to('/')->with('error', 'You do not have the correct permission for that url.');
		}

		return $next($request);
    }
}
