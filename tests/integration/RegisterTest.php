<?php

class RegisterTest extends IntegrationCase
{

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb(false);
	}

	/** @test */
	public function it_submits_forms()
	{
		$this->visit('login')
			->submitForm('Sign up', ['username' => 'test' ,'email' => 'test@test.test', 'password' => 'testtest'])
			->andSee('Your user has been created, use the link in the mail to activate your user.')
			->onPage('/login');
	}
}