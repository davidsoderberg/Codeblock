<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class TopicControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_topic(){
		$this->create('App\Forum');

		$this->visit('forum/1')
			->submitForm('Create', ['title' => 'test', 'reply' => 'test'])
			->see('Your topic has been created.');
		return $this;
	}

	public function test_creat_topic(){
		$this->create_topic()->onPage('forum/1');
	}

	public function test_edit_topic(){
		$this->create_topic();

		$this->visit('forum/topic/1')
			->submitForm('Update topic title', ['title' => 'hej'])
			->see('hej')
			->see('Your topic has been updated.');
	}

	public function test_delete_topic(){
		$this->create_topic();

		$this->visit('topics/delete/1')
			->see('Your topic has been deleted.');
	}

	public function test_delete_none_topic(){
		$this->visit('topics/delete/1')
			->see('That topic could not be deleted.');
	}
}