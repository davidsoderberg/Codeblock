<?php

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiCase extends TestCase {

	use TestTrait, DatabaseTransactions;

	protected $baseUrl = 'http://localhost';

	private $token;

	public function setUp(){
		parent::setUp();
		$this->resetEvents();
		$this->setUpDb();
	}

	private function get_token(){
		$response = $this->post('/api/v1/auth', $this->user)->seeStatusCode(200);
		$response = json_decode($response->response->getContent());
		return $response->token;
	}

	protected function get_headers($headers = []){
		return array_merge($headers, ['X-Auth-Token' => $this->get_token()]);
	}

}