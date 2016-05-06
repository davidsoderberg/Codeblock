<?php namespace App\Models\Traits;

use App\Models\Thread;
use App\Models\Participant;

/**
 * Class Messagable
 * @package App\Models\Traits
 */
trait Messagable
{
	/**
	 * Message relationship
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function messages()
	{
		return $this->hasMany('App\Models\Message');
	}

	/**
	 * Thread relationship
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
	 */
	public function threads()
	{
		return $this->belongsToMany('App\Models\Thread', 'participants');
	}

	/**
	 * Returns the new messages count for user
	 *
	 * @return int
	 */
	public function newMessagesCount()
	{
		return count($this->threadsWithNewMessages());
	}

	/**
	 * Returns all threads with new messages
	 *
	 * @return array
	 */
	public function threadsWithNewMessages()
	{
		$threadsWithNewMessages = [];
		$participants = Participant::where('user_id', $this->id)->lists('last_read', 'thread_id');

		if ($participants) {
			$threads = Thread::whereIn('id', array_keys($participants))->get();

			foreach ($threads as $thread) {
				if ($thread->updated_at > $participants[$thread->id]) {
					$threadsWithNewMessages[] = $thread->id;
				}
			}
		}

		return $threadsWithNewMessages;
	}
}
