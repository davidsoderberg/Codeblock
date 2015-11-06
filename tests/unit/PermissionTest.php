<?php

use App\Repositories\Permission\EloquentPermissionRepository;

class PermissionTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb(false);
		$this->repo = new EloquentPermissionRepository();
	}

	public function testCreateOrUpdate(){
		$input = ['permission' => ''];
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
		$this->repo->errors = null;
		$input = ['permission' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertfalse($this->repo->createOrUpdate(['permission' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$this->assertTrue($this->repo->createOrUpdate(['permission' => 'hej'],1));
		$this->repo->createOrUpdate(['permission' => 'test'],1);
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertFalse(is_object($this->repo->get(1)));

		$input = ['permission' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue(is_object($this->repo->get(1)));
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(1));
		$input = ['permission' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->delete(1));
	}

}