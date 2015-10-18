<?php namespace App\Http\Controllers;

use App\Notification;
use App\NotificationType;
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
		parent::__construct();
		$this->notification = $notification;
	}

	/**
	 * Listar alla notifikationer.
	 * @return mixed
	 */
	public function index() {
		return View::make('notification.index')->with('title', 'Notifications')->with('notifications', $this->notification->get());
	}

	/**
	 * Listar alla notifikationer för en specifik användare.
	 * @return mixed
	 */
	public function listNotification(){
		$this->notification->setRead(Auth::user()->id);
		return View::make('notification.list')->with('title', 'Notifications');
	}

	/**
	 * Visar en specifik notifikation.
	 * @param $id
	 * @return mixed
	 */
	public function show($id) {
		$notification = $this->notification->get($id);
		return View::make('notification.show')->with('title', 'Notification: '.$notification->subject)->with('notification', $notification);
	}

	/**
	 * @param int $id
	 * @return mixed
	 */
	public function create($id = 0){
		if($id > 0){
			return $this->edit($id);
		}

		return View::make('notification.create')
			->with('title', 'Create message')
			->with('teammates', $this->getTemmates());
	}

	/**
	 * @param int $id
	 * @return mixed
	 */
	public function createOrUpdate($id = 0)
	{
		$this->notification->setReply($id);
		$input = $this->request->all();
		if($input['to_id'] != 0) {
			if($this->notification->send($input['to_id'], NotificationType::MESSAGE, null, $input['subject'], $input['body'])) {
				return Redirect::action('NotificationController@listNotification')
				               ->with('success', 'Your messaged has been send.');
			}
			return Redirect::back()->withErrors($this->notification->getErrors())->withInput();
		}
		return Redirect::back()->with('error', 'Choose someone to send this message to.')->withInput();
	}

	/**
	 * Tar bort en notifikation.
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

	/**
	 * @param $id
	 * @return mixed
	 */
	private function edit($id) {
		$notification = new Notification();
		$notification->id = $id;
		$note = Notification::find($id);
		$notification->user_id = $note->from_id;
		$notification->subject = '';
		$notification->body = $notification->subject;
		$notification->reply_id = $note->id;

		return View::make('notification.edit')
		           ->with('title', 'Reply message')
		           ->with('teammates', $this->getTemmates())
		           ->with('notification', $notification);
	}

	/**
	 * @return array
	 */
	private function getTemmates() {
		$teammates = [];
		$teammates[0] = '';
		$teams = Auth::user()->teams->merge(Auth::user()->ownedTeams);
		foreach($teams as $team) {
			foreach($team->users as $user) {
				$teammates[$user->id] = $user->username;
			}
		}

		return $teammates;
	}

}