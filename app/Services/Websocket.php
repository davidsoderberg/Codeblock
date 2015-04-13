<?php namespace App\Services;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface {
	protected $clients;

	public function __construct() {
		$this->clients = array();
	}

	public function onOpen(ConnectionInterface $conn) {
		echo "New connection! ({$conn->resourceId})\n";
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		$msg = json_decode($msg, true);

		switch($msg['channel']){
			case 'auth':
				$user = \JWT::decode($msg['token'], env('APP_KEY'));
				if(!array_key_exists($user->id, $this->clients)) {
					$this->clients[$user->id] = $from;
				}
				break;
			case 'welcome':
				$this->clients[$msg['id']]->send(json_encode(array('channel' => 'welcome', 'message' => 'Welcome')));
				break;
		}
	}

	public function onClose(ConnectionInterface $conn) {
		$this->removeConn($conn);
		echo "Connection {$conn->resourceId} has disconnected\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "An error has occurred: {$e->getMessage()}\n";
		$this->removeConn($conn);
		$conn->close();
	}

	private function removeConn(ConnectionInterface $conn){
		if(false !== $key = array_search($conn, $this->clients)){
			unset($this->clients[$key]);
		}
	}
}