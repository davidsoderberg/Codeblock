<?php namespace api;

use App\User;

class AuthTest extends \ApiCase {

	public function test_get_token(){
		$this->post('/api/auth', $this->user)->seeStatusCode(200);
	}

	public function test_register(){
		$this->post('/api/auth/register', ['username' => 'testar', 'password' => 'test', 'email' => 'testar@test.test'])->seeStatusCode(201);
	}

	public function test_forgot_password(){
		$user = User::find(1);
		$this->post('/api/auth/forgot', ['email' => $user->email])->seeStatusCode(200);

		$this->post('/api/auth/forgot', ['email' => ''])->seeStatusCode(400);
	}

}