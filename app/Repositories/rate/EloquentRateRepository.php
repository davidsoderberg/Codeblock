<?php namespace App\Repositories\Rate;

use App\Models\Rate;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentRateRepository
 * @package App\Repositories\Rate
 */
class EloquentRateRepository extends CRepository implements RateRepository {

	/**
	 * Fetch all rates.
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null
	 */
	private function get(){
		return $this->cache('all', Rate::where('id', '!=', 0));
	}

	/**
	 * Checks if user has give a comment a rate.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function check($id)
	{
		$rate = CollectionService::filter($this->get(), 'user_id', Auth::user()->id);
		$rate = CollectionService::filter($rate, 'comment_id', $id, 'first');
		//$rate = Rate::where('user_id', '=', Auth::user()->id)->where('comment_id', '=', $id)->first();
		if(!is_null($rate)){
			return $rate->type;
		}
		return false;
	}

	/**
	 * Calculates rate for comment.
	 *
	 * @param $id
	 *
	 * @return int
	 */
	public function calc($id){
		$first = CollectionService::filter($this->get(), 'type', '+');
		$first = CollectionService::filter($first, 'comment_id', $id);
		$second = CollectionService::filter($this->get(), 'type', '-');
		$second = CollectionService::filter($second, 'comment_id', $id);
		return count($first) - count($second);
	}

	/**
	 * Creates a rate for a comment.
	 *
	 * @param $id
	 * @param $type
	 *
	 * @return bool
	 */
	public function rate($id, $type){
		if(!$this->delete($id, $type)){
			$rate = new Rate;

			$rate->user_id = Auth::user()->id;
			$rate->comment_id = $id;
			$rate->type = $type;

			return $rate->save();
		}
		return true;
	}

	/**
	 * Change rate type.
	 *
	 * @param $type
	 *
	 * @return string
	 */
	private function typeSwitch($type){
		if($type == '+'){
			return '-';
		}
		return '+';
	}

	/**
	 * Deletes a rate.
	 *
	 * @param $id
	 * @param $type
	 *
	 * @return bool
	 */
	private function delete($id, $type){
		$rate = CollectionService::filter($this->get(), 'type', $this->typeSwitch($type));
		$rate = CollectionService::filter($rate, 'user_id', Auth::user()->id);
		$rate = CollectionService::filter($rate, 'comment_id', $id, 'first');
		if(!is_null($rate)){
			return $rate->delete();
		}
		return false;
	}

}