<?php namespace App\Http\Controllers;

use App\Repositories\Topic\TopicRepository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

/**
 * Class TopicController
 * @package App\Http\Controllers
 */
class TopicController extends Controller {

	/**
	 * @param TopicRepository $topic
	 */
	public function __construct(TopicRepository $topic) {
		$this->topic = $topic;
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