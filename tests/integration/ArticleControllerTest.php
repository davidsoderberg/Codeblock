<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class ArticleControllerTest extends \IntegrationCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_article(){
		$this->visit('blog')
			->submitForm('Send', ['title' => 'test', 'body' => 'test'])
			->see('Your article has been created.');
		return $this;
	}

	public function test_create_article(){
		$this->create_article()->see('test')->onPage('blog');
	}

	public function test_edit_article(){
		$this->create_article();

		$this->visit('blog/1')
			->submitForm('Send', ['title' => 'hej'])
			->see('hej')
			->see('Your article has been updated.')
			->onPage('blog');
	}

	public function test_delete_article(){
		$this->create_article();

		$this->visit('blog/delete/1')
			->see('The Article has been deleted.')
			->onPage('blog');
	}

	public function test_delete_none_existing_article(){
		$this->visit('blog')
			->visit('blog/delete/5')
			->see('The Article could not be deleted.')
			->onPage('blog');
	}

}