<?php

class MenuControllerTest extends IntegrationCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
	}

	public function test_sign_up()
	{
		$user = ['username' => 'test' ,'email' => 'test@test.test', 'password' => 'testtest'];
		$this->visit('login')
			->submitForm('Sign up', $user)
			->seeInDatabase('users', $this->removeField($user, 'password'))
			->see('Your user has been created, use the link in the mail to activate your user.')
			->onPage('/login');
	}

	public function test_sign_in(){
		$user = ['loginUsername' => 'david', 'loginpassword' => 'test'];
		$this->visit('login')
			->submitForm('Login', $user)
			->see('You have logged in.')
			->onPage('/user');
	}
}