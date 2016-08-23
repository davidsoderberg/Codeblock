<?php namespace App\Services;

use App\Repositories\Read\EloquentReadRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class Pusher
{

    public $pusher;

    public function __construct()
    {
        $this->pusher = new \Pusher(env('PUSHER_KEY'), env('PUSHER_SECRET'), env('PUSHER_APP_ID'));
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
        $return = [];
        $html    = new HtmlBuilder(\App::make('url'), \App::make('view'));
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
                $return['topic_id'] = $object->id;
                $message .= 'reply in topic: ' . $html->actionlink($url = [
                        'action' => 'TopicController@show',
                        'params' => [$object->id],
                    ], $object->title);
                break;
        }

        $return['message'] = $message;

        return $return;
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
            $namespaces  = explode('\\', get_class($object));
            $object_type = $namespaces[count($namespaces) - 1];
            if (class_exists('App\\Models\\' . $object_type)) {
                return $object_type;
            }
        }

        return '';
    }

    /**
     * Sending message to websocket server from the application.
     *
     * @param $object
     *
     */
    public function send($object, $user_id)
    {
        if ($user_id !== 0) {
            $channel  = 'presence-user_' . $user_id;
            $channels = array_keys($this->pusher->get_channels()->channels);
            if (in_array($channel, $channels)) {
                if ($this->pusher->trigger($channel, 'toast', $this->getMessage($object))) {
                    return true;
                }
            }
        }

        return false;
    }

    public function new_reply($reply, $user_id, $topic_id)
    {
        $channel   = 'presence-topic_' . $topic_id;
        $user_ids  = $this->getUsers($channel);
        $read_repo = new EloquentReadRepository();
        foreach ($user_ids as $id) {
            $read_repo->hasRead($topic_id, $id);
        }
        $this->pusher->trigger($channel, 'new_reply', [
            'message' => View::make('topic.reply')->with('reply', $reply)->render(),
            'user_id' => $user_id
        ]);
    }

    public function new_comment($comment, $user_id, $post_id){
        $channel   = 'presence-post_' . $post_id;
        $user_ids  = $this->getUsers($channel);
        $view = View::make('comment.comment')
                    ->with('comment', $comment)
                    ->with('rate', App::make('App\Repositories\Rate\RateRepository'))
                    ->with('pusher_auth_ids', $user_ids)
                    ->render();
        $this->pusher->trigger($channel, 'new_comment', [
            'message' => $view,
            'parent' => $comment->parent,
            'user_id' => $user_id
        ]);
    }

    public function getUsers($channel)
    {

        $result = $this->pusher->get('/channels/' . $channel . '/users');
        if (isset($result['result']['users'])) {
            $users    = $result['result']['users'];
            $user_ids = [];
            foreach ($users as $user) {
                $user_ids[] = $user['id'];
            }

            return $user_ids;
        }

        return [];
    }
}
