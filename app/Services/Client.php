<?php namespace App\Services;

use InvalidArgumentException;
use WebSocket\ConnectionException;
use App\Services\HtmlBuilder;

/**
 * Class Client
 * @package App\Services
 */
class Client extends PubSub
{

    /**
     * Property to store current client in.
     * @var \WebSocket\Client
     */
    private $client;

    /**
     * Constructo for Client.
     */
    public function __construct()
    {
        $this->client = new \WebSocket\Client("ws://" . env('SOCKET_ADRESS') . ":" . env('SOCKET_PORT'));
    }

    /**
     * Sending message to websocket server from the application.
     *
     * @param $object
     * @param int $user_id
     * @param string $channel
     * @param string $topic
     *
     * @throws \WebSocket\BadOpcodeException
     */
    public function send($object, $user_id = 0, $channel = 'toast', $topic = '')
    {
        if ($user_id == 0 || !is_numeric($user_id)) {
            if (!isset($object->user_id)) {
                throw new \InvalidArgumentException('The user id is not valid');
            }
            $user_id = $object->user_id;
        }
        try {
            $this->client->send(json_encode([
                "channel" => $channel,
                'topic' => $topic,
                'id' => $user_id,
                'message' => $this->checkToast($channel, $topic, $object),
            ]));
        } catch (ConnectionException $e) {
        }
    }

    /**
     * Check if message is toast.
     *
     * @param $channel
     * @param $topic
     * @param $object
     *
     * @return string
     */
    private function checkToast($channel, $topic, $object)
    {
        if ($channel != 'toast') {
            if ($topic == '') {
                throw new \InvalidArgumentException('Topic is not set');
            }
            if ($channel != 'subscribe' && $channel != 'publish') {
                throw new \InvalidArgumentException('Channel has wrong value, value should be subscribe or publish.');
            }

            return $object;
        }

        return $this->getMessage($object);
    }

    /**
     * Get toast message.
     *
     * @param $object
     *
     * @return string
     */
    private function getMessage($object)
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
    private function getObjectName($object)
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
