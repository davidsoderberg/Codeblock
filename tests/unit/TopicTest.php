<?php

use App\Repositories\Topic\EloquentTopicRepository;

class TopicTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repo = new EloquentTopicRepository();
	}

	public function createDummy(){
		$input = ['title' => 'test', 'forum_id' => 1];
		$this->repo->createOrUpdate($input);
	}

	public function testCreateOrUpdate(){
		$input = ['title' => 'test', 'forum_id' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$input = ['title' => '', 'forum_id' => 1];
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(is_object($this->repo->getErrors()));
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