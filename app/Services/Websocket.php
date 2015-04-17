<?php namespace App\Services;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface {

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
				$this->onSubscribe($from, $msg['topic']);
				break;
			case 'unsubscribe':
				$this->onUnSubscribe($from, $msg['topic']);
				break;
			case 'publish':
				foreach($this->topics[$msg['topic']] as $conn){
					if($conn != $from) {
						$conn->send(json_encode(array('channel' => $msg['topic'], 'message' => $msg['message'])));
					}
				}
				break;
		}
	}

	private function onSubscribe(ConnectionInterface $conn, $topic){
		if(!is_array($this->topics[$topic])){
			$this->topics[$topic] = array();
		}
		$this->topics[$topic][] = $conn;
	}

	private function onUnSubscribe(ConnectionInterface $conn, $topic = ''){
		if($topic != ''){
			if(false !== $key = array_search($conn, $this->topics[$topic])){
				unset($this->topics[$topic][$key]);
			}
		}else{
			foreach($this->topics as $topic => $array){
				if(false !== $key = array_search($conn, $array)){
					unset($this->topics[$topic][$key]);
				}
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
	}
}