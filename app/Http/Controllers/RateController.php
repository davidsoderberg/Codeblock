<?php namespace App\Http\Controllers;

use App\Repositories\Rate\RateRepository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class RateController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'PostController@index');
	|
	*/

	public function __construct(RateRepository $rate)
	{
		$this->rate = $rate;
	}

	/**
	 * lägger till en + på en kommentar
	 * @param  int $id id på kommentaren som skall få +.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function plus($id){
		if($this->rate->rate($id, '+')){
			return Redirect::back();
		}
		return Redirect::back()->with('error', 'You could not rate that comment, please try agian');
	}

	/**
	 * lägger till en - på en kommentar
	 * @param  [type] $id id på kommentaren som skall få -.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function minus($id){
		if($this->rate->rate($id, '-')){
			return Redirect::back();
		}
		return Redirect::back()->with('error', 'You could not rate that comment, please try agian');
	}

}