<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class CommentControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_post() {
		$this->create('App\Post', ['user_id' => 1]);
	}

	public function create_comment(){
		$this->create_post();
		$this->visit('posts/1')
			->submitForm('Comment', ['comment' => 'test'])
			->see('Your comment have been created.');
		return $this;
	}


	public function test_comment_views(){
		$this->visit('comments')->statusCode(200);
		$this->visit('comments/list')->statusCode(200);
	}


	public function test_create_comment(){
		$this->create_comment()->onPage('posts/1');
	}

	public function test_delete_comment(){
		$this->create_comment();

		$this->visit('comments/delete/1')
			->see('That comment has now been deleted.');
	}

	public function test_delete_none_existing_comment(){
		$this->visit('comments/delete/1')
			->see('We could not delete that comment.');
	}

	public function test_edit_comment(){
		$this->create_comment();
		$this->visit('comments/edit/1')
			->submitForm('Edit', ['status' => 1])
			->see('This comment have been updated.');
	}

	public function test_edit_comment_as_user(){
		$this->create_comment();

		$this->visit('posts/1/1')
			->fill('hej','comment')
			->press('Comment')
			->see('This comment have been updated.');
	}

}