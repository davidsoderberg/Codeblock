<?php namespace App\Repositories\Notification;

interface NotificationRepository {

	public function get($id = 0);

	public function send($user_id, $type, $subject, $body, $object);

	public function delete($id);
}