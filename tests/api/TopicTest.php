<?php namespace api;

use App\Forum;
use App\Reply;
use App\Topic;

// 4
class TopicTest extends \ApiCase {

	public function create_forum(){
		return $this->create(Forum::class);
	}

	public function test_get() {
		$this->get('/api/topics')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$forum = $this->create_forum();
		$this->post('/api/topics', ['forum_id' => $forum->id, 'title' => 'hej', 'reply' => 'hej'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$forum = $this->create_forum();
		$topic = $this->create(Topic::class, ['forum_id' => $forum->id]);
		$this->create(Reply::class, ['topic_id' => $topic->id]);
		$this->post('/api/topics/'.$topic->id, ['_method' => 'put', 'title' => 'test' ], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$forum = $this->create_forum();
		$topic = $this->create(Topic::class, ['forum_id' => $forum->id]);
		$this->create(Reply::class, ['topic_id' => $topic->id]);
		$this->post('/api/topics/'.$topic->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}