<?php namespace App\Http\Middleware;

use App\Repositories\Post\PostRepository;
use App\Repositories\User\UserRepository;
use Closure;
use Illuminate\Support\Facades\Session;
use App\Services\Analytics;
use Illuminate\Routing\Router;

class gaTracking {

	private $key;
	private $router;
	private $userRepository;
	private $postRepository;

	/**
	 * @param Router $router
	 */
	public function __construct(Router $router, UserRepository $userRepository, PostRepository $postRepository){
		$this->router = $router;
		$this->userRepository = $userRepository;
		$this->postRepository = $postRepository;
		$this->key = 'previousRoute';
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
		if(Session::has($this->key)){
			switch(Session::get($this->key)){
				case 'PostController@fork':
					Analytics::track(Analytics::CATEGORY_INTERNAL, Analytics::ACTION_FORK);
					break;
				case 'PostController@forkGist':
					if(Session::has('success')){
						Analytics::track(Analytics::CATEGORY_SOCIAL, Analytics::ACTION_FORK);
					}else{
						Analytics::track(Analytics::CATEGORY_ERROR, Analytics::ACTION_FORK, 'Github');
					}
					break;
				case 'UserController@oauth':
					if(Session::has('success')){
						if(Session::has('social_connect')) {
							Analytics::track(Analytics::CATEGORY_SOCIAL, Analytics::ACTION_CONNECT, Session::get('social_connect'));
						}
						if(Session::has('social_login')) {
							Analytics::track(Analytics::CATEGORY_SOCIAL, Analytics::ACTION_LOGIN, Session::get('social_login'));
						}
					}else{
						if(Session::has('social_connect')) {
							Analytics::track(Analytics::CATEGORY_ERROR, Analytics::ACTION_CONNECT, Session::get('social_connect'));
						}
						if(Session::has('social_login')) {
							Analytics::track(Analytics::CATEGORY_ERROR, Analytics::ACTION_LOGIN, Session::get('social_login'));
						}
					}
					Session::forget('social_connect');
					Session::forget('social_login');
					break;
			}
		}
		$action = $this->router->getRoutes()->match($request)->getAction();
		$action = str_replace($action['namespace'].'\\', '', $action['uses']);
		Session::put($this->key,$action);
		return $next($request);
	}

}
