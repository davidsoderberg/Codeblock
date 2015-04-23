<?php namespace App\Services;

use InvalidArgumentException;
use WebSocket\ConnectionException;
use App\Services\HtmlBuilder;

class Client extends PubSub {
	private $client;

	public function __construct() {
		$this->client = new \WebSocket\Client("ws://".env('SOCKET_ADRESS').":".env('SOCKET_PORT'));
	}

	public function send($object, $user_id = 0, $channel = 'toast', $topic = ''){
		if($user_id == 0 || !is_numeric($user_id)){
			if(!isset($object->user_id)) {
				Throw new \InvalidArgumentException('The user id is not valid');
			}
			$user_id = $object->user_id;
		}
		try {
			$this->client->send(json_encode(array("channel" => $channel, 'topic' => $topic, 'id' => $user_id, 'message' => $this->checkToast($channel, $topic, $object))));
		} catch (ConnectionException $e){}
	}

	private function checkToast($channel, $topic, $object){
		if($channel != 'toast' ){
			if($topic == ''){
				Throw new \InvalidArgumentException('Topic is not set');
			}
			if($channel != 'subscribe' && $channel != 'publish'){
				Throw new \InvalidArgumentException('Channel has wrong value, value should be subscribe or publish.');
			}
			return $object;
		}
		return $this->getMessage($object);
	}

	private function getMessage($object){
		$message = 'You have a new ';
		$html = new HtmlBuilder();
		switch($this->getObjectName($object)){
			case 'Notification':
				$message .= $html->actionlink($url = array('action' => 'NotificationController@listNotification'), 'notification');
				break;
			case 'Post':
				$message .= 'comment in post: '. $html->actionlink($url = array('action' => 'PostController@show', 'params' => array($object->id)), $object->name);
				break;
			case 'Topic':
				$message .= 'reply in topic: '. $html->actionlink($url = array('action' => 'TopicController@show', 'params' => array($object->id)), $object->title);
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