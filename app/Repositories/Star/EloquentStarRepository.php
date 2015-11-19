<?php namespace App\Repositories\Star;

use App\Star;
use App\Repositories\CRepository;

/**
 * Class EloquentStarRepository
 * @package App\Repositories\Star
 */
class EloquentStarRepository extends CRepository implements StarRepository {

	/**
	 * Fetch one or all stars.
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null
	 */
	public function get()
	{
		return $this->cache('all', Star::where('id', '!=', 0));
	}

}