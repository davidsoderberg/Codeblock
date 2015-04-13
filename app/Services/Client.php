<?php namespace App\Services;

use WebSocket\ConnectionException;

class Client {
	private $client;

	public function __construct() {
		$this->client = new \WebSocket\Client("ws://localhost:8080");
	}

	public function send($object, $user_id = 0){
		if($user_id == 0){
			$user_id = $object->user_id;
		}
		try {
			$this->client->send(json_encode(array("channel" => "toast", 'id' => $user_id, 'message' => $this->getMessage($object))));
		} catch (ConnectionException $e){}
	}

	private function getMessage($object){
		$message = 'You have a new ';
		switch($this->getObjectName($object)){
			case 'Notification':
				$message += HTML::actionlink($url = array('action' => 'NotificationController@listNotification'), 'notification');
				break;
			case 'Post':
				$message += 'comment in post: '+ HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($object->id)), $object->name);
				break;
			case 'Topic':
				$message += 'reply in topic: '+ HTML::actionlink($url = array('action' => 'TopicController@show', 'params' => array($object->id)), $object->name);
				break;
		}
		return $message;
	}

	private function getObjectName($object){
		if(is_object($object)){
			$namespaces = explode('\\', get_class($object));
			$object_type = $namespaces[count($namespaces)-1];
			if(class_exists('App\\'.$object_type)) {
				return $object_type;
			}
		}
		return '';
	}
}