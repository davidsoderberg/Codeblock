<?php namespace App\Services;


use Illuminate\Support\Facades\Session;
use Irazasyed\LaravelGAMP\Facades\GAMP;

class Analytics{

	private static $clientId = 'codeblock.se';

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

	private static function parseLabel($label = null){
		if(is_array($label)){
			return json_encode($label);
		}
		return $label;
	}

	public static function track($category, $action, $label = null){
		if( !is_null(env('TRACKING_ID', null))) {
			if(!env('APP_DEBUG')) {
				$analytics = GAMP::setClientId(Self::$clientId);
				try {
					$analytics->setEventCategory(Self::getCategory($category))
					          ->setEventAction(Self::getAction($action));
					if(!is_null($label)) {
						$analytics->setEventLabel(Self::parseLabel($label));
					}
					$analytics->sendEvent();
				} catch(\OutOfRangeException $e) {
					$analytics->setEventCategory(Self::getCategory(Self::CATEGORY_ERROR))
					          ->setEventAction(Self::getAction(Self::ACTION_CREATE))
					          ->setEventLabel('Track event')
					          ->setEventValue($e->getMessage())
					          ->sendEvent();
				}
			}
		}
	}
}