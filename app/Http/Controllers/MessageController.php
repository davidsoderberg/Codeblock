<?php namespace App\Http\Controllers;

use App\Repositories\Message\MessageRepository;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Input;

/**
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller {


	/**
	 * Property to store MessageRepository in.
	 *
	 * @var MessageRepository
	 */
	private $repo;

	/**
	 * Constructor for ArticleController.
	 *
	 * @param MessageRepository $repository
	 */
	public function __construct( MessageRepository $repository ) {
		parent::__construct();
		$this->repo = $repository;
	}

	/**
	 * Render index view for messages.
	 *
	 * @param $id
	 *
	 * @return objekt
	 */
	public function index( $id = null ) {

		$threads = $this->repo->getThread();

		$users = User::where( 'id', '!=', Auth::id() )->get();


		if ( !is_null( $id ) ) {
			$thread = $this->repo->getThread( $id );
			if ( !is_null( $thread ) ) {
				$threads = $thread;
			}

			$userId = Auth::user()->id;
			$users = User::whereNotIn( 'id', $threads->participantsUserIds( $userId ) )->get();

			$threads->markAsRead( $userId );
		}

		return View::make( 'message.index' )
		           ->with( 'title', 'Threads' )
		           ->with( 'users', $users )
		           ->with( 'threads', $threads )
		           ->with( 'id', $id );
	}

	/**
	 * Creates or updates a message.
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function createOrUpdate( $id = null ) {
		$input = Input::all();
		$errors = null;
		$thread = null;

		if ( !is_null( $id ) && is_numeric( $id ) ) {
			$thread = $this->repo->getThread( $id );
			$this->repo->setThread( $thread );

			$thread->activateAllParticipants();
		}

		if ( is_null( $thread ) ) {
			$this->repo->CreateThread( $input['subject'] );

			$errors = $this->repo->getErrors();
		}

		if ( $errors === null ) {
			$this->repo->CreateMessage( $input['message'] );
			if ( $errors === null ) {
				$this->repo->CreateParticipant();

				if ( !empty( $input['recipients'] ) ) {
					$this->repo->addParticipants( $input['recipients'] );
				}
			}
		}

		if ( $errors === null ) {
			return Redirect::back()->with( 'success', 'Your message has been saved.' );
		}

		return Redirect::back()->withErrors( $errors )->withInput();
	}

}