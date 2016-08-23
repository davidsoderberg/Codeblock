<?php namespace App\Repositories\Notification;

/**
 * Interface NotificationRepository
 * @package App\Repositories\Notification
 */
interface NotificationRepository
{

    /**
     *  Getter for current notifications user_id.
     */
    public function getUserId();

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
     * @param \App\Models\Notification $notification
     *
     * @return mixed
     */
    public function getSubjectAndBody(\App\Models\Notification $notification);

    /**
     * Sends a notification by mail.
     *
     * @return bool
     */
    public function sendNotificationEmail();

    /**
     * Deletes a notification.
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);
}
