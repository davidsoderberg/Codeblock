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
		parent::__construct();
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
	public function createOrUpdate(ReadRepository $read,$id = null) {
		$input = Input::all();
		if($this->topic->createOrUpdate(Input::all(), $id)) {
			if(is_null($id)) {
				$input['topic_id'] = $this->topic->topic->id;
				if(!$this->reply->createOrUpdate($input)) {
					$this->delete($this->topic->topic->id);
					return Redirect::back()->withErrors($this->reply->getErrors())->withInput();
				}
				$read->hasRead($this->topic->topic->id);
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
		$forum_id = $this->topic->get($id)->forum_id;
		if($this->topic->delete($id)) {
			return Redirect::action('ForumController@show', array($forum_id))->with('success', 'Your topic has been deleted.');
		}

		return Redirect::back();
	}
}