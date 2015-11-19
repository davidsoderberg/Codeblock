<?php namespace App\Http\Controllers;

use App\Repositories\Notification\NotificationRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Class NotificationController
 * @package App\Http\Controllers
 */
class NotificationController extends Controller {

	/**
	 * Property to store NotificationRepository.
	 *
	 * @var
	 */
	private $notification;

	/**
	 * Constructor for NotificationController.
	 *
	 * @param NotificationRepository $notification
	 */
	public function __construct(NotificationRepository $notification) {
		parent::__construct();
		$this->notification = $notification;
	}

	/**
	 * Lists all notifications.
	 * @return mixed
	 */
	public function index() {
		return View::make('notification.index')->with('title', 'Notifications')->with('notifications', $this->notification->get());
	}

	/**
	 * Lists all notifications for a user.
	 * @return mixed
	 */
	public function listNotification(){
		$this->notification->setRead(Auth::user()->id);
		return View::make('notification.list')->with('title', 'Notifications');
	}

	/**
	 * Render a choosen notification.
	 * @param $id
	 * @return mixed
	 */
	public function show($id) {
		$notification = $this->notification->get($id);
		return View::make('notification.show')->with('title', 'Notification: '.$notification->subject)->with('notification', $notification);
	}

	/**
	 * Deletes a notification.
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		try {
			$note = $this->notification->get($id);
			if(Auth::user()->id == $note->user_id) {
				if($this->notification->delete($id)) {
					return Redirect::back()->with('success', 'Your notification has been deleted.');
				}
			}
		} catch(\Exception $e){}

		return Redirect::back()->with('error', 'You can not delete that notification.');
	}

}