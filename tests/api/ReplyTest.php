<?php namespace api;

use App\Forum;
use App\Reply;
use App\Topic;

// 3
class ReplyTest extends \ApiCase {

	public function create_topic(){
		$forum = $this->create(Forum::class);
		return $this->create(Topic::class, ['forum_id' => $forum->id]);
	}

	/*
	 * Needs token.
	 */
	public function test_create() {
		$topic = $this->create_topic();
		$this->post('/api/replies', ['topic_id' => $topic->id, 'reply' => 'hej'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_update() {
		$topic = $this->create_topic();
		$reply = $this->create(Reply::class, ['topic_id' => $topic->id]);
		$this->post('/api/replies/'.$reply->id, ['_method' => 'put', 'reply' => 'hej'], $this->get_headers())->seeStatusCode(201);

		$this->setUser(2);
		$reply = $this->create(Reply::class, ['topic_id' => $topic->id, 'user_id' => 2]);
		$this->post('/api/replies/'.$reply->id, ['_method' => 'put', 'reply' => 'hej'], $this->get_headers())->seeStatusCode(201);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$topic = $this->create_topic();
		$this->create(Reply::class, ['topic_id' => $topic->id]);
		$reply = $this->create(Reply::class, ['topic_id' => $topic->id]);
		$this->post('/api/replies/'.$reply->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);

		$this->setUser(2);
		$reply = $this->create(Reply::class, ['topic_id' => $topic->id, 'user_id' => 2]);
		$this->post('/api/replies/'.$reply->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}