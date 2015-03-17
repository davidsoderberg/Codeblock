<?php namespace App\Repositories\Notification;

use App\Notification;
use App\NotificationType;
use App\Repositories\CRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;

class EloquentNotificationRepository extends CRepository implements NotificationRepository {

	private $user;

	public function __construct(UserRepository $user){
		$this->user = $user;
	}

	public function get($id = 0){
		if(is_numeric($id) && $id > 0){
			return Forum::find($id);
		}
		return Forum::all();
	}

	public function send($user_id, $type, $object, $subject = null, $body = null) {

		$note = new Notification();

		if(is_numeric($user_id)){
			$note->user_id = $user_id;
			$user = $this->user->get($user_id);
		}else{
			$user = $this->user->getIdByUsername($user_id);
		}
		if(!is_null($user)){
			$this->user = $user;
		}else{
			$this->errors = array('user' => array('That user does not exist.'));
			return false;
		}

		if(isset($type) && NotificationType::isType($type)){
			$note->type = $type;
		}

		if(!is_null($subject) && !is_null($body)){
			$note->subject = $subject;
			$note->body = $body;
		}else{
			if(isset($type) && NotificationType::isType($type)) {
				$note = $this->setSubjectAndBody($note, $object);
			}
		}


		if(is_object($object)){
			$namespaces = explode('\\', get_class($object));
			$object_type = $namespaces[count($namespaces)-1];
			if(class_exists('App\\'.$object_type)) {
				$note->object_id = $object->id;
				$note->object_type = $object_type;
			}
		}

		$note->sent_at = new \DateTime('now');
		$note->from_id = Auth::user()->id;

		if($note->save()){
			return $this->sendNotification($note);
		}else{
			$this->errors = $note::$errors;
			return false;
		}
	}

	private function setSubjectAndBody($notification, $object){
		$from = Auth::user();
		$notification->subject = 'New '.$notification->type;
		switch($notification->type){
			case NotificationType::MENTION:
				$notification->body = 'You have been mention by '.$from->username. ' in a '.strtolower($notification->object_type).' on this ';
				if($notification->object_type == 'Reply') {
					$notification->body .= 'topic.';
				}
				if($notification->object_type == 'Comment') {
					$notification->body .= 'codeblock.';
				}
				break;
			case NotificationType::COMMENT:
				$notification->body = 'You have a new comment on this codeblock.';
				break;
			case NotificationType::FAVOURITE:
				$notification->subject = 'Your codeblock has been '.$notification->type.'d';
				$notification->body = $from->username.' has favourited your codeblock.';
				break;
			case NotificationType::REPLY:
				$notification->body = 'You have a new reply on this topic.';
				break;
			case NotificationType::ROLE:
				$notification->body = $from->role->name.' have given you a role as: '.$this->user->role->name;
				break;
			case NotificationType::STAR:
				$notification->body = $from->username.' has added a star to your codeblock.';
				break;
			case NotificationType::BANNED:
				$notification->subject = 'You have been '.$notification->type;
				$notification->body = $from->role->name.' has '.$notification->type. ' you for some reason, please reply to this email for questions.';
				break;
		}
		return $notification;
	}

	private function sendNotification($notification){
		$data = array('subject' => $notification->subject, 'body' => $notification->body);
		$emailInfo = array('toEmail' => $this->user->email, 'toName' => $this->user->username, 'subject' => $notification->subject);
		if($this->sendEmail('emails.notification', $emailInfo, $data) == 1) {
			return true;
		}
		return false;
	}

	public function delete($id){
		$Notification = Notification::find($id);
		if($Notification == null){
			return false;
		}
		return $Notification->delete();
	}
}