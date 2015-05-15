<?php

class RegisterTest extends IntegrationCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb(false);
	}

	public function test_sign_up()
	{
		$this->visit('login')
			->submitForm('Sign up', ['username' => 'test' ,'email' => 'test@test.test', 'password' => 'testtest'])
			->andSee('Your user has been created, use the link in the mail to activate your user.')
			->onPage('/login');
	}
}