<?php namespace api;

use App\Models\Forum;

class ForumTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/v1/forums')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$forum = $this->create(Forum::class);
		$this->post('/api/v1/forums/'.$forum->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}