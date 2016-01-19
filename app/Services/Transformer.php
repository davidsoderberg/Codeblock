<?php namespace App\Services;

use App\Models\Forum;
use App\Models\Role;
use App\Models\User;
use App\Models\Model;
use App\Models\Topic;
use App\Models\Reply;
use App\Models\Post;
use App\Models\Notification;
use App\Models\Team;
use App\Models\Category;
use App\Models\Tag;

/**
 * Class Transformer
 * @package App\Services
 */
class Transformer {

	/**
	 * Walks through an collection or array and transforms models to array.
	 *
	 * @param $items
	 */
	public static function walker( &$items ) {
		if ( !is_array( $items ) && !$items instanceof Collection ) {
			$items = [$items];
		}

		if ( $items instanceof Collection ) {
			$items->values();
		} else {
			$items = array_values( $items );
		}

		switch( get_class( $items[0] ) ) {
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
			case Role::class:
				$method = 'roleTransformer';
				break;
			case Tag::class:
				$method = 'tagTransformer';
				break;
			default:
				$method = '';
				break;
		}

		if ( $method != '' ) {
			for( $i = 0; $i < count( $items ); $i++ ) {
				$items[$i] = self::$method( $items[$i] );
			}
			if ( count( $items ) == 1 ) {
				$items = $items[0];
			}
		}
	}

	/**
	 * Transforms model to array.
	 *
	 * @param $object
	 */
	private static function toArray( &$object ) {
		if ( !is_array( $object ) && $object instanceof Model ) {
			$object = $object->toArray();
		}

		self::unsetKeys( $object );
	}

	/**
	 * Remove values on given keys.
	 *
	 * @param array $object to remove values from.
	 * @param array $keys for value to remove.
	 */
	private static function unsetKeys( &$object, $keys = [] ) {
		if ( empty( $keys ) ) {
			$keys = ['created_at', 'updated_at'];
		}
		if ( is_array( $object ) ) {
			foreach( $keys as $key ) {
				if ( array_key_exists( $key, $object ) ) {
					unset( $object[$key] );
				}
			}
		}
	}

	/**
	 * Removes empty values from given places.
	 *
	 * @param array $model to remove values from.
	 * @param array $keys for values to remove.
	 */
	private static function unsetEmpty( &$model, $keys = [] ) {
		if ( is_array( $model ) ) {
			if ( empty( $keys ) ) {
				$keys = array_keys( $model );
			}
			foreach( $keys as $key ) {
				if ( empty( $model[$key] ) ) {
					unset( $model[$key] );
				}
			}
		}
	}

	/**
	 * Transform user model to array.
	 *
	 * @param User $user
	 * @param bool|false $parent
	 *
	 * @return User
	 */
	public static function userTransformer( User $user, $parent = false ) {
		if ( !$parent ) {
			$parent = 'user';
		}

		$role = self::roleTransformer( $user->roles );
		if ( $parent != 'team' ) {
			$teams = $user->teams;
			for( $i = 0; $i < count( $teams ); $i++ ) {
				$teams[$i] = self::teamTransformer( $teams[$i] );
			}
		}

		self::toArray( $user );

		if ( $parent == 'team' || empty( $user['teams'] ) ) {
			unset( $user['teams'] );
		} else {
			$user['teams'] = $teams;
		}

		if ( empty( $user['links'] ) ) {
			unset( $user['links'] );
		}
		unset( $user['roles'] );
		unset( $user['email'] );
		unset( $user['rolename'] );
		unset( $user['role'] );
		unset( $user['active'] );
		unset( $user['paid'] );
		unset( $user['alerted'] );
		unset( $user['updated_at'] );
		$user['role'] = $role;

		return $user;
	}

	/**
	 * Transform forum model to array.
	 *
	 * @param Forum $forum
	 * @param bool|false $parent
	 *
	 * @return Forum
	 */
	public static function forumTransformer( Forum $forum, $parent = false ) {
		if ( !$parent ) {
			$parent = 'forum';
		}

		$topics = $forum->topics;
		for( $i = 0; $i < count( $topics ); $i++ ) {
			$topics[$i] = self::topicTransformer( $topics[$i], $parent );
		}

		self::toArray( $forum );

		if ( empty( $forum['links'] ) ) {
			unset( $forum['links'] );
		}

		return $forum;

	}

