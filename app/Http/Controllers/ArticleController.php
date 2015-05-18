<?php namespace App\Http\Controllers;

use App\Repositories\Article\ArticleRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class ArticleController extends Controller {

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

	public function __construct(ArticleRepository $Article)
	{
		parent::__construct();
		$this->Article = $Article;
	}

	/**
	 * Visar index vyn för artikel
	 * @param  int $id id för artikel som skall redigera
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index($id = null)
	{
		$Article = null;

		if(is_numeric($id)){
			$Article = $this->Article->get($id);
		}

		return View::make('article.index')->with('title', 'Articles')->with('articles', $this->Article->get())->with('article', $Article);
	}

	/**
	 * Skapa en artikel.
	 * @permission create_article
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function create()
	{
		if($this->Article->createOrUpdate($this->request->all())){
			return Redirect::action('ArticleController@index')->with('success', 'Your article has been created.');
		}

		return Redirect::back()->withErrors($this->Article->getErrors())->withInput();
	}

	/**
	 * Uppdaterar en artikel.
	 * @permission update_article
	 * @param  int $id id för kategorin som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function update($id)
	{
		if($this->Article->createOrUpdate($this->request->all(), $id)){
			return Redirect::action('ArticleController@index')->with('success', 'Your article has been updated.');
		}

		return Redirect::back()->withErrors($this->Article->getErrors())->withInput();
	}

	/**
	 * Ta bort en artikel
	 * @permission delete_article
	 * @param  int $id id för kategori som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id){
		if($this->Article->delete($id)){
			return Redirect::back()->with('success', 'The Article has been deleted.');
		}

		return Redirect::back()->with('error', 'The Article could not be deleted.');
	}

}