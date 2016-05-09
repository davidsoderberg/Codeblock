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
    use \ClientTrait;

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
        if (!empty(env('SOCKET_ADRESS')) && !empty(env('SOCKET_PORT'))) {
            $this->client = new \WebSocket\Client("ws://" . env('SOCKET_ADRESS') . ":" . env('SOCKET_PORT'));
        }
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
        if (!empty($this->client)) {
            $user_id = $this->getUserId($user_id, $object);
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
}
