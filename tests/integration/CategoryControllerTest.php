<?php namespace integration;

use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;

class CategoryControllerTest extends \IntegrationCase {

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		DB::table('categories')->truncate();
		Auth::loginUsingId(1);
	}

	public function create_category(){
		$this->visit('categories')
			->submitForm('Send', ['name' => 'test'])
			->see('Your category has been created.');
		return $this;
	}

	public function test_create_category(){
		$this->create_category()->onPage('categories');
	}

	public function test_edit_category(){
		$this->create_category();

		$this->visit('categories/1')
			->fill('hej','name')
			->press('Send')
			->see('Your category has been updated.')
			->onPage('categories');
	}

	public function test_delete_category(){
		$this->create_category();

		$this->visit('categories/delete/1')
			->see('The category has been deleted.')
			->onPage('categories');
	}

	public function test_delete_none_existing_category(){
		$this->visit('categories')
			->visit('categories/delete/5')
			->see('The category could not be deleted.')
			->onPage('categories');
	}

}