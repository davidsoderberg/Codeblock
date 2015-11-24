<?php namespace App\Repositories\Message;

use App\Models\Thread;

/**
 * Interface MessageRepository
 * @package App\Repositories\Message
 */
interface MessageRepository {

	public function setThread( Thread $thread );

	public function getThread( $id = null );

	public function getThreadsParticipatingIn();

	public function getThreadsWithNewMessages();

	public function CreateThread( $subject );

	public function CreateMessage( $body );

	public function CreateParticipant( $user_id = null );

	public function addParticipants( $recipients );
}