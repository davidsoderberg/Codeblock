<?php namespace api;

use App\Forum;

class ForumTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/forums')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$forum = $this->create(Forum::class);
		$this->post('/api/forums/'.$forum->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}