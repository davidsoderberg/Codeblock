<?php namespace App\Http\Controllers;

use App\NotificationType;
use App\Repositories\Notification\NotificationRepository;
use App\Services\Jwt;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use App\Services\Annotation\Permission;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Services\Client;
use Illuminate\Http\Request;

abstract class Controller extends BaseController {

	protected $client;
	protected $request;

	use DispatchesCommands, ValidatesRequests;

	public function __construct(){
		$this->request = app('Illuminate\Http\Request');
		View::share('siteName', ucfirst(str_replace('http://', '', URL::to('/'))));
		$this->client = new Client();
	}

	protected function mentioned($text, $object, NotificationRepository $notification){
		$users = array();
		preg_match_all('/(^|\s)@(\w+)/', $text, $users);
		foreach($users as $username) {
			if(count($username) >= 1) {
				$username = $username[0];
				if(!$notification->send($username, NotificationType::MENTION, $object)){
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

	protected function getPermission(){
		$action = debug_backtrace()[1];
		$permissionAnnotation = New Permission($action['class']);
		return $permissionAnnotation->getPermission($action['function']);
	}

	public function getJwt(){
		if(Auth::check()) {
			return Response::json(array('token' => Jwt::encode(array('id' => Auth::user()->id))), 200);
		}
		return Response::json(array('message', 'You could not get your auth token, please try agian'), 400);
	}
}
