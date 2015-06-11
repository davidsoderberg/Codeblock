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
		$this->visit('news')
			->submitForm('Send', ['title' => 'test', 'body' => 'test'])
			->see('Your article has been created.');
		return $this;
	}

	public function test_create_article(){
		$this->create_article()->see('test')->onPage('news');
	}

	public function test_edit_article(){
		$this->create_article();

		$this->visit('news/1')
			->submitForm('Send', ['title' => 'hej'])
			->see('hej')
			->see('Your article has been updated.')
			->onPage('news');
	}

	public function test_delete_article(){
		$this->create_article();

		$this->visit('news/delete/1')
			->see('The Article has been deleted.')
			->onPage('news');
	}

	public function test_delete_none_existing_article(){
		$this->visit('news')
			->visit('news/delete/5')
			->see('The Article could not be deleted.')
			->onPage('news');
	}

}