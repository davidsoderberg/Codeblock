<?php namespace App\Http\Controllers;

use App\NotificationType;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	protected function mentioned($text, $object, NotificationRepository $notification){
		$users = array();
		preg_match_all('/(^|\s)@(\w+)/', $text, $users);
		foreach($users as $username) {
			if(!$notification->send($username[0], NotificationType::MENTION, $object)){
				$errors = array(
					'username' => $username,
					'type' => NotificationType::MENTION,
					'errors' => $notification->errors
				);
				Log::error(json_encode($errors));
			}
		}
	}

}
