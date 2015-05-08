<?php namespace App\Services;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket extends PubSub implements MessageComponentInterface {

	private $clients = array();
	private $connections = array();
	private $topics = array();

	public function onOpen(ConnectionInterface $conn) {
		$this->connections[] = $conn;
		echo "New connection! ({$conn->resourceId})\n";
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		$msg = json_decode($msg, true);

		switch($msg['channel']){
			case 'auth':
				$user = Jwt::decode($msg['token']);
				if(!array_key_exists($user->id, $this->clients)) {
					$this->clients[$user->id] = $from;
				}
				break;
			case 'toast':
				if(array_key_exists($msg['id'], $this->clients)) {
					$this->clients[$msg['id']]->send(json_encode(array('channel' => 'toast', 'message' => $msg['message'])));
				}
				break;
			case 'broadcast':
				foreach($this->connections as $conn){
					if($conn != $from) {
						$conn->send(json_encode(array('channel' => 'toast', 'message' => $msg['message'])));
					}
				}
				break;
			case 'subscribe':
				$this->onSubscribe($msg['id'], $msg['topic']);
				break;
			case 'unsubscribe':
				$this->onUnSubscribe($from, $msg['topic']);
				break;
			case 'publish':
				if(isset($this->topics[$msg['topic']])) {
					foreach($this->topics[$msg['topic']] as $id) {
						$conn = $this->clients[$id];
						$msg = $this->getPublish($msg, $id);
						$conn->send(json_encode(array('channel' => $msg['topic'], 'message' => $msg['html'])));
					}
				}
				break;
		}
	}

	private function getPublish($msg, $user_id){
		$msg['topic'] = explode('.',$msg['topic'])[0];
		switch($msg['topic']){
			case self::TOPIC:
				$msg['html'] = $this->topic($msg['message'], $user_id);
				break;
		}
		return $msg;
	}

	private function onSubscribe($id, $topic){
		if(!isset($this->topics[$topic])){
			$this->topics[$topic] = array();
		}
		$this->topics[$topic][] = $id;
	}

	private function onUnSubscribe(ConnectionInterface $conn, $topic = ''){
		if(false !== $id = array_search($conn, $this->clients)) {
			if($topic != '') {
				$this->removeFromTopic($topic, $id, $this->topics[$topic]);
			} else {
				foreach($this->topics as $topic => $array) {
					$this->removeFromTopic($topic, $id, $array);
				}
			}
		}
	}

	private function removeFromTopic($topic, $id, $array){
		if(false !== $key = array_search($id, $array)) {
			unset($this->topics[$topic][$key]);
			if(empty($this->topics[$topic])){
				unset($this->topics[$topic]);
			}
		}
	}

	public function onClose(ConnectionInterface $conn) {
		$this->onUnSubscribe($conn);
		$this->removeConn($conn);
		echo "Connection {$conn->resourceId} has disconnected\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "An error has occurred: {$e->getMessage()}\n";
		$conn->close();
	}

	private function removeConn(ConnectionInterface $conn){
		if(false !== $key = array_search($conn, $this->clients)){
			unset($this->clients[$key]);
		}
		if(false !== $key = array_search($conn, $this->connections)){
			unset($this->connections[$key]);
		}
	}
}