<?php namespace api;

use App\Comment;
use App\Post;

class CommentTest extends \ApiCase {

	/*
	 * Needs token.
	 */
	public function test_create() {
		$this->create(Post::class);
		$this->post('/api/comments', ['comment' => 'testar', 'post_id' => 1], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$this->create(Post::class);
		$comment = $this->create(Comment::class);
		$this->post('/api/comments/'.$comment->id, ['comment' => 'testar', '_method' => 'put'], $this->get_headers())->seeStatusCode(201);

		$this->setUser(2);
		$comment = $this->create(Comment::class, ['user_id' => 2]);
		$this->post('/api/comments/'.$comment->id, ['comment' => 'testar', '_method' => 'put'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$this->create(Post::class);
		$comment = $this->create(Comment::class);
		$this->post('/api/comments/'.$comment->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);

		$this->setUser(2);
		$comment = $this->create(Comment::class, ['user_id' => 2]);
		$this->post('/api/comments/'.$comment->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_rate() {
		$this->create(Post::class);
		$comment = $this->create(Comment::class, ['user_id' => 2]);
		$this->post('/api/comments/rate/'.$comment->id, [], $this->get_headers())->seeStatusCode(200);
	}

}