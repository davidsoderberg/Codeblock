<?php namespace App\Http\Controllers;

use App\NotificationType;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	private $notification;

	public function __construct(NotificationRepository $notification){
		$this->notification = $notification;
	}

	protected function mentioned($text, $object){
		$users = array();
		preg_match_all('/(^|\s)@(\w+)/', $text, $users);
		foreach($users as $username) {
			if(!$this->notification->send($username, NotificationType::MENTION, $object)){
				$errors = array(
					'username' => $username,
					'type' => NotificationType::MENTION,
					'errors' => $this->notification->errors
				);
				Log::error(json_encode($errors));
			}
		}
	}

}
