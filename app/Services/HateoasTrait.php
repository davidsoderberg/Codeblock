<?php namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Input;

trait HateoasTrait {

	private $created;

	public function hateoas($id = null, $filterOn = ''){
		$this->created = [];
		$api = 'api/';
		$filterOn = $api.$filterOn;
		$routeArray = array();
		foreach(Route::getRoutes()->getRoutes() as $route){
			if(Str::contains($route->uri(), $filterOn)){
				$url = $this->getRouteUrl($id, $route);
				if(!is_null($url)) {
					$routeArray[] = $url;
				}
			}
		}
		foreach($this->created as $route){
			$url = $this->getRouteUrl(null, $route);
			if(!is_null($url)) {
				$routeArray[] = $url;
			}
		}

		return $this->sort($routeArray);
	}

	private function sort($routeArray = array()){
		$routes = array("GET" => array(), "POST" => array(), "PUT" => array(), "DELETE" => array());

		foreach($routeArray as $route){
			$routes[$route['method']][] = $route;
		}

		return array_merge($routes['GET'], $routes['POST'], $routes['PUT'], $routes['DELETE']);
	}

	/**
	 * @param $id
	 * @param $route
	 * @return array
	 */
	private function getURL($id = null, $route) {
		$method = $route->methods()[0];
		if(Str::contains($route->uri(), 'id')) {
			$url = $route->uri();
			if(is_null($id)){
				$url = str_replace('/{id?}', '', $url);
			}else{
				$url = str_replace('{id}', $id, $url);
				$url = str_replace('{id?}', $id, $url);
				if(Str::contains($route->uri(), 'id?')){
					$this->created[] = $route;
				}
			}
		}else {
			$url =	$route->uri();
		}
		return array('method' => $method, 'uri' => '/'.$url);
	}

	/**
	 * @param $id
	 * @param $route
	 * @return array
	 */
	private function getRouteUrl($id, $route) {
		if(in_array('jwt', $route->middleware())) {
			if(Jwt::auth(Input::get('token'))){
				return $this->getURL($id, $route);
			}
		} else {
			return $this->getURL($id, $route);
		}
		return null;
	}
}