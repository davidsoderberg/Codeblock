<?php namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Thread
 * @package App\Models
 */
class Thread extends Model {

	use SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'threads';

	/**
	 * The attributes that can be set with Mass Assignment.
	 *
	 * @var array
	 */
	protected $fillable = ['subject'];

	/**
	 * Validation rules.
	 *
	 * @var array
	 */
	protected static $rules = [
		'subject' => 'required',
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	/**
	 * "Users" table name to use for manual queries
	 *
	 * @var string|null
	 */
	private $usersTable = null;

	/**
	 * Messages relationship
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function messages() {
		return $this->hasMany( 'App\Models\Message' );
	}

	/**
	 * Returns the latest message from a thread
	 *
	 * @return \Cmgmyr\Messenger\Models\Message
	 */
	public function getLatestMessageAttribute() {
		return $this->messages()->latest()->first();
	}

	/**
	 * Participants relationship
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function participants() {
		return $this->hasMany( 'App\Models\Participant' );
	}

	/**
	 * Returns the user object that created the thread
	 *
	 * @return mixed
	 */
	public function creator() {
		return $this->messages()->oldest()->first()->user;
	}

	/**
	 * Returns all of the latest threads by updated_at date
	 *
	 * @return mixed
	 */
	public static function getAllLatest() {
		return self::latest( 'updated_at' );
	}

	/**
	 * Returns an array of user ids that are associated with the thread
	 *
	 * @param null $userId
	 *
	 * @return array
	 */
	public function participantsUserIds( $userId = null ) {
		$users = $this->participants()->lists( 'user_id' );

		if ( $userId ) {
			$users[] = $userId;
		}

		return $users;
	}

	/**
	 * Mark a thread as read for a user
	 *
	 * @param integer $user_id
	 */
	public function markAsRead( $user_id ) {
		try {
			$participant = $this->getParticipantFromUser( $user_id );
			$participant->last_read = new Carbon;
			$participant->save();
		} catch( ModelNotFoundException $e ) {
			// do nothing
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
		try {
			$participant = $this->getParticipantFromUser( $userId );
			if ( $this->updated_at > $participant->last_read ) {
				return true;
			}
		} catch( ModelNotFoundException $e ) {
			// do nothing
		}

		return false;
	}

	/**
	 * Finds the participant record from a user id
	 *
	 * @param $userId
	 *
	 * @return mixed
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public function getParticipantFromUser( $userId ) {
		return $this->participants()->where( 'user_id', $userId )->firstOrFail();
	}

	/**
	 * Restores all participants within a thread that has a new message
	 */
	public function activateAllParticipants() {
		$participants = $this->participants()->get();
		foreach( $participants as $participant ) {
			$participant->restore();
		}
	}

	/**
	 * Checks to see if a user is a current participant of the thread
	 *
	 * @param $userId
	 *
	 * @return bool
	 */
	public function hasParticipant( $userId ) {
		$participants = $this->participants()->where( 'user_id', '=', $userId );
		if ( $participants->count() > 0 ) {
			return true;
		}

		return false;
	}

}
