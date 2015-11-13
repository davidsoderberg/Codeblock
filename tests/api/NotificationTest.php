<?php namespace api;

use App\Notification;

class NotificationTest extends \ApiCase {

	public function test_get() {
		$this->get('/api/v1/notifications', $this->get_headers())->seeStatusCode(200);
	}

	/*
	 * Needs token.
	 */
	public function test_delete() {
		$notification = $this->create(Notification::class, ['user_id' => 1]);
		$this->post('/api/v1/notifications/'.$notification->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);

		$this->setUser(2);
		$notification = $this->create(Notification::class, ['user_id' => 2]);
		$this->post('/api/v1/notifications/'.$notification->id, ['_method' => 'delete'], $this->get_headers())->seeStatusCode(200);
	}

}