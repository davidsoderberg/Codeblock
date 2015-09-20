<?php namespace App\Services;


use Ipunkt\LaravelAnalytics\Providers\GoogleAnalytics;

class Analytics{

	private static $categories = array(
		'social',
		'internal',
		'error'
	);
	private static $actions = array(
		'login',
		'create',
		'update',
		'delete',
		'connect',
		'fork'
	);

	const CATEGORY_SOCIAL = 0;
	const CATEGORY_INTERNAL = 1;
	const CATEGORY_ERROR = 2;

	const ACTION_LOGIN = 0;
	const ACTION_CREATE = 1;
	consT ACTION_UPDATE = 2;
	const ACTION_DELETE = 3;
	const ACTION_CONNECT = 4;
	const ACTION_FORK = 5;

	private static function getCategory($int){
		if($int <= (count(Self::$categories) - 1)){
			return Self::$categories[$int];
		}
		throw new \OutOfRangeException("That category does not exist.");
	}

	private static function getAction($int){
		if($int <= (count(Self::$actions) - 1)){
			return Self::$actions[$int];
		}
		throw new \OutOfRangeException("That action does not exist.");
	}

	public static function track($category, $action, $label = null, $value = null){
		if(env('APP_DEBUG')) {
			$analytics = new GoogleAnalytics();
			try {
				$analytics->trackEvent(Self::getCategory($category), Self::getAction($action), $label, $value);
			} catch(\OutOfRangeException $e) {
				$analytics->trackEvent(Self::getCategory(Self::CATEGORY_ERROR), Self::getAction(Self::ACTION_CREATE), 'Track event, '.$e->getMessage());
			}
		}
	}
}