<?php namespace App\Http\Controllers;

use App\Models\Model;
use App\Models\NotificationType;
use App\Repositories\Notification\NotificationRepository;
use App\Services\PaginationPresenter;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
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
abstract class Controller extends BaseController {

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
	public function __construct() {
		$this->request = app( 'Illuminate\Http\Request' );
		View::share( 'siteName', ucfirst( str_replace( 'http://', '', URL::to( '/' ) ) ) );
		$this->client = new Client();
	}

	/**
	 * Sends a notification to mentioned user.
	 *
	 * @param $text
	 * @param $object
	 * @param NotificationRepository $notification
	 */
	protected function mentioned( $text, $object, NotificationRepository $notification ) {
		$users = [];
		preg_match_all( '/(^|\s)@(\w+)/', $text, $users );
		foreach( $users as $username ) {
			if ( count( $username ) >= 1 ) {
				$username = $username[0];
				if ( !$notification->send( $username, NotificationType::MENTION, $object ) ) {
					$errors = [
						'username' => $username,
						'type' => NotificationType::MENTION,
						'errors' => $notification->errors,
					];
					Log::error( json_encode( $errors ) );
				}
			}
		}
	}

	/**
	 * Fetch permission for current method.
	 * @return array|string
	 */
	protected function getPermission() {
		$action = debug_backtrace()[1];
		$permissionAnnotation = New Permission( $action['class'] );

		return $permissionAnnotation->getPermission( $action['function'] );
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
	protected function getSelectArray( $objects, $key = 'id', $value = 'name' ) {
		$selectArray = [];
		foreach( $objects as $object ) {
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
	protected function addHidden( $objects ) {
		if ( $objects instanceof Collection ) {
			foreach( $objects as $object ) {
				$object->addToHidden();
			}
		} else {
			if ( $objects instanceof Model ) {
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
	public function createPaginator( Collection $data ) {
		if ( !isset( $_GET['page'] ) || !is_numeric( $_GET['page'] ) ) {
			$_GET['page'] = 1;
		}
		$paginator = new LengthAwarePaginator( $data, count( $data ), $this->perPage, $_GET['page'], ['path' => '/' . Request::path()] );
		$data = $data->slice( ( $_GET['page'] * $this->perPage ) - $this->perPage, $this->perPage );

		return ['data' => $data, 'paginator' => $paginator->render()];
	}
}
