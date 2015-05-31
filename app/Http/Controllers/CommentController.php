<?php namespace App\Http\Controllers;

use App\NotificationType;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\Post\PostRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CommentController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'PostController@index');
	|
	*/

	/**
	 * @param CommentRepository $comment
	 */
	public function __construct(CommentRepository $comment)
	{
		parent::__construct();
		$this->comment = $comment;
	}

	/**
	 * Visar index vyn för kommentarerna
	 * @permission view_comments
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index()
	{
		return View::make('comment.index')->with('title', 'Comments')->with('comments', $this->comment->get());
	}

	/**
	 * @return mixed
	 */
	public function listComments(){
		return View::make('comment.index')->with('title', 'Comments')->with('comments', Auth::user()->comments);
	}

	/**
	 * Skapa och uppdatera en kommentar.
	 * @param  int $id id för kommentar som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function createOrUpdate(NotificationRepository $notification, PostRepository $post, $id = null)
	{
		if($this->comment->createOrUpdate($this->request->all(), $id)){
			if(!is_null($id)){
				if(Str::contains(URL::previous(), 'posts')){
					return Redirect::action('PostController@show', $this->comment->get($id)->post_id)->with('success', 'This comment have been updated.');
				}else {
					return Redirect::back()->with('success', 'This comment have been updated.');
				}
			}
			$post = $post->get($this->request->get('post_id'));
			if(Auth::user()->id != $post->user_id) {
				$notification->send($post->user_id, NotificationType::COMMENT, $post);
				$this->client->send($post, $post->user_id);
			}
			$this->mentioned($this->request->get('comment'), $post, $notification);
			return Redirect::back()->with('success', 'Your comment have been created.');
		}

		return Redirect::back()->withErrors($this->comment->getErrors())->withInput();
	}

	/**
	 * vissar vyn för att uppdatera en kommentar.
	 * @permission edit_comments:optional
	 * @param  int $id id för kommentaren som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function edit($id){
		$comment = $this->comment->get($id);
		if(Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
			return View::make('comment.edit')->with('title', 'Edit comments')->with('comment', $comment);
		}else{
			return Redirect::back()->with('error', 'You do not have permission to edit that comment.');
		}
	}

	/**
	 * Ta bort en kommentar
	 * @permission delete_comments:optional
	 * @param  int $id id för kommentaren som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id){
		try {
			$comment = $this->comment->get($id);
			if(Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($this->comment->delete($id)) {
					return Redirect::back()->with('success', 'That comment has now been deleted.');
				}
			} else {
				return Redirect::back()->with('error', 'You do not have permission to delete that comment.');
			}
		} catch(\Exception $e){}

		return Redirect::back()->with('error', 'We could not delete that comment.');
	}

}