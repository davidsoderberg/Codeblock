<?php

use Repositories\User\EloquentUserRepository;

class UserTest extends TestCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentUserRepository();
	}

	public function testCreateOrUpdate(){
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertFalse($this->repo->createOrUpdate(['email' => ''],2));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['email' => 'hej@hej.com', 'oldpassword' => 'test', 'password' => 'hej'],2));
		$this->repo->createOrUpdate(['email' => 'test@test.com'],2);
		$this->setExpectedException('Illuminate\Database\QueryException');
		$this->assertFalse($this->repo->createOrUpdate($input));
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertFalse(is_object($this->repo->get(2)));

		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue(is_object($this->repo->get(2)));
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(2));
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->be($this->repo->get(1));
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->delete(2));
	}

	public function testLogin(){
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$user = User::find(2);
		$user->active = 1;
		$user->save();
		$input = ['loginUsername' => 'test', 'loginpassword' => 'test'];
		$this->assertTrue($this->repo->login($input));
		$input = ['loginUsername' => 'hej', 'loginpassword' => 'hej'];
		$this->assertFalse($this->repo->login($input));
	}

	public function testForgotPassword(){
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$user = $this->repo->get(2);
		$this->assertTrue($this->repo->forgotPassword(array('email' => $user->email)));

	}

	public function testActivateUser(){
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$user = $this->repo->get(2);
		$token = str_replace('/','', md5($user->email));
		$this->assertTrue($this->repo->activateUser($user->id, $token));
	}

}