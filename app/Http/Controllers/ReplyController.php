<?php namespace App\Http\Controllers;

use App\Repositories\Reply\ReplyRepository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Support\Facades\Auth;
use App\NotificationType;
use App\Repositories\Read\ReadRepository;

/**
 * Class ReplyController
 * @package App\Http\Controllers
 */
class ReplyController extends Controller {

	/**
	 * @param ReplyRepository $Reply
	 */
	public function __construct(ReplyRepository $Reply) {
		parent::__construct();
		$this->reply = $Reply;
	}

	/**
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdate(ReadRepository $read, NotificationRepository $notification, $id = null) {
		if($this->reply->createOrUpdate(Input::all(), $id)) {
			$reply = $this->reply->Reply;
			if(is_null($id)) {
				$replies = $reply->topic->replies;
				if(Auth::user()->id != $replies->first()->user_id) {
					$notification->send($replies->first()->user_id, NotificationType::REPLY, $reply->topic);
					$this->client->send($reply->topic, $replies->first()->user_id);
				}
				$this->client->send($reply, Auth::user()->id, 'publish', $this->client->getTopic($reply->topic->id));
				$this->mentioned(Input::get('reply'), $reply->topic, $notification);
				$read->UpdatedRead($reply->topic->id);
			}
			return Redirect::action('TopicController@show', array($reply->topic->id))->with('success', 'Your Reply has been saved.');
		}
		return Redirect::back()->withErrors($this->reply->getErrors())->withInput();
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		if($this->reply->delete($id)) {
			return Redirect::back()->with('success', 'Your reply has been deleted.');
		}
		return Redirect::back();
	}
}