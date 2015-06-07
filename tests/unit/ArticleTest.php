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
		$input = ['title' => '', 'body' => ''];
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->getErrors()) == 2);
		$this->repo->errors = null;
		$input = ['title' => '', 'body' => 'hej'];
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
		$this->repo->errors = null;
		$input = ['title' => 'test', 'body' => ''];
		$this->assertFalse($this->repo->createOrUpdate($input));
		$this->assertTrue(count($this->repo->getErrors()) == 1);
		$this->repo->errors = null;
		$input = ['title' => 'test', 'body' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertfalse($this->repo->createOrUpdate(['title' => '', 'body' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$this->assertfalse($this->repo->createOrUpdate(['title' => 'hej', 'body' => ''],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$this->assertfalse($this->repo->createOrUpdate(['title' => '', 'body' => 'test'],1));
		$this->assertTrue(is_object($this->repo->getErrors()));
		$this->repo->errors = null;
		$this->assertTrue($this->repo->createOrUpdate(['title' => 'hej', 'body' => 'test'],1));
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