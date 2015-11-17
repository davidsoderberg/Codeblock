<?php namespace App\Http\Controllers;

use App\Repositories\Read\ReadRepository;
use App\Repositories\Reply\ReplyRepository;
use App\Repositories\Topic\TopicRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

/**
 * Class TopicController
 * @package App\Http\Controllers
 */
class TopicController extends Controller {

	/**
	 * @param TopicRepository $topic
	 * @param ReplyRepository $reply
	 */
	public function __construct( TopicRepository $topic, ReplyRepository $reply ) {
		parent::__construct();
		$this->topic = $topic;
		$this->reply = $reply;
	}

	/**
	 * Visar en tråd.
	 *
	 * @param ReadRepository $read
	 * @param $id
	 * @param int $reply
	 *
	 * @return mixed
	 */
	public function show( ReadRepository $read, $id, $reply = 0 ) {
		$topic = $this->topic->get( $id );
		if ( Auth::check() && !is_null( $topic ) ) {
			$this->client->send( $topic, Auth::user()->id, 'subscribe', $this->client->getTopic( $topic->id ) );
		}
		$reply = $this->reply->get( $reply );
		if ( !Auth::user()->hasPermission( 'create_reply', false ) ) {
			if ( isset( $reply->user_id ) && $reply->user_id != Auth::user()->id ) {
				return Redirect::back()->with( 'error', 'You can´t edit other users replies.' );
			}
		}
		$read->hasRead( $id );

		return View::make( 'topic.show' )
		           ->with( 'title', 'Topic: ' . $topic->title )
		           ->with( 'topic', $topic )
		           ->with( 'editReply', $reply );
	}

	/**
	 * Skapar eller uppdaterar en tråd.
	 * @permission create_topic:optional
	 *
	 * @param ReadRepository $read
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function createOrUpdate( ReadRepository $read, $id = null ) {
		if ( !is_null( $id ) ) {
			if ( !Auth::user()->hasPermission( $this->getPermission(), false ) ) {
				$topic = $this->topic->get( $id );
				$reply = $topic->replies()->first();
				if ( Auth::user()->id != $reply->user_id ) {
					return Redirect::back()->with( 'error', 'You can´t edit other users topics.' );
				}
			}
		}
		$input = $this->request->all();
		if ( $this->topic->createOrUpdate( $this->request->all(), $id ) ) {
			if ( is_null( $id ) ) {
				$input['topic_id'] = $this->topic->topic->id;
				if ( !$this->reply->createOrUpdate( $input ) ) {
					$this->delete( $this->topic->topic->id );

					return Redirect::back()->withErrors( $this->reply->getErrors() )->withInput();
				}
				$read->hasRead( $this->topic->topic->id );

				return Redirect::back()->with( 'success', 'Your topic has been created.' );
			}

			return Redirect::back()->with( 'success', 'Your topic has been updated.' );
		}

		return Redirect::back()->withErrors( $this->topic->getErrors() )->withInput();
	}

	/**
	 * Tar bort en tråd.
	 * @permission delete_topic:optional
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete( $id ) {
		try {
			$topic = $this->topic->get( $id );
			if ( !is_null( $topic ) ) {
				$reply = $topic->replies()->first();
				if ( Auth::user()
				         ->hasPermission( $this->getPermission(), false ) || Auth::user()->id == $reply->user_id
				) {
					$forum_id = $this->topic->get( $id )->forum_id;
					if ( $this->topic->delete( $id ) ) {
						return Redirect::action( 'ForumController@show', [$forum_id] )
						               ->with( 'success', 'Your topic has been deleted.' );
					}
				}
			}

		} catch( \Exception $e ) {
		}

		return Redirect::back()->with( 'error', 'That topic could not be deleted.' );
	}
}