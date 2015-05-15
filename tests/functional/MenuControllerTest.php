<?php namespace functional;

class MenuControllerTest extends \FunctionalCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
	}

	public function testIndex(){
		$this->get('/');
		$this->assertViewHas('title', 'Home');
		$this->assertHtmlHasWord('Welcome');
		$this->assertResponseOk();
	}

	public function testBrowse(){
		$this->get('/browse');
		$this->assertViewHas('title', 'Browse');
		$this->assertHtmlHasWord('php');
		$this->assertResponseOk();
	}

	public function testContact(){
		$this->get('/contact');
		$this->assertViewHas('title', 'Contact');
		$this->assertResponseOk();
	}

	public function testLogin(){
		$this->get('/login');
		$this->assertViewHas('title', 'Login / Sign up');
		$this->assertResponseOk();
	}
}