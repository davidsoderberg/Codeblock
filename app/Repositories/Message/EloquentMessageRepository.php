<?php namespace App\Repositories\Message;

use App\Models\Message;
use App\Models\Participant;
use App\Models\Thread;
use App\Repositories\CRepository;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Class EloquentArticleRepository
 * @package App\Repositories\Message
 */
class EloquentMessageRepository extends CRepository implements MessageRepository {

	/**
	 * Property to store Thread model in.
	 *
	 * @var
	 */
	private $thread;

	/**
	 * Setter for thread.
	 *
	 * @param Thread $thread
	 */
	public function setThread( Thread $thread ) {
		$this->thread = $thread;
	}

	/**
	 * Getter for thread.
	 *
	 * @param $id
	 */
	public function getThread( $id = null ) {
		if ( !is_null( $id ) && is_numeric( $id ) && $id !== 0 ) {
			return Thread::find( $id );
		}

		if ( $id === 0 ) {
			return $this->thread;
		}

		return Thread::getAllLatest()->get();

	}

	/**
	 * Get threads current user participating in.
	 *
	 * @return mixed
	 */
	public function getThreadsParticipatingIn() {
		return Thread::forUser( Auth::user()->id )->latest( 'updated_at' )->get();
	}

	/**
	 * Get threads with new messages for current user..
	 *
	 */
	public function getThreadsWithNewMessages() {
		Thread::forUserWithNewMessages( Auth::user()->id )->latest( 'updated_at' )->get();
	}

	/**
	 * Creates a thread.
	 *
	 * @param $subject
	 *
	 * @return boolean
	 */
	public function CreateThread( $subject ) {

		$thread = new Thread();

		$thread->subject = $this->stripTrim( $subject );

		if ( $thread->save() ) {
			$this->thread = $thread;
			return true;
		}

		$this->errors = $thread::$errors;

		return false;

	}

	/**
	 * Creates a message.
	 *
	 * @param $body
	 *
	 * @return boolean
	 */
	public function CreateMessage( $body ) {

		$message = new Message();

		$message->body = $this->stripTrim( $body );

		$message->thread_id = $this->thread->id;

		$message->user_id = Auth::user()->id;

		if ( $message->save() ) {
			return true;
		}

		$this->errors = $message::$errors;

		return false;

	}

	/**
	 * Creates participant.
	 */
	public function CreateParticipant( $user_id = null ) {

		if ( is_null( $user_id ) ) {
			$user_id = Auth::user()->id;
		}

		$participant = new Participant();

		$participant->last_read = new Carbon;

		$participant->thread_id = $this->thread->id;

		$participant->user_id = $user_id;

		if ( $participant->save() ) {
			return true;
		}

		$this->errors = $participant::$errors;

		return false;

	}

	/**
	 * Adds participants.
	 *
	 * @param $recipients
	 */
	public function addParticipants( $recipients ) {
		if ( count( $recipients ) ) {
			foreach( $recipients as $user_id ) {
				$this->CreateParticipant($user_id);
			}
		}
	}


}