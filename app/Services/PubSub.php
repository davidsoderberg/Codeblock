<?php namespace App\Services;

use App\Reply;
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
	public function topic(Reply $reply, $user_id){
		Auth::loginUsingId($user_id);
		$html = View::render('topic.reply')->with('reply', $reply);
		Auth::logout();
		return $html;
	}

}