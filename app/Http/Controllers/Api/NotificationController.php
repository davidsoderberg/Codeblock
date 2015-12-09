<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Support\Facades\Auth;
use App\Services\Transformer;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class NotificationController
 * @package App\Http\Controllers\Api
 */
class NotificationController extends ApiController {

	/**
	 * Shows a notification.
	 *
	 * @param NotificationRepository $notificationRepository
	 *
	 * @return mixed
	 */
	public function notifications(NotificationRepository $notificationRepository) {
		$notificationRepository->setRead(Auth::user()->id);
		$notifications = $this->addHidden(Auth::user()->inbox);
		Transformer::walker($notifications);
		return $this->response([$this->stringData => $notifications], 200);
	}

	/**
	 * Deletes a notification.
	 *
	 * @param NotificationRepository $notification
	 * @param $id
	 *
	 * @return mixed
	 */
	public function deleteNotification(NotificationRepository $notification, $id) {
		try {
			$note = $notification->get($id);
			if(Auth::user()->id == $note->user_id) {
				if($notification->delete($id)) {
					return $this->response([$this->stringMessage => 'Your notification has been deleted.'], 200);
				}
			}
		} catch(\Exception $e) {
		}

		return $this->response([$this->stringErrors => 'You can not delete that notification.'], 204);
	}

}
