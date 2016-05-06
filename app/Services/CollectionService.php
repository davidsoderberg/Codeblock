<?php namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class CollectionService
 * @package App\Services
 */
Class CollectionService
{

	/**
	 * Filter collection.
	 *
	 * @param Collection $collection
	 * @param $property
	 * @param $matching
	 * @param null $verb
	 *
	 * @return Collection|static
	 */
	public static function filter(Collection $collection, $property, $matching, $verb = null)
	{
		$collection = $collection->filter(function ($item) use ($property, $matching) {
			if ($item->$property == $matching) {
				return $item;
			}
		});

		if (!is_null($verb)) {
			return $collection->$verb();
		}
		return $collection;
	}

}