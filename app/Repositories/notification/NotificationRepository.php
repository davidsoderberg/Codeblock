<?php namespace App\Repositories\Notification;

/**
 * Interface NotificationRepository
 * @package App\Repositories\Notification
 */
interface NotificationRepository {

	/**
	 * Fetch one or all notifications.
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function get($id = 0);

	/**
	 * Creates an notification
	 *
	 * @param $user_id
	 * @param $type
	 * @param $object
	 * @param null $subject
	 * @param null $body
	 *
	 * @return mixed
	 */
	public function send($user_id, $type, $object, $subject = null, $body = null);

	/**
	 * Fetch subject and body for notification
	 *
	 * @param \App\Notification $notification
	 *
	 * @return mixed
	 */
	public function getSubjectAndBody(\App\Notification $notification);

	/**
	 * Deletes a notification.
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete($id);
}