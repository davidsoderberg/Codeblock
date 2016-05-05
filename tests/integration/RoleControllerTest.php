<?php namespace integration;

use Illuminate\Support\Facades\Auth;

class RoleControllerTest extends \IntegrationCase {

	public function setUp() {
		parent::setUp();
		$this->setUpDb();
		Auth::loginUsingId(1);
	}

	public function create_role(){
		$this->visit('roles')
			->submitForm('Create', ['name' => 'test'])
			->see('The role has been created.');
		return $this;
	}

	public function test_create_role(){
		$this->create_role()->seePageIs('roles');
	}

	public function test_edit_role(){
		$this->create_role();

		$this->visit('roles/1')
			->submitForm('Update', ['name' => 'hej'])
			->see('The role has been updated.');
	}

	public function test_set_default(){
		$this->visit('roles')
			->submitForm('Set default', ['default' => 2])
			->see('The default role has been updated.');
	}

	public function test_delete_role(){
		$this->create_role();
		$this->visit('roles/delete/3')
			->see('The role has been deleted.');

		$this->assertEquals(count( \App\Models\Role::all()), 2);
	}

	public function test_delete_none_existing_role(){
		$this->visit('roles/delete/3')
			->see('The role could not be deleted.');

		$this->assertEquals(count( \App\Models\Role::all()), 2);
	}
}