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
	 * @var
	 */
	private $notification;

	/**
	 * @param NotificationRepository $notification
	 */
	public function __construct(NotificationRepository $notification) {
		$this->notification = $notification;
	}

	/**
	 * @return mixed
	 */
	public function index() {
		return View::make('notification.index')->with('title', 'Notifications')->with('notifications', $this->notification->get());
	}

	/**
	 * @return mixed
	 */
	public function listNotification(){
		return View::make('notification.list')->with('title', 'Notifications');
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function show($id) {
		$notification = $this->notification->get($id);
		return View::make('notification.show')->with('title', 'Notification: '.$notification->subject)->with('notification', $notification);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		$note = $this->notification->get($id);
		if(Auth::user()->id == $note->user_id) {
			if($this->notification->delete($id)) {
				return Redirect::back()->with('success', 'Your forum has been deleted.');
			}
		}

		return Redirect::back();
	}

}