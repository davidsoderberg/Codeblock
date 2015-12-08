<?php namespace App\Services;

use App\Models\Forum;
use App\Models\User;
use App\Models\Model;
use App\Models\Topic;
use App\Models\Reply;
use App\Models\Post;
use App\Models\Notification;

class Transformer{

	private static function toArray(&$object){
		if(!is_array($object) && $object instanceof Model){
			$object = $object->toArray();
		}
	}

	public static function userTransformer(User $user, $parent = false){
		if(!$parent){
			$parent = 'user';
		}

		$teams = $user->teams;
		$role = $user->roles;

		self::toArray($user);

		unset($user['email']);
		unset($user['rolename']);
		unset($user['role']);
		unset($user['active']);
		unset($user['paid']);
		unset($user['alerted']);
		unset($user['updated_at']);

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

}