<?php namespace App\Http\Controllers;

use App\Models\Model;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Services\PaginationPresenter;
use App\Services\Pusher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Services\Annotation\Permission;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Services\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
abstract class Controller extends BaseController
{

    /**
     * Property to store current websocket client in.
     *
     * @var Client
     */
    protected $client;

    /**
     * Property to store current request object in.
     *
     * @var mixed
     */
    protected $request;

    /**
     * Property to store number of posts to render on each paginator page.
     *
     * @var int
     */
    protected $perPage = 10;

    use DispatchesJobs, ValidatesRequests;

    /**
     * Constructor for Controller.
     */
    public function __construct()
    {
        $this->request = app('Illuminate\Http\Request');
        $url = preg_replace("/https?:\/\//", "", URL::to('/'));
        View::share('siteName', ucfirst($url));
        $this->client = new Pusher();
    }

    /**
     * Sends notification to user.
     *
     * @param int          $user_id
     * @param string       $type
     * @param Notification $object
     * @param null         $subject
     * @param null         $body
     *
     * @return bool
     */
    protected function send_notification($user_id, $type, $object, $subject = null, $body = null)
    {
        $notificationRepository = App::make('App\Repositories\Notification\NotificationRepository');
        if ($notificationRepository->send($user_id, $type, $object, $subject, $body)) {
            if ( ! $this->client->send(new Notification(), $notificationRepository->getUserId())) {
                return $notificationRepository->sendNotificationEmail();
            }

            return true;
        }

        return $notificationRepository->errors;
    }

    /**
     * Sends a notification to mentioned user.
     *
     * @param $text
     * @param $object
     */
    protected function mentioned($text, $object)
    {
        $users = [];
        preg_match_all('/(^|\s)@(\w+)/', $text, $users);
        if (count($users) === 3) {
            $users = $users[2];
            foreach ($users as $username) {
                $errors = $this->send_notification($username, NotificationType::MENTION, $object);
                if (is_array($errors) && ! empty($errors)) {
                    $errors = [
                        'username' => $username,
                        'type' => NotificationType::MENTION,
                        'errors' => $errors,
                    ];
                    Log::error(json_encode($errors));
                }
            }
        }
    }

    /**
     * Fetch permission for current method.
     * @return array|string
     */
    protected function getPermission()
    {
        $action = debug_backtrace()[1];
        $permissionAnnotation = new Permission($action['class']);

        return $permissionAnnotation->getPermission($action['function']);
    }

    /**
     * Fetch select array from objects.
     *
     * @param $objects
     * @param string $key
     * @param string $value
     *
     * @return array
     */
    protected function getSelectArray($objects, $key = 'id', $value = 'name')
    {
        $selectArray = [];
        foreach ($objects as $object) {
            $selectArray[$object[$key]] = $object[$value];
        }

        return $selectArray;
    }

    /**
     * Adding hidden to objects.
     *
     * @param $objects
     *
     * @return mixed
     */
    protected function addHidden($objects)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $object->addToHidden();
            }
        } else {
            if ($objects instanceof Model) {
                $objects->addToHidden();
            }
        }

        return $objects;
    }

    /**
     * Creates paginator.
     *
     * @param Collection $data
     *
     * @return array
     */
    public function createPaginator(Collection $data)
    {
        if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        $paginator = new LengthAwarePaginator($data, count($data), $this->perPage, $_GET['page'],
            ['path' => '/' . Request::path()]);
        $data = $data->slice(($_GET['page'] * $this->perPage) - $this->perPage, $this->perPage);

        return ['data' => $data, 'paginator' => $paginator->render()];
    }
}
