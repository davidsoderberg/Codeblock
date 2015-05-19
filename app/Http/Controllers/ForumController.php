<?php namespace App\Http\Controllers;

use App\Repositories\Forum\ForumRepository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

/**
 * Class ForumController
 * @package App\Http\Controllers
 */
class ForumController extends Controller {

	/**
	 * @param ForumRepository $forum
	 */
	public function __construct(ForumRepository $forum) {
		parent::__construct();
		$this->forum = $forum;
	}

	/**
	 * Listar alla forum i en tabel.
	 * @permission view_forums
	 * @return mixed
	 */
	public function index($id = null)
	{
		$forum = null;

		if(is_numeric($id)){
			$forum = $this->forum->get($id);
		}

		return View::make('forum.index')->with('title', 'Forums')->with('forums', $this->forum->get())->with('forum', $forum);
	}

	/**
	 * Vissar alla forum.
	 * @return mixed
	 */
	public function listForums(){
		return View::make('forum.list')->with('title', 'Forum')->with('forums', $this->forum->get());
	}

	/**
	 * Omdirigerar ett forum.
	 * @param $id
	 * @return mixed
	 */
	public function forumsRedirect($id){
		return Redirect::action('ForumController@show', array('id' => $id));
	}

	/**
	 * Visar ett forum.
	 * @param $id
	 */
	public function show($id) {
		$forum = $this->forum->get($id);
		return View::make('forum.show')->with('title', 'Forum: '.$forum->title)->with('forum', $forum);
	}

	/**
	 * Skapar eller uppdaterar ett forum.
	 * @permission create_update_forums
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdate($id = null) {
		if($this->forum->createOrUpdate($this->request->all(), $id)) {
			return Redirect::back()->with('success', 'Your forum has been saved.');
		}

		return Redirect::back()->withErrors($this->forum->getErrors())->withInput();
	}

	/**
	 * Tar bort ett forum.
	 * @permission delete_forums
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		if($this->forum->delete($id)) {
			return Redirect::back()->with('success', 'Your forum has been deleted.');
		}

		return Redirect::back();
	}
}