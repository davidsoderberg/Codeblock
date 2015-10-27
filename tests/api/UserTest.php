<?php namespace api;

// 2
class UserTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/users')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$this->post('/api/users/1', ['_method' => 'put', 'email' => 'testaren@test.test'], $this->get_headers())->seeStatusCode(201);
	}

}