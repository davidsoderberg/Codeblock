<?php namespace App\Repositories\Notification;

interface NotificationRepository {
	public function send($user_id, $type, $subject, $body, $object);
}