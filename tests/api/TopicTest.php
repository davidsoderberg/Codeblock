<?php namespace api;

use App\Models\Forum;
use App\Models\Reply;
use App\Models\Topic;

class TopicTest extends \ApiCase {

	public function create_forum(){
		return $this->create(Forum::class);
	}

	public function test_get() {
		$this->get('/api/v1/topics')->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$forum = $this->create_forum();
		$this->post('/api/v1/topics', ['forum_id' => $forum->id, 'title' => 'hej', 'reply' => 'hej'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$forum = $this->create_forum();
		$topic = $this->create(Topic::class, ['forum_id' => $forum->id]);
		$this->create(Reply::class, ['topic_id' => $topic->id]);
		$this->post('/api/v1/topics/'.$topic->id, ['_method' => 'put', 'title' => 'test' ], $this->get_headers())->seeStatusCode(201);
        /*
		$this->setUser(2);
		$topic = $this->create(Topic::class, ['forum_id' => $forum->id]);
		$this->create(Reply::class, ['topic_id' => $topic->id, 'user_id' => 2]);
		$this->post('/api/v1/topics/'.$topic->id, ['_method' => 'put', 'title' => 'test' ], $this->get_headers())->seeStatusCode(201);
        */
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$forum = $this->create_forum();
		$topic = $this->create(Topic::class, ['forum_id' => $forum->id]);
		$this->create(Reply::class, ['topic_id' => $topic->id]);
		$this->post('/api/v1/topics/'.$topic->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
        /*
		$this->setUser(2);
		$topic = $this->create(Topic::class, ['forum_id' => $forum->id]);
		$this->create(Reply::class, ['topic_id' => $topic->id, 'user_id' => 2]);
		$this->post('/api/v1/topics/'.$topic->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
        */
	}

}