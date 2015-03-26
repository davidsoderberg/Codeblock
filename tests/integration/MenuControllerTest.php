<?php

class MenuControllerTest extends IntegrationCase {

	public function testWelcomePage(){
		$this->visit('/')->see('WELCOME');
	}

	public function testBrowsePage(){
		$this->visit('browse')->see('HTML');
	}

	public function testContactPage(){
		$this->visit('contact')->see('CONTACT');
	}

	public function testLoginPage(){
		$this->visit('login')->see('LOGIN');
	}

}