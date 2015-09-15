<?php namespace App\Repositories\Rate;

use App\Rate;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

class EloquentRateRepository extends CRepository implements RateRepository {

	private function get(){
		return $this->cache('all', Rate::where('id', '!=', 0));
	}

	// metod för att kolla om en användare har get en kommentar ett omdöme.
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

	// räknar ut totalen av en kommetars omdöme.
	public function calc($id){
		$first = CollectionService::filter($this->get(), 'type', '+');
		$first = CollectionService::filter($first, 'comment_id', $id);
		$second = CollectionService::filter($this->get(), 'type', '-');
		$second = CollectionService::filter($second, 'comment_id', $id);
		return count($first) - count($second);
	}

	// skapa en kommentars omdöme
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

	// ändrar typ från + till -
	private function typeSwitch($type){
		if($type == '+'){
			return '-';
		}
		return '+';
	}

	// tar bort ett omdöme.
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