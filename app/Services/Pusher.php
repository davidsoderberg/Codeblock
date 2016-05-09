<?php namespace App\Services;

class Pusher extends PubSub
{
    use ClientTrait;

    private function isValidChannel($channel)
    {
        if (in_array($channel, ['toast','topic'])) {
            return true;
        }
        return false;
    }

    /**
     * Sending message to websocket server from the application.
     *
     * @param $object
     * @param int $user_id
     * @param string $channel
     * @param string $topic
     *
     */
    public function send($object, $user_id = 0, $channel = 'toast', $topic = '')
    {
        $user_id = $this->getUserId($user_id, $object);

        Pusher::trigger('codeblock', $channel, [
            'from_id' => $user_id,
            'to_id' => $this->getToUsers($object),
            'message' => $this->getMessage($object),
        ]);
    }

    private function getToUsers($object)
    {
        $userIds = [];
        switch ($this->getObjectName($object)) {
            case 'Notification':

                break;
            case 'Post':

                break;
            case 'Topic':

                break;
        }

        return $userIds;
    }
}
