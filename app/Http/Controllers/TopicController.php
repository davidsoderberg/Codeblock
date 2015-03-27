<?php namespace App\Http\Controllers;

use App\Repositories\Read\ReadRepository;
use App\Repositories\Reply\ReplyRepository;
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
	public function __construct(TopicRepository $topic, ReplyRepository $reply) {
		$this->topic = $topic;
		$this->reply = $reply;
	}

	public function show(ReadRepository $read, $id){
		$topic = $this->topic->get($id);
		$read->hasRead($id);
		return View::make('topic.show')->with('title', 'Topic: '.$topic->title)->with('topic', $topic);
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdate(ReadRepository $read, $id = null) {
		$input = Input::all();
		if($this->topic->createOrUpdate(Input::all(), $id)) {
			$input['topic_id'] = $this->topic->topic->id;
			if($this->reply->createOrUpdate($input)) {
				$read->UpdatedRead($input['topic_id']);
				return Redirect::back()->with('success', 'Your topic has been saved.');
			}
			return Redirect::to('topics/'.$this->topic->topic->id)->with('success', 'Your topic has been saved, but we could not save your reply, please try agian below.');
		}
		return Redirect::back()->withErrors($this->topic->getErrors())->withInput();
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