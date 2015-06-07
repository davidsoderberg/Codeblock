<?php

use App\Repositories\Forum\EloquentForumRepository;

class ForumTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentForumRepository();
	}

	public function createDummy(){
		$input = ['title' => 'test', 'description' => 'test'];
		$this->repo->createOrUpdate($input);
	}

	public function testCreateOrUpdate(){
		$input = ['title' => 'test', 'description' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));

		$this->assertFalse($this->repo->createOrUpdate(['title' => '', 'description' => '']));
		$this->assertTrue(count($this->repo->getErrors()) == 2);
		$this->repo->errors = null;
		$this->assertFalse($this->repo->createOrUpdate(['title' => 'test', 'description' => '']));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
		$this->repo->errors = null;
		$this->assertFalse($this->repo->createOrUpdate(['title' => '', 'description' => 'test']));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
		$this->repo->errors = null;
		$this->assertfalse($this->repo->createOrUpdate(['title' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['title' => 'hej'],1));
		$this->repo->createOrUpdate(['title' => 'test'],1);
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertTrue(count($this->repo->get()) == 0);
		$this->assertFalse(is_object($this->repo->get(1)));
		$this->createDummy();
		$this->assertTrue(is_object($this->repo->get(1)));
		$this->assertTrue(count($this->repo->get()) > 0);
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(1));
		$this->createDummy();
		$this->assertTrue($this->repo->delete(1));
	}

}