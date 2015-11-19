<?php namespace App\Services;


use Illuminate\Support\Facades\Session;
use Irazasyed\LaravelGAMP\Facades\GAMP;

/**
 * Class Analytics
 * @package App\Services
 */
class Analytics {

	/**
	 * Property to store client id in.
	 *
	 * @var string
	 */
	private static $clientId = 'codeblock.se';

	/**
	 * Property to store analytics categories in.
	 *
	 * @var array
	 */
	private static $categories = [
		'social',
		'internal',
		'error',
	];

	/**
	 * Property to store analytics actions in.
	 *
	 * @var array
	 */
	private static $actions = [
		'login',
		'create',
		'update',
		'delete',
		'connect',
		'fork',
	];

	/**
	 * Constant to store category social index in.
	 */
	const CATEGORY_SOCIAL = 0;

	/**
	 * Constant to store category internal index in.
	 */
	const CATEGORY_INTERNAL = 1;

	/**
	 * Constant to store category error index in.
	 */
	const CATEGORY_ERROR = 2;

	/**
	 * Constant to store action login index in.
	 */
	const ACTION_LOGIN = 0;

	/**
	 * Constant to store action create index in.
	 */
	const ACTION_CREATE = 1;

	/**
	 * Constant to store action update index in.
	 */
	const ACTION_UPDATE = 2;

	/**
	 * Constant to store action delete index in.
	 */
	const ACTION_DELETE = 3;

	/**
	 * Constant to store action connect index in.
	 */
	const ACTION_CONNECT = 4;

	/**
	 * Constant to store action fork index in.
	 */
	const ACTION_FORK = 5;

	/**
	 * Fetch analytics category.
	 *
	 * @param $int
	 *
	 * @return mixed
	 */
	private static function getCategory( $int ) {
		if ( $int <= ( count( Self::$categories ) - 1 ) ) {
			return Self::$categories[$int];
		}
		throw new \OutOfRangeException( "That category does not exist." );
	}

	/**
	 * Fetch analytics action.
	 *
	 * @param $int
	 *
	 * @return mixed
	 */
	private static function getAction( $int ) {
		if ( $int <= ( count( Self::$actions ) - 1 ) ) {
			return Self::$actions[$int];
		}
		throw new \OutOfRangeException( "That action does not exist." );
	}

	/**
	 * Parse label.
	 *
	 * @param null $label
	 *
	 * @return null|string
	 */
	private static function parseLabel( $label = null ) {
		if ( is_array( $label ) ) {
			return json_encode( $label );
		}

		return $label;
	}

	/**
	 * Adds track.
	 *
	 * @param $category
	 * @param $action
	 * @param null $label
	 */
	public static function track( $category, $action, $label = null ) {
		if ( !is_null( env( 'TRACKING_ID', null ) ) ) {
			if ( !env( 'APP_DEBUG' ) ) {
				$analytics = GAMP::setClientId( Self::$clientId );
				try {
					$analytics->setEventCategory( Self::getCategory( $category ) )
					          ->setEventAction( Self::getAction( $action ) );
					if ( !is_null( $label ) ) {
						$analytics->setEventLabel( Self::parseLabel( $label ) );
					}
					$analytics->sendEvent();
				} catch( \OutOfRangeException $e ) {
					$analytics->setEventCategory( Self::getCategory( Self::CATEGORY_ERROR ) )
					          ->setEventAction( Self::getAction( Self::ACTION_CREATE ) )
					          ->setEventLabel( 'Track event' )
					          ->setEventValue( $e->getMessage() )
					          ->sendEvent();
				}
			}
		}
	}
}