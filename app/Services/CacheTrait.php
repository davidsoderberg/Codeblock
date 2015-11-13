<?php namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class EloquentCacheTrait
 * @package App\Services
 */
trait CacheTrait {
	/**
	 * Cache duration in minutes; 0 is forever
	 *
	 * @var int
	 */
	protected $cacheForMinutes = 0;

	/**
	 * Enable logging
	 *
	 * @var boolean
	 */
	protected $enableLogging = true;

	/**
	 * Key used to store index of all service keys
	 *
	 * @var string
	 */
	protected $cacheIndexKey = 'cache-index';

	/**
	 * model used to get cache key.
	 *
	 * @var string
	 */
	protected $model = null;

	/**
	 * @var
	 */
	protected $cacheKey = '';

	/**
	 * Retrieve from cache if not empty, otherwise store results
	 * of query in cache
	 *
	 * @param  string $key
	 * @param  Builder $query
	 * @param  string $verb Optional Builder verb to execute query
	 *
	 * @return Collection|Model|array|null
	 */
	protected function cache( $key, Builder $query, $verb = 'get' ) {
		$this->setModel( $query->getModel() );

		$key = $this->getCacheSelector( $key );

		$this->indexKey( $key );

		$fetchData = function () use ( $key, $query, $verb ) {
			$this->log( 'refreshing cache for ' . get_class( $this ) . ' (' . $key . ')' );

			return $query->$verb();
		};

		if ( env( 'CACHE' ) ) {
			if ( $this->cacheForMinutes > 0 ) {
				return Cache::remember( $key, $this->cacheForMinutes, $fetchData );
			}

			return Cache::rememberForever( $key, $fetchData );
		}

		return $fetchData();
	}

	/**
	 * Get items from collection whose properties match a given attribute and value
	 *
	 * @param  Collection $collection
	 * @param  string $attribute
	 * @param  mixed $value
	 *
	 * @return Collection
	 */
	protected function getByAttributeFromCollection( Collection $collection, $attribute, $value = null ) {
		return $collection->filter( function ( $item ) use ( $attribute, $value ) {
			if ( isset( $item->$attribute ) && $value ) {
				return $item->$attribute == $value;
			}

			return false;
		} );
	}

	/**
	 * Get cache key from concrete service
	 *
	 * @return string
	 */
	protected function getCacheKey() {
		return $this->cacheKey;
	}

	/**
	 * Create and get cache selector
	 *
	 * @param  string $id Optional id to suffix base key
	 *
	 * @return string
	 */
	protected function getCacheSelector( $id = null ) {
		return $this->getCacheKey() . ( $id ? '.' . $id : '' );
	}

	/**
	 * Get keys from key inventory
	 *
	 * @return array
	 */
	protected function getKeys() {
		return Cache::get( $this->cacheIndexKey, [] );
	}

	/**
	 * Get model
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	protected function getModel() {
		return $this->model;
	}

	/**
	 * Set model
	 *
	 * @param $model
	 */
	public function setModel( $model ) {
		$this->cacheKey = $this->getClass( $model );
		$this->model = $model;
	}

	/**
	 * @param $object
	 *
	 * @return string
	 */
	protected function getClass( $object ) {
		if ( is_object( $object ) ) {
			$object = get_class( $object );
		}

		return strtolower( str_replace( 'App\\', '', $object ) );
	}

	/**
	 * Get keys for concrete service
	 *
	 * @return array
	 */
	protected function getServiceKeys() {
		$keys = $this->getKeys();
		$serviceKey = $this->getCacheKey();

		if ( !isset( $keys[$serviceKey] ) ) {
			$keys[$serviceKey] = [];
		} elseif ( !is_array( $keys[$serviceKey] ) ) {
			$keys[$serviceKey] = [$keys[$serviceKey]];
		}

		return $keys[$serviceKey];
	}

	/**
	 * Index a given key in the service key inventory
	 *
	 * @param  string $key
	 *
	 * @return void
	 */
	protected function indexKey( $key ) {
		$keys = $this->getServiceKeys();

		array_push( $keys, $key );

		$keys = array_unique( $keys );

		$this->setServiceKeys( $keys );
	}

	/**
	 * Log the message, if enabled
	 *
	 * @param  string $message
	 *
	 * @return void
	 */
	protected function log( $message ) {
		if ( $this->enableLogging ) {
			Log::info( $message );
		}
	}

	/**
	 * Set keys for concrete service
	 *
	 * @param array $keys
	 */
	protected function setServiceKeys( $keys = [] ) {
		$allkeys = $this->getKeys();
		$serviceKey = $this->getCacheKey();

		$allkeys[$serviceKey] = $keys;

		Cache::forever( $this->cacheIndexKey, $allkeys );
	}

	/**
	 * Flush the cache for the concrete service
	 *
	 * @return void
	 */
	public function flushCache( $model = null ) {
		if ( !is_null( $model ) ) {
			$this->setModel( $model );
		}
		$keys = $this->getServiceKeys();

		array_map( function ( $key ) {
			$this->log( 'flushing cache for ' . get_class( $this ) . ' (' . $key . ')' );

			Cache::forget( $key );
		}, $keys );
	}
}