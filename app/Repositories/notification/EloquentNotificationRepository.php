<?php namespace App\Repositories\Notification;

use App\Notification;
use App\NotificationType;
use App\Repositories\CRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use App\Services\HtmlBuilder;

class EloquentNotificationRepository extends CRepository implements NotificationRepository {

	private $user;

	public function __construct(UserRepository $user){
		$this->user = $user;
	}

	// hämtar en eller alla notifikationer.
	public function get($id = 0){
		if(is_numeric($id) && $id > 0){
			return Notification::find($id);
		}
		return Notification::all();
	}

	public function setRead($user_id){
		$notifications = Notification::where('user_id', $user_id)->get();
		foreach($notifications as $notification){
			$notification->is_read = 1;
			$notification->save();
		}
	}

	// Sätter id på den användare som notifikationen ska till.
	public function setUserId($user_id, $note){
		if(!is_null($this->user->get($this->stripTrim($user_id)))){
			$note->user_id = $user_id;
		}else{
			$this->errors = array('to_user' => array('That user does not exist.'));
			Throw new InvalidParameterException();
		}
		return $note;
	}

	// Sätter vilken typ av notifikationen är.
	public function setType($type, $note){
		if(isset($type) && NotificationType::isType($type)){
			$note->type = $type;
			return $note;
		}
		return $note;
	}

	// Sätter vilken model notifikationen tillhör.
	public function setObject($object, $note){
		if(is_object($object)){
			$namespaces = explode('\\', get_class($object));
			$object_type = $namespaces[count($namespaces)-1];
			if(class_exists('App\\'.$object_type)) {
				$note->object_id = $object->id;
				$note->object_type = $object_type;
			}
		}
		return $note;
	}

	// Sätter notifikationens innehåll.
	public function setcontent($subject, $body, $type, $note){
		if(!is_null($subject) && !is_null($body)){
			$note->subject = $this->stripTrim($subject);
			$note->body = $this->stripTrim($body);
		}else{
			if(isset($type) && NotificationType::isType($type)) {
				$note = $this->setSubjectAndBody($note);
			}
		}
		return $note;
	}

	// Sätter vilket id notifikationen kommer ifrån.
	public function setFromId($from_id, $note){
		if(!is_null($this->user->get($from_id))){
			$note->from_id = $from_id;
		}else{
			$this->errors = array('from_user' => array('That user does not exist.'));
			Throw new InvalidParameterException();
		}
		return $note;
	}

	// Skapar en notifikation.
	public function send($user_id, $type, $object, $subject = null, $body = null) {

		$note = new Notification();

		try{
			$note = $this->setUserId($user_id, $note);
		} catch(InvalidParameterException $e){
			return false;
		}

		try{
			$note = $this->setFromId(Auth::user()->id, $note);
		} catch(InvalidParameterException $e){
			return false;
		}

		$note = $this->setType($type, $note);
		$note = $this->setObject($object, $note);
		$note = $this->setcontent($subject, $body, $type, $note);


		if($note->save()){
			return $this->sendNotification($note);
		}else{
			$this->errors = $note::$errors;
			return false;
		}
	}

	// Hämtar ämne och meddelande för en notifikation.
	public function getSubjectAndBody(Notification $notification){

		try{
			$notification = $this->setUserId($notification->user_id, $notification);
		} catch(InvalidParameterException $e){
			return $notification;
		}

		try{
			$notification = $this->setFromId($notification->from_id, $notification);
		} catch(InvalidParameterException $e){
			return $notification;
		}

		$notification = $this->setType($notification->type, $notification);
		$notification = $this->setObject($notification->object, $notification);
		if(isset($notification->type) && NotificationType::isType($notification->type)) {
			$notification = $this->setSubjectAndBody($notification);
		}

		$notification->save();

		return $notification;
	}

	// Sätter ämne och meddelande för en notifikation.
	private function setSubjectAndBody(Notification $notification){
		$object = $notification->object;
		$from = $this->user->get($notification->from_id);
		$notification->subject = 'New '.$notification->type;
		$html = new HtmlBuilder();
		switch($notification->type){
			case NotificationType::MENTION:
				if($notification->object_type == 'Topic') {
					$type = 'reply';
					$notification->body = $html->actionlink($url = array('action' => 'TopicController@show', 'params' => array($object->id)), 'topic').'.';
				}
				if($notification->object_type == 'Post') {
					$type = 'comment';
					$notification->body = $html->actionlink($url = array('action' => 'PostController@show', 'params' => array($object->id)), 'codeblock').'.';
				}
				$notification->body = 'You have been mention by '.$from->username. ' in a '.$type.' on this '.$notification->body;
				break;
			case NotificationType::COMMENT:
				$notification->body = 'You have a new comment on this '.$html->actionlink($url = array('action' => 'PostController@show', 'params' => array($object->id)), 'codeblock').'.';
				break;
			case NotificationType::FAVOURITE:
				$notification->subject = 'Your codeblock has been '.$notification->type.'d';
				$notification->body = $from->username.' has favourited your topic.';
				break;
			case NotificationType::REPLY:
				$notification->body = 'You have a new reply on this '.$html->actionlink($url = array('action' => 'TopicController@show', 'params' => array($object->id)), 'topic').'.';
				break;
			case NotificationType::ROLE:
				$user = $this->user->get($notification->user_id);
				$notification->body = $from->role->name.' have given you a role as: '.$user->role->name;
				break;
			case NotificationType::STAR:
				$notification->body = $from->username.' has added a star to your '.$html->actionlink($url = array('action' => 'PostController@show', 'params' => array($object->id)), 'codeblock').'.';
				break;
			case NotificationType::BANNED:
				$notification->subject = 'You have been '.$notification->type;
				$notification->body = $from->role->name.' has '.$notification->type. ' you for some reason, please reply to this email for questions.';
				break;
		}
		return $notification;
	}

	// Skickar notifikationen via mejl.
	private function sendNotification(Notification $notification){
		$user = $this->user->get($notification->user_id);
		$data = array('subject' => $notification->subject, 'body' => $notification->body);
		$emailInfo = array('toEmail' => $user->email, 'toName' => $user->username, 'subject' => $notification->subject);
		if($this->sendEmail('emails.notification', $emailInfo, $data)) {
			return true;
		}
		return false;
	}

	// Tar bort en notifikation.
	public function delete($id){
		$Notification = Notification::find($id);
		if($Notification == null){
			return false;
		}
		return $Notification->delete();
	}
}