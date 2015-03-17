<?php namespace App\Repositories\Rate;

use App\Rate;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;

class EloquentRateRepository extends CRepository implements RateRepository {

	// metod för att kolla om en användare har get en kommentar ett omdöme.
	public function check($id)
	{
		$rate = Rate::where('user_id', '=', Auth::user()->id)->where('comment_id', '=', $id)->first();
		if(!is_null($rate)){
			return $rate->type;
		}
		return false;
	}

	// räknar ut totalen av en kommetars omdöme.
	public function calc($id){
		return count(Rate::where('type', '=', '+')->where('comment_id', '=', $id)->get()) - count(Rate::where('type', '=', '-')->where('comment_id', '=', $id)->get());
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
		$rate = Rate::where('type', '=', $this->typeSwitch($type))->where('user_id', '=', Auth::user()->id)->where('comment_id', '=', $id)->first();
		if(!is_null($rate)){
			return $rate->delete();
		}
		return false;
	}

}