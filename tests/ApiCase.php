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

	protected $user = ['username' => 'david', 'password' => 'test'];

	public function removeField(array $data, $fields){
		if(!is_array($fields)){
			$fields = array($fields);
		}

		foreach($fields as $field){
			unset($data[$field]);
		}
		return $data;
	}

	public function create($model, array $overrides = [], $numbers = 1){
		return factory($model)->times($numbers)->create($overrides);
	}

	private function get_token(){
		$response = $this->post('/api/auth', $this->user)->seeStatusCode(200);
		$response = json_decode($response->response->getContent());
		return $response->token;
	}

	protected function get_headers($headers = []){
		return array_merge($headers, ['X-Auth-Token' => $this->get_token()]);
	}

}