<?php

use App\Repositories\Article\EloquentArticleRepository;

class ArticleTest extends UnitCase {

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb(false);
		$this->repo = new EloquentArticleRepository();
	}

	public function testCreateOrUpdate(){
		$input = ['title' => 'test', 'body' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertfalse($this->repo->createOrUpdate(['title' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->assertTrue($this->repo->createOrUpdate(['title' => 'hej'],1));
		$this->repo->createOrUpdate(['title' => 'test'],1);
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertFalse(is_object($this->repo->get(1)));

		$input = ['title' => 'test', 'body' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue(is_object($this->repo->get(1)));
	}

	public function testDelete(){
		$this->assertFalse($this->repo->delete(1));
		$input = ['title' => 'test', 'body' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->delete(1));
	}

}