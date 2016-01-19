<?php namespace App\Repositories\Message;

use App\Models\Message;
use App\Models\Participant;
use App\Models\Thread;
use App\Repositories\CRepository;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Class EloquentMessageRepository
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
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function getThread( $id = null ) {
		if ( !is_null( $id ) && is_numeric( $id ) && $id !== 0 ) {
			$this->thread = Thread::find( $id );
			$id = 0;
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
	 * Get threads with new messages for current user.
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
	 *
	 * @param null $user_id
	 *
	 * @return bool
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
				$this->CreateParticipant( $user_id );
			}
		}
	}


	/**
	 * Finds the participant record from a user id
	 *
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function getParticipantFromUser( $user_id ) {
		$thread = $this->thread;
		if ( !is_null( $thread ) ) {
			$participants = $thread->participants;
			foreach( $participants as $participant ) {
				if ( $participant->user_id === $user_id ) {
					return $participant;
				}
			}
		}
	}

	/**
	 * See if the current thread is unread by the user
	 *
	 * @param integer $userId
	 *
	 * @return bool
	 */
	public function isUnread( $userId ) {
		$participant = $this->getParticipantFromUser( $userId );
		if ( !is_null( $participant ) ) {
			if ( $this->thread->updated_at > $participant->last_read ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks to see if a user is a current participant of the thread
	 *
	 * @param $user_id
	 *
	 * @return bool
	 */
	public function hasParticipant( $user_id ) {
		$participant = $this->getParticipantFromUser( $user_id );
		if ( !is_null( $participant ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Mark a thread as read for a user
	 *
	 * @param integer $user_id
	 *
	 * @return boolean
	 */
	public function markAsRead( $user_id ) {
		$participant = $this->getParticipantFromUser( $user_id );
		if ( !is_null( $participant ) ) {
			$participant->last_read = new Carbon;

			return $participant->save();
		}

		return false;
	}

	/**
	 * Restores all participants within a thread that has a new message
	 */
	public function activateAllParticipants() {
		$participants = $this->thread->participants()->get();
		$participants->merge($this->thread->trashedParticipants());
		foreach( $participants as $participant ) {
			$participant->restore();
		}
	}

	/**
	 * Returns an array of user ids that are associated with the thread
	 *
	 * @param null $user_id
	 *
	 * @return array
	 */
	public function participantsUserIds( $user_id = null ) {
		$users = $this->thread->participants()->lists( 'user_id' );
		$users->merge($this->thread->trashedParticipants()->lists('user_id'));

		if ( $user_id ) {
			$users[] = $user_id;
		}

		return $users;
	}

	/**
	 * Removes user from message thread.
	 *
	 * @param $thread_id
	 *
	 * @return bool
	 */
	public function leave($thread_id){
		$thread = $this->getThread($thread_id);
		if($thread instanceof Thread){
			$participants = $thread->participants;

			foreach($participants as $participant){
				if($participant->user_id == Auth::user()->id){
					$participant->deleted_at = date('Y-m-d H:i:s');
					return $participant->save();
				}
			}
		}
		return false;
	}
}