<?php namespace App\Repositories\Star;

use App\Star;
use App\Repositories\CRepository;

class EloquentStarRepository extends CRepository implements StarRepository {

	// hämtar en eller alla artikel.
	public function get()
	{
		return $this->cache('all', Star::where('id', '!=', 0));
	}

}