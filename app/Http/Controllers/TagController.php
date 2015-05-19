<?php namespace App\Http\Controllers;

use App\Repositories\Tag\TagRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class TagController extends Controller {

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

	public function __construct(TagRepository $tag)
	{
		parent::__construct();
		$this->tag = $tag;
	}

	/**
	 * Visar index vyn för ettiketer
	 * @permission view_tags
	 * @param  int $id id för ettiketen som skall redigera
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index($id = null)
	{
		$tag = null;

		if(is_numeric($id)){
			$tag = $this->tag->get($id);
		}

		return View::make('tag.index')->with('title', 'Tags')->with('tags', $this->tag->get())->with('tag', $tag);
	}

	/**
	 * Skapa och uppdatera en ettiket.
	 * @permission create_update_tags
	 * @param  int $id id för ettiketen som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function createOrUpdate($id = null)
	{
		if($this->tag->createOrUpdate($this->request->all(), $id)){
			if(is_null($id)){
				return Redirect::to('tags')->with('success', 'Your tag has been created.');
			}
			return Redirect::to('tags')->with('success', 'Your tag has been updated.');
		}

		return Redirect::back()->withErrors($this->tag->getErrors())->withInput($this->request->all());
	}

	/**
	 * Ta bort en ettiket
	 * @permission delete_tags
	 * @param  int $id id för ettiketen som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id){
		if($this->tag->delete($id)){
			return Redirect::to('tags')->with('success', 'The tag has been deleted.');
		}

		return Redirect::back()->with('error', 'The tag could not be deleted.');
	}

}