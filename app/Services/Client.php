<?php namespace App\Services;

class Client {
	private $client;

	public function __construct() {
		$this->client = new \WebSocket\Client("ws://localhost:8080");
	}

	public function send(){
		$this->client->send(json_encode(array("channel" => "welcome", 'id' => 3)));
	}
}