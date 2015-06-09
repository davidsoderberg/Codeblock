<?php

use App\Repositories\User\EloquentUserRepository;

class UserTest extends UnitCase {

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
		$this->repo->errors = null;

		$user = $input;
		$user['email'] = '';
		$this->assertFalse($this->repo->createOrUpdate($user));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$user['email'] = 'testtest.com';
		$this->assertFalse($this->repo->createOrUpdate($user));
		$this->assertTrue(is_object($this->repo->getErrors()));

		$this->repo->errors = null;
		$user['email'] = 'test@test.com';
		$user['username'] = '';
		$this->assertFalse($this->repo->createOrUpdate($user));
		$this->assertTrue(is_object($this->repo->getErrors()));

		$this->repo->errors = null;
		$user['password'] = '';
		$user['username'] = 'test';
		$this->assertFalse($this->repo->createOrUpdate($user));
		$this->assertTrue(is_object($this->repo->getErrors()));

		$this->repo->createOrUpdate($input);
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'hej@hej.com', 'active' => 1, 'role' => 1];
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
		$this->repo->errors = null;
		$token = str_replace('/','', md5('test@test.com'));
		$this->repo->activateUser(3, $token);
		$input['username'] = 'hej';
		$input['email'] = 'test@test.com';
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
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
		$user = App\User::find(2);
		$user->active = 1;
		$user->save();
		$input = ['loginUsername' => 'test', 'loginpassword' => 'test'];
		$this->assertTrue($this->repo->login($input));
		$input = ['loginUsername' => 'hej', 'loginpassword' => 'hej'];
		$this->assertFalse($this->repo->login($input));

		$this->assertFalse($this->repo->login(['loginUsername' => '', 'loginpassword' => '']));
		$this->assertFalse($this->repo->login(['loginUsername' => 'test', 'loginpassword' => '']));
		$this->assertFalse($this->repo->login(['loginUsername' => '', 'loginpassword' => 'test']));
	}

	public function testForgotPassword(){
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$user = $this->repo->get(2);
		$this->assertTrue($this->repo->forgotPassword(array('email' => $user->email)));
		$this->assertFalse($this->repo->forgotPassword(array('email' => '')));
		$this->assertFalse($this->repo->forgotPassword(array('email' => 'hej@hej.hej')));
		$this->assertFalse($this->repo->forgotPassword(array('email' => 'hejhej.hej')));
		$this->assertFalse($this->repo->forgotPassword(array('email' => 'hej@hejhej')));
	}

	public function testActivateUser(){
		$input = ['username' => 'test', 'password' => 'test', 'email' => 'test@test.com', 'active' => 1, 'role' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$user = $this->repo->get(2);
		$token = str_replace('/','', md5($user->email));
		$this->assertTrue($this->repo->activateUser($user->id, $token));
		$this->assertFalse($this->repo->activateUser($user->id +1, $token));
		$this->assertFalse($this->repo->activateUser($user->id, 'hej'));
	}

}