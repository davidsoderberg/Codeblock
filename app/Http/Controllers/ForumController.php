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
		$this->forum = $forum;
	}

	/**
	 * @return mixed
	 */
	public function index() {
		return View::make('forum.index')->with('title', 'Forums')->with('forums', $this->forum->get());
	}

	/**
	 * @return mixed
	 */
	public function listForum(){
		return View::make('forum.list')->with('title', 'Forum')->with('forums', $this->forum->get());
	}

	/**
	 * @param $id
	 */
	public function show($id) {
		$forum = $this->forum->get($id);
		return View::make('forum.show')->with('title', 'Forum: '.$forum->title)->with('forum', $forum);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function create($id) {
		if($id){
			return View::make('forum.create')->with('title', 'update')->with('forum', $this->forum->get($id));
		}
		return View::make('forum.create')->with('title', 'create')->with('forum', null);
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdate($id = null) {
		if($this->forum->createOrUpdate(Input::all(), $id)) {
			return Redirect::back()->with('success', 'Your forum has been saved.');
		}

		return Redirect::back()->withErrors($this->forum->getErrors())->withInput();
	}

	/**
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