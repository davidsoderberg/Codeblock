<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class ReplyControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_reply(){
		$this->create('App\Models\Forum');
		$this->create('App\Models\Topic', ['forum_id' => 1]);

		$this->visit('forum/topic/1')
			->submitForm('Reply', ['reply' => 'test'])
			->see('Your Reply has been saved.');
		return $this;
	}

	public function test_create_reply(){
		$this->create_reply()->seePageIs('forum/topic/1');
	}

	public function test_edit_reply(){
		$this->create_reply();
		$this->visit('http://codeblock.dev/forum/topic/1/1')
			->submitForm('Reply', ['reply' => 'hej'])
			->see('Your Reply has been saved.');
	}

	public function test_delete_reply(){
		$this->create_reply();
		$this->create_reply();

		$this->visit('http://codeblock.dev/reply/delete/1')
			->see('Your reply has been deleted.');
	}

	public function test_delete_none_reply(){
		$this->visit('/')->visit('/reply/delete/1')
			->see('Your reply could not be deleted.');
	}
}