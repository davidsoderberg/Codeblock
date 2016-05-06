<?php namespace App\Repositories\Message;

use App\Models\Thread;

/**
 * Interface MessageRepository
 * @package App\Repositories\Message
 */
interface MessageRepository
{

	/**
	 * Setter for thread.
	 *
	 * @param Thread $thread
	 *
	 * @return mixed
	 */
	public function setThread(Thread $thread);

	/**
	 * Getter for thread.
	 *
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function getThread($id = null);

	/**
	 * Get threads current user participating in.
	 *
	 * @return mixed
	 */
	public function getThreadsParticipatingIn();

	/**
	 * Get threads with new messages for current user.
	 *
	 * @return mixed
	 */
	public function getThreadsWithNewMessages();

	/**
	 * Creates a thread.
	 *
	 * @param $subject
	 *
	 * @return mixed
	 */
	public function CreateThread($subject);

	/**
	 * Creates a message.
	 *
	 * @param $body
	 *
	 * @return mixed
	 */
	public function CreateMessage($body);

	/**
	 * Creates participant.
	 *
	 * @param null $user_id
	 *
	 * @return mixed
	 */
	public function CreateParticipant($user_id = null);

	/**
	 * Adds participants.
	 *
	 * @param $recipients
	 *
	 * @return mixed
	 */
	public function addParticipants($recipients);
}