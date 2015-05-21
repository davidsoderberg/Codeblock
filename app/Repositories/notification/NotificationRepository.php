<?php namespace App\Repositories\Notification;

interface NotificationRepository {

	public function get($id = 0);

	public function setUserId($user_id, $note);

	public function setType($type, $note);

	public function setObject($object, $note);

	public function setcontent($subject, $body, $type, $note);

	public function setFromId($from_id, $note);

	public function send($user_id, $type, $object, $subject = null, $body = null);

	public function getSubjectAndBody(\App\Notification $notification);

	public function delete($id);
}