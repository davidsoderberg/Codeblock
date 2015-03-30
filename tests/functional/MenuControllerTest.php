<?php
use Illuminate\Support\Str;

class MenuControllerTest extends FunctionalCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
	}

	public function testIndex(){
		$this->get('/');
		$this->assertResponseOk();
	}

	public function testBrowse(){
		$this->get('/browse');
		$this->assertViewHas('title', 'Browse');
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