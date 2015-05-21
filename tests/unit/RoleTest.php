<?php

use App\Repositories\Role\EloquentRoleRepository;

class RoleTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb(false);
		$this->repo = new EloquentRoleRepository();
	}

	public function testCreateOrUpdate(){
		$input = ['name' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertfalse($this->repo->createOrUpdate(['name' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['name' => 'hej'],1));
		$this->repo->createOrUpdate(['name' => 'test'],1);
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertFalse(is_object($this->repo->get(1)));

		$input = ['name' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue(is_object($this->repo->get(1)));
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(1));
		$input = ['name' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->delete(1));
	}

	public function testSetDefault(){
		$input = ['name' => 'test', 'default' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$input = ['name' => 'hej'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertFalse($this->repo->setDefault(0));
	}

}