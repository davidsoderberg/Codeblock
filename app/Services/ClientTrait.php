<?php namespace App\Services;

use InvalidArgumentException;
use WebSocket\ConnectionException;

/**
 * Class ClientTrait
 * @package App\Services
 */
trait ClientTrait
{
    protected function getUserId($user_id, $object)
    {
        if ($user_id == 0 || !is_numeric($user_id)) {
            if (!isset($object->user_id)) {
                throw new \InvalidArgumentException('The user id is not valid');
            }
            return $object->user_id;
        }
        return $user_id;
    }

    /**
     * Get toast message.
     *
     * @param $object
     *
     * @return string
     */
    protected function getMessage($object)
    {
        $message = 'You have a new ';
        $html = new HtmlBuilder();
        switch ($this->getObjectName($object)) {
            case 'Notification':
                $message .= $html->actionlink($url = ['action' => 'NotificationController@listNotification'],
                    'notification');
                break;
            case 'Post':
                $message .= 'comment in post: ' . $html->actionlink($url = [
                        'action' => 'PostController@show',
                        'params' => [$object->id],
                    ], $object->name);
                break;
            case 'Topic':
                $message .= 'reply in topic: ' . $html->actionlink($url = [
                        'action' => 'TopicController@show',
                        'params' => [$object->id],
                    ], $object->title);
                break;
        }

        return $message;
    }

    /**
     * Return objects name.
     *
     * @param $object
     *
     * @return string
     */
    protected function getObjectName($object)
    {
        if (is_object($object)) {
            $namespaces = explode('\\', get_class($object));
            $object_type = $namespaces[count($namespaces) - 1];
            if (class_exists('App\\Models\\' . $object_type)) {
                return $object_type;
            }
        }

        return '';
    }
}
