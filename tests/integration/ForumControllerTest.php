<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class ForumControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_forum(){
		$this->visit('forums')
			->submitForm('Send', ['title' => 'test', 'description' => 'test'])
			->see('Your forum has been created.');
		return $this;
	}

	public function test_create_forum(){
		$this->create_forum()->onPage('forums');
	}

	public function test_edit_forum(){
		$this->create_forum();

		$this->visit('forums')
			->click('Edit')
			->submitForm('Send', ['title' => 'hej'])
			->see('hej')
			->see('Your forum has been updated.');
	}

	public function test_delete_forum(){
		$this->create_forum();

		$this->visit('forums')
			->click('Delete')
			->see('Your forum has been deleted.');
	}

	public function test_view_forum(){
		$this->create_forum();

		$this->visit('forums')
			->click('View')
			->see('<h2>test</h2>');
	}
}