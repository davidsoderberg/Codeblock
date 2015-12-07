<?php namespace api;

use App\Models\User;

class AuthTest extends \ApiCase {

	public function test_get_token(){
		$this->post('/api/v1/auth', $this->user)->seeStatusCode(200);
	}

	public function test_register(){
		$this->post('/api/v1/auth/register', ['username' => 'testar', 'password' => 'test', 'email' => 'testar@test.test'])->seeStatusCode(201);
	}

	public function test_forgot_password(){
		$user = User::find(1);
		$this->post('/api/v1/auth/forgot', ['email' => $user->email])->seeStatusCode(200);

		$this->post('/api/v1/auth/forgot', ['email' => ''])->seeStatusCode(400);
	}

}