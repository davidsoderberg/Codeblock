<?php namespace App\Services;

use App\Reply;
use App\Repositories\Reply\ReplyRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class PubSub
 * @package App\Services
 */
abstract class PubSub{

	/**
	 *
	 */
	const TOPIC = 'Topic';
	/**
	 *
	 */
	const COMMENT = 'Comment';

	/**
	 * @param $id
	 * @return string
	 */
	public function getTopic($id){
		return self::TOPIC.'.'.$id;
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function getComment($id){
		return self::COMMENT.'.'.$id;
	}

	/**
	 * @param Reply $reply
	 * @param $user_id
	 */
	public function topic($reply, $user_id){
		Auth::loginUsingId($user_id);
		$ReplyRepository = App::make('App\Repositories\Reply\ReplyRepository');
		$reply = $ReplyRepository->get($reply['id']);
		$html = View::make('topic.reply')->with('reply', $reply)->render();
		Auth::logout();
		return $html;
	}

}