	/**
	 * Transform topic model to array.
	 *
	 * @param Topic $topic
	 * @param bool|false $parent
	 *
	 * @return Topic
	 */
	public static function topicTransformer( Topic $topic, $parent = false ) {
		if ( !$parent ) {
			$parent = 'topic';
		}

		$replies = $topic->replies;
		for( $i = 0; $i < count( $replies ); $i++ ) {
			$replies[$i] = self::replyTransformer( $replies[$i], 'topic' );
		}

		self::toArray( $topic );

		$topic['replies'] = $replies;

		if ( $parent == 'forum' ) {
			unset( $topic['forumtitle'] );
			unset( $topic['forum_id'] );
		}

		if ( empty( $topic['links'] ) ) {
			unset( $topic['links'] );
		}

		return $topic;
	}

	/**
	 * Transform reply model to array.
	 *
	 * @param Reply $reply
	 * @param bool|false $parent
	 *
	 * @return Reply
	 */
	public static function replyTransformer( Reply $reply, $parent = false ) {
		if ( !$parent ) {
			$parent = 'reply';
		}

		$user = self::userTransformer( $reply->user );

		self::toArray( $reply );

		$reply['user'] = $user;

		if ( $parent == 'topic' ) {
			unset( $reply['topic_id'] );
		}
		if ( empty( $reply['links'] ) ) {
			unset( $reply['links'] );
		}

		unset( $reply['user_id'] );
		unset( $reply['username'] );

		return $reply;
	}

	/**
	 * Transform post model to array.
	 *
	 * @param Post $post
	 * @param bool|false $parent
	 *
	 * @return Post
	 */
	public static function postTransformer( Post $post, $parent = false ) {
		if ( !$parent ) {
			$parent = 'post';
		}

		$tags = $post->tags;
		for($i = 0; $i < count($tags); $i++){
			$tags[$i] = self::tagTransformer($tags[$i]);
		}
		$category = self::categoryTransformer($post->category);
		$user = self::userTransformer( $post->user );

		self::toArray( $post );

		if ( empty( $post['links'] ) ) {
			unset( $post['links'] );
		}
		unset( $post['categoryname'] );
		unset( $post['cat_id'] );
		unset( $post['user_id'] );
		$post['user'] = $user;
		$post['category'] = $category;
		$post['tags'] = $tags;

		return $post;
	}

	/**
	 * Transform notification model to array.
	 *
	 * @param Notification $notification
	 * @param bool|false $parent
	 *
	 * @return Notification
	 */
	public static function notificationTransformer( Notification $notification, $parent = false ) {
		self::toArray( $notification );

		unset( $notification['type'] );
		if ( empty( $notification['links'] ) ) {
			unset( $notification['links'] );
		}

		return $notification;
	}

	/**
	 * Transform team model to array.
	 *
	 * @param Team $team
	 * @param bool|false $parent
	 *
	 * @return Team
	 */
	public static function teamTransformer( Team $team, $parent = false ) {
		if ( !$parent ) {
			$parent = 'team';
		}

		$users = $team->users;
		for( $i = 0; $i < count( $users ); $i++ ) {
			$users[$i] = self::userTransformer( $users[$i], 'team' );
		}

		$owner = self::userTransformer( $team->owner, 'team' );

		self::toArray( $team );

		unset( $team['owner_id'] );
		$team['owner'] = $owner;

		self::unsetEmpty( $team );

		return $team;
	}

	/**
	 * Transform role model to array.
	 *
	 * @param Role $role
	 * @param bool|false $parent
	 *
	 * @return Role
	 */
	public static function roleTransformer( Role $role, $parent = false ) {

		self::toArray( $role );

		self::unsetEmpty( $role, ['links'] );

		unset($role['grade']);

		return $role;
	}

	/**
	 * Transform category model to array.
	 *
	 * @param Category $category
	 * @param bool|false $parent
	 *
	 * @return Category
	 */
	public static function categoryTransformer( Category $category, $parent = false ) {

		self::toArray( $category );

		self::unsetEmpty( $category, ['links'] );

		self::unsetKeys($category);

		return $category;
	}

	/**
	 * Transform tag model to array.
	 *
	 * @param Tag $tag
	 * @param bool|false $parent
	 *
	 * @return Tag
	 */
	public static function tagTransformer( Tag $tag, $parent = false ) {

		self::toArray( $tag );

		self::unsetEmpty( $tag, ['links'] );

		self::unsetKeys($tag);

		return $tag;
	}

}