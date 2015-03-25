<?php namespace App\Http\Controllers;

use App\Repositories\Reply\ReplyRepository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

/**
 * Class ReplyController
 * @package App\Http\Controllers
 */
class ReplyController extends Controller {

	/**
	 * @param ReplyRepository $Reply
	 */
	public function __construct(ReplyRepository $Reply) {
		$this->Reply = $Reply;
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdate($id = null) {
		if($this->Reply->createOrUpdate(Input::all(), $id)) {
			return Redirect::back()->with('success', 'Your Reply has been saved.');
		}
		return Redirect::back()->withErrors($this->Reply->getErrors())->withInput();
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		if($this->forum->delete($id)) {
			return Redirect::back()->with('success', 'Your reply has been deleted.');
		}

		return Redirect::back();
	}
}