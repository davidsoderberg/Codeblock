<?php namespace App\Http\Controllers;

use App\Repositories\Category\CategoryRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller {

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

	public function __construct(CategoryRepository $category)
	{
		parent::__construct();
		$this->category = $category;
	}

	/**
	 * Visar index vyn för kategorier
	 * @permission view_categories
	 * @param  int $id id för kategorin som skall redigera
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index($id = null)
	{
		$category = null;

		if(is_numeric($id)){
			$category = $this->category->get($id);
		}

		return View::make('category.index')->with('title', 'Categories')->with('categories', $this->category->get())->with('category', $category);
	}

	/**
	 * Skapa och uppdatera en kategorin.
	 * @permission create_update_categories
	 * @param  int $id id för kategorin som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function createOrUpdate($id = null)
	{
		if($this->category->createOrUpdate($this->request->all(), $id)){
			if(is_null($id)){
				return Redirect::to('categories')->with('success', 'Your category has been created.');
			}
			return Redirect::to('categories')->with('success', 'Your category has been updated.');
		}

		return Redirect::back()->withErrors($this->category->getErrors())->withInput();
	}

	/**
	 * Ta bort en kategori
	 * @permission delete_categories
	 * @param  int $id id för kategori som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id){
		if($this->category->delete($id)){
			return Redirect::to('categories')->with('success', 'The category has been deleted.');
		}

		return Redirect::back()->with('error', 'The category could not be deleted.');
	}

}