<?php namespace App\Http\Controllers;

use App\Repositories\Reply\ReplyRepository;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationType;
use App\Repositories\Read\ReadRepository;

/**
 * Class ReplyController
 * @package App\Http\Controllers
 */
class ReplyController extends Controller {

	/**
	 * Constructor for ReplyController.
	 *
	 * @param ReplyRepository $Reply
	 */
	public function __construct( ReplyRepository $Reply ) {
		parent::__construct();
		$this->reply = $Reply;
	}

	/**
	 * Creates or updates a reply.
	 * @permission create_reply:optional
	 *
	 * @param ReadRepository $read
	 * @param NotificationRepository $notification
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function createOrUpdate( ReadRepository $read, NotificationRepository $notification, $id = null ) {
		if ( !is_null( $id ) ) {
			$reply = $this->reply->get( $id );
			if ( !Auth::user()->hasPermission( $this->getPermission(), false ) ) {
				if ( Auth::user()->id != $reply->user_id ) {
					return Redirect::back()->with( 'error', 'You can´t edit other users replies.' );
				}
			}
		}
		if ( $this->reply->createOrUpdate( $this->request->all(), $id ) ) {
			$reply = $this->reply->Reply;
			if ( is_null( $id ) ) {
				$replies = $reply->topic->replies;
				if ( Auth::user()->id != $replies->first()->user_id ) {
					$notification->send( $replies->first()->user_id, NotificationType::REPLY, $reply->topic );
					$this->client->send( $reply->topic, $replies->first()->user_id );
				}
				$this->client->send( $reply, Auth::user()->id, 'publish', $this->client->getTopic( $reply->topic->id ) );
				$this->mentioned( $this->request->get( 'reply' ), $reply->topic, $notification );
				$read->UpdatedRead( $reply->topic->id );
			}

			return Redirect::action( 'TopicController@show', [$reply->topic->id] )
			               ->with( 'success', 'Your Reply has been saved.' );
		}

		return Redirect::back()->withErrors( $this->reply->getErrors() )->withInput();
	}

	/**
	 * Deletes a reply.
	 * @permission delete_reply:optional
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete( $id ) {
		if ( count( $this->reply->get() ) > 1 ) {
			$reply = $this->reply->get( $id );
			if ( !is_null( $reply ) ) {
				if ( Auth::user()
				         ->hasPermission( $this->getPermission(), false ) || Auth::user()->id == $reply->user_id
				) {
					if ( $this->reply->delete( $id ) ) {
						return Redirect::back()->with( 'success', 'Your reply has been deleted.' );
					}
				}
			}
		}

		return Redirect::back()->with( 'error', 'Your reply could not be deleted.' );
	}
}