<?php namespace App\Http\Controllers;

use App\Repositories\Notification\NotificationRepository;

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
	 *
	 */
	public function index() {

	}


	/**
	 *
	 */
	public function store() {
		//
	}

	/**
	 * @param $id
	 */
	public function show($id) {

	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id) {
		if($this->notification->delete($id)) {
			return Redirect::back()->with('success', 'Your forum has been deleted.');
		}

		return Redirect::back();
	}

}