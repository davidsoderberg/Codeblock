<?php namespace App\Repositories\Notification;

use App\Exceptions\NotANumberException;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Repositories\CRepository;
use App\Repositories\User\UserRepository;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use App\Services\HtmlBuilder;

/**
 * Class EloquentNotificationRepository
 * @package App\Repositories\Notification
 */
class EloquentNotificationRepository extends CRepository implements NotificationRepository {

	/**
	 * Property to store user repository in.
	 *
	 * @var UserRepository
	 */
	private $user;

	/**
	 * Constructor for EloquentNotificationRepository.
	 * Takes a UserRepository as parameter.
	 *
	 * @param UserRepository $user
	 */
	public function __construct( UserRepository $user ) {
		$this->user = $user;
		$this->replyId = 0;
	}

	/**
	 * Fetch one or all notifications.
	 *
	 * @param int $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get( $id = 0 ) {
		if ( is_numeric( $id ) && $id > 0 ) {
			return CollectionService::filter( $this->get(), 'id', $id, 'first' );
		}

		return $this->cache( 'all', Notification::where( 'id', '!=', 0 ) );
	}

	/**
	 * Setter for is_read property on notification object.
	 *
	 * @param $user_id
	 */
	public function setRead( $user_id ) {
		$notifications = CollectionService::filter( $this->get(), 'user_id', $user_id );
		foreach( $notifications as $notification ) {
			$notification->is_read = 1;
			$notification->save();
		}
	}

	/**
	 * Setter for user_id property on notification object.
	 *
	 * @param $user_id
	 * @param $note
	 *
	 * @return mixed
	 */
	private function setUserId( $user_id, $note ) {
		if ( !is_null( $this->user->get( $this->stripTrim( $user_id ) ) ) ) {
			$note->user_id = $user_id;
		} else {
			$this->errors = ['to_user' => ['That user does not exist.']];
			Throw new InvalidParameterException();
		}

		return $note;
	}

	/**
	 * Setter for type property on notification object.
	 *
	 * @param $type
	 * @param $note
	 *
	 * @return mixed
	 */
	private function setType( $type, $note ) {
		if ( isset( $type ) && NotificationType::isType( $type ) ) {
			$note->type = $type;

			return $note;
		}

		return $note;
	}

	/**
	 * Setter for object property on notification object.
	 *
	 * @param $object
	 * @param $note
	 *
	 * @return mixed
	 */
	private function setObject( $object, $note ) {
		if ( is_object( $object ) ) {
			$namespaces = explode( '\\', get_class( $object ) );
			$object_type = $namespaces[count( $namespaces ) - 1];
			if ( class_exists( 'App\\Models\\' . $object_type ) ) {
				$note->object_id = $object->id;
				$note->object_type = $object_type;
			}
		}

		return $note;
	}

	/**
	 * Setter for subject and body property on notification object.
	 *
	 * @param $subject
	 * @param $body
	 * @param $type
	 * @param $note
	 *
	 * @return \App\Models\Notification
	 */
	private function setContent( $subject, $body, $type, $note ) {
		if ( !is_null( $subject ) && !is_null( $body ) ) {
			$note->subject = $this->stripTrim( $subject );
			$note->body = $this->stripTrim( $body );
		} else {
			if ( isset( $type ) && NotificationType::isType( $type ) ) {
				$note = $this->setSubjectAndBody( $note );
			}
		}

		return $note;
	}

	/**
	 * Setter for from_id property on notification object.
	 *
	 * @param $from_id
	 * @param $note
	 *
	 * @return mixed
	 */
	private function setFromId( $from_id, $note ) {
		if ( !is_null( $this->user->get( $from_id ) ) ) {
			$note->from_id = $from_id;
		} else {
			$this->errors = ['from_user' => ['That user does not exist.']];
			Throw new InvalidParameterException();
		}

		return $note;
	}

	/**
	 * Creates an notification object.
	 *
	 * @param $user_id
	 * @param $type
	 * @param $object
	 * @param null $subject
	 * @param null $body
	 *
	 * @return bool
	 */
	public function send( $user_id, $type, $object, $subject = null, $body = null ) {

		$note = new Notification();

		try {
			$note = $this->setUserId( $user_id, $note );
		} catch( InvalidParameterException $e ) {
			return false;
		}

		try {
			$note = $this->setFromId( Auth::user()->id, $note );
		} catch( InvalidParameterException $e ) {
			return false;
		}

		$note = $this->setType( $type, $note );
		$note = $this->setObject( $object, $note );
		$note = $this->setContent( $subject, $body, $type, $note );

		if ( $note->save() ) {
			return $this->sendNotification( $note );
		} else {
			$this->errors = $note::$errors;

			return false;
		}
	}

