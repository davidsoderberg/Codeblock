<?php namespace App\Services;

use App\Models\Forum;
use App\Models\User;
use App\Models\Model;
use App\Models\Topic;
use App\Models\Reply;
use App\Models\Post;
use App\Models\Notification;
use App\Models\Team;

class Transformer{

	public static function walker(&$items){
		if(!is_array($items) && !$items instanceof Collection){
			$items = array($items);
		}

		if($items instanceof Collection){
			$items->values();
		}else{
			$items = array_values($items);
		}

		switch(get_class($items[0])){
			case User::class:
				$method = 'userTransformer';
				break;
			case Forum::class:
				$method = 'forumTransformer';
				break;
			case Notification::class:
				$method = 'notificationTransformer';
				break;
			case Post::class:
				$method = 'postTransformer';
				break;
			case Topic::class:
				$method = 'topicTransformer';
				break;
			default:
				$method = '';
				break;
		}

		if($method != '') {
			for( $i = 0; $i < count( $items ); $i++ ) {
				$items[$i] = self::$method( $items[$i] );
			}
			if(count($items) == 1){
				$items = $items[0];
			}
		}
	}

	private static function toArray(&$object){
		if(!is_array($object) && $object instanceof Model){
			$object = $object->toArray();
		}
	}

	public static function userTransformer(User $user, $parent = false){
		if(!$parent){
			$parent = 'user';
		}

		$role = $user->roles;
		if($parent != 'team') {
			$teams = $user->teams;
			for( $i = 0; $i < count( $teams ); $i++ ) {
				$teams[$i] = self::teamTransformer( $teams[$i] );
			}
		}

		self::toArray($user);

		if($parent == 'team') {
			unset( $user['teams'] );
		}else{
			$user['teams'] = $teams;
		}

		unset($user['roles']);
		unset($user['email']);
		unset($user['rolename']);
		unset($user['role']);
		unset($user['active']);
		unset($user['paid']);
		unset($user['alerted']);
		unset($user['updated_at']);
		$user['role'] = $role;

		return $user;
	}

	public static function forumTransformer(Forum $forum, $parent = false){
		if(!$parent){
			$parent = 'forum';
		}

		$topics = $forum->topics;
		for($i = 0; $i < count($topics); $i++){
			$topics[$i] = self::topicTransformer($topics[$i], $parent);
		}

		self::toArray($forum);

		return $forum;

	}

	public static function topicTransformer(Topic $topic, $parent = false){
		if(!$parent){
			$parent = 'topic';
		}

		$replies = $topic->replies;
		for($i = 0; $i < count($replies); $i++){
			$replies[$i] = self::replyTransformer($replies[$i], 'topic');
		}

		self::toArray($topic);

		$topic['replies'] = $replies;

		if($parent == 'forum'){
			unset($topic['forumtitle']);
			unset($topic['forum_id']);
		}

		return $topic;
	}

	public static function replyTransformer(Reply $reply, $parent = false){
		if(!$parent){
			$parent = 'reply';
		}

		$user = self::userTransformer($reply->user);

		self::toArray($reply);

		$reply['user'] = $user;

		if($parent == 'topic'){
			unset($reply['topic_id']);
		}

		unset($reply['user_id']);
		unset($reply['username']);

		return $reply;
	}

	public static function postTransformer(Post $post, $parent = false){
		if(!$parent){
			$parent = 'post';
		}

		$tags = $post->tags;
		$category = $post->category;
		$user = self::userTransformer($post->user);

		self::toArray($post);

		unset($post['categoryname']);
		unset($post['cat_id']);
		unset($post['user_id']);
		$post['user'] = $user;

		return $post;
	}

	public static function notificationTransformer(Notification $notification, $parent = false){
		self::toArray($notification);

		unset($notification['type']);

		return $notification;
	}

	public static function teamTransformer(Team $team, $parent = false){
		if(!$parent){
			$parent = 'team';
		}

		$users = $team->users;
		for($i = 0; $i < count($users); $i++){
			$users[$i] = self::userTransformer($users[$i], 'team');
		}

		$owner = self::userTransformer($team->owner, 'team');

		self::toArray($team);

		unset($team['owner_id']);
		$team['owner'] = $owner;

		return $team;
	}

}