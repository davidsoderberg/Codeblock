<?php

use App\Repositories\Post\EloquentPostRepository;
use App\Repositories\Comment\EloquentCommentRepository;
use App\User;

class CommentTest extends UnitCase {

	public $repoPost;

	public $repo;

	public function setUp()
	{
		parent::setUp();
		$this->setUpDb();
		$this->repoPost = new EloquentPostRepository();
		$this->repo = new EloquentCommentRepository();
	}

	public function testGet(){
		$this->assertTrue(is_object($this->repo->get()));
		$this->assertFalse(is_object($this->repo->get(1)));

		$this->be(User::find(1));
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->repoPost->createOrUpdate($input);
		$input = ['post_id' => 1, 'comment' => 'hej'];
		$this->assertTrue($this->repo->createOrUpdate($input));

		$this->assertTrue(is_object($this->repo->get(1)));
	}

	public function testCreateOrUpdate(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repoPost->createOrUpdate($input));
		$input = ['post_id' => 1, 'comment' => 'test'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$input = ['status' => '2'];
		$this->assertTrue($this->repo->createOrUpdate($input, 1));
		$this->assertEquals(count($this->repoPost->get(1)->comments),1);
	}

	public function testDelete(){
		$input = ['name' => 'test', 'cat_id' => 2, 'description' => 'test', 'code' => 'test'];
		$this->be(User::find(1));
		$this->assertTrue($this->repoPost->createOrUpdate($input));
		$this->assertFalse($this->repo->delete(1));
		$input = ['post_id' => 1, 'comment' => 'hej'];
		$this->assertTrue($this->repo->createOrUpdate($input));
		$this->assertTrue($this->repo->delete(1));
	}

}