	/**
	 * Fetch subject and body for a notification.
	 *
	 * @param Notification $notification
	 *
	 * @return \App\Models\Notification|mixed
	 */
	public function getSubjectAndBody( Notification $notification ) {

		try {
			$notification = $this->setUserId( $notification->user_id, $notification );
		} catch( InvalidParameterException $e ) {
			return $notification;
		}

		try {
			$notification = $this->setFromId( $notification->from_id, $notification );
		} catch( InvalidParameterException $e ) {
			return $notification;
		}

		$notification = $this->setType( $notification->type, $notification );
		$notification = $this->setObject( $notification->object, $notification );
		if ( isset( $notification->type ) && NotificationType::isType( $notification->type ) ) {
			$notification = $this->setSubjectAndBody( $notification );
		}

		$notification->save();

		return $notification;
	}

	/**
	 * Setter for subject and body property on notification object.
	 *
	 * @param \App\Models\Notification $notification
	 *
	 * @return \App\Models\Notification
	 */
	private function setSubjectAndBody( Notification $notification ) {
		$object = $notification->object;
		$from = $this->user->get( $notification->from_id );
		$notification->subject = 'New ' . $notification->type;
		$html = new HtmlBuilder();
		switch( $notification->type ) {
			case NotificationType::MENTION:
				if ( $notification->object_type == 'Topic' ) {
					$type = 'reply';
					$notification->body = $html->actionlink( $url = [
							'action' => 'TopicController@show',
							'params' => [$object->id],
						], 'topic' ) . '.';
				}
				if ( $notification->object_type == 'Post' ) {
					$type = 'comment';
					$notification->body = $html->actionlink( $url = [
							'action' => 'PostController@show',
							'params' => [$object->id],
						], 'codeblock' ) . '.';
				}
				$notification->body = 'You have been mention by ' . $from->username . ' in a ' . $type . ' on this ' . $notification->body;
				break;
			case NotificationType::COMMENT:
				$notification->body = 'You have a new comment on this ' . $html->actionlink( $url = [
						'action' => 'PostController@show',
						'params' => [$object->id],
					], 'codeblock' ) . '.';
				break;
			case NotificationType::FAVOURITE:
				$notification->subject = 'Your codeblock has been ' . $notification->type . 'd';
				$notification->body = $from->username . ' has favourited your topic.';
				break;
			case NotificationType::REPLY:
				$notification->body = 'You have a new reply on this ' . $html->actionlink( $url = [
						'action' => 'TopicController@show',
						'params' => [$object->id],
					], 'topic' ) . '.';
				break;
			case NotificationType::ROLE:
				$user = $this->user->get( $notification->user_id );
				$notification->body = $from->role->name . ' have given you a role as: ' . $user->role->name;
				break;
			case NotificationType::STAR:
				$notification->body = $from->username . ' has added a star to your ' . $html->actionlink( $url = [
						'action' => 'PostController@show',
						'params' => [$object->id],
					], 'codeblock' ) . '.';
				break;
			case NotificationType::BANNED:
				$notification->subject = 'You have been ' . $notification->type;
				$notification->body = $from->role->name . ' has ' . $notification->type . ' you for some reason, please reply to this email for questions.';
				break;
		}

		return $notification;
	}

	/**
	 * Sends a notification by mail.
	 *
	 * @param \App\Models\Notification $notification
	 *
	 * @return bool
	 */
	private function sendNotification( Notification $notification ) {
		$user = $this->user->get( $notification->user_id );
		$data = ['subject' => $notification->subject, 'body' => $notification->body];
		$emailInfo = ['toEmail' => $user->email, 'toName' => $user->username, 'subject' => $notification->subject];
		if ( $this->sendEmail( 'emails.notification', $emailInfo, $data ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Deletes a notification.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete( $id ) {
		$Notification = $this->get( $id );
		if ( $Notification == null ) {
			return false;
		}

		return $Notification->delete();
	}
}