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

	public function show(ReadRepository $read, $id, $reply = 0){
		$topic = $this->topic->get($id);
		$reply = $this->reply->get($reply);
		$read->hasRead($id);
		return View::make('topic.show')->with('title', 'Topic: '.$topic->title)->with('topic', $topic)->with('editReply', $reply);
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdate($id = null) {
		$input = Input::all();
		if($this->topic->createOrUpdate(Input::all(), $id)) {
			if(is_null($id)) {
				$input['topic_id'] = $this->topic->topic->id;
				if(!$this->reply->createOrUpdate($input)) {
					return Redirect::to('topics/'.$this->topic->topic->id)->with('success', 'Your topic has been saved, but we could not save your reply, please try agian below.');
				}
			}
			return Redirect::back()->with('success', 'Your topic has been saved.');
		}
		return Redirect::back()->withErrors($this->topic->getErrors())->withInput();
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		if($this->topic->delete($id)) {
			return Redirect::back()->with('success', 'Your forum has been deleted.');
		}

		return Redirect::back();
	}
}