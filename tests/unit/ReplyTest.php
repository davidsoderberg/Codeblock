<?php

use App\Repositories\Reply\EloquentReplyRepository;

class ReplyTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb(true);
		$this->repo = new EloquentReplyRepository();
		$this->be( \App\Models\User::find(1));
	}

	public function createDummy(){
		$input = ['reply' => 'test', 'topic_id' => 1];
		$this->repo->createOrUpdate($input);
	}

	public function testCreateOrUpdate(){
		$input = ['reply' => 'test', 'topic_id' => 1];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertfalse($this->repo->createOrUpdate(['reply' => '', 'topic_id' => 1]));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$this->assertfalse($this->repo->createOrUpdate(['reply' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['reply' => 'hej'],1));
		$this->repo->createOrUpdate(['reply' => 'reply'],1);
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