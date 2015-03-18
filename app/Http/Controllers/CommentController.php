<?php namespace App\Http\Controllers;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\Post\PostRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

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

	public function __construct(CommentRepository $comment)
	{
		$this->comment = $comment;
	}

	/**
	 * Visar index vyn för kommentarerna
	 * @return objekt     objekt som innehåller allt som behövs i vyn
	 */
	public function index()
	{
		if(Auth::user()->role == 2){
			$comments = $this->comment->get();
		}else{
			$posts = Auth::user()->posts;
			$commentsArray = array();

			foreach ($posts as $post) {
				foreach ($post->comments as $comment) {
					$commentsArray[] = $comment;
				}
			}
			$comments = $commentsArray;
		}

		return View::make('comment.index')->with('title', 'Comments')->with('comments', $comments);
	}

	/**
	 * Skapa och uppdatera en kommentar.
	 * @param  int $id id för kommentar som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function createOrUpdate(NotificationRepository $notification, PostRepository $post, $id = null)
	{
		if($this->comment->createOrUpdate(Input::all(), $id)){
			if(!is_null($id)){
				return Redirect::back()->with('success', 'This comment have been saved.');
			}
			$this->mentioned(Input::get('comment'), $post->get(Input::get('post_id')), $notification);
			return Redirect::back();
		}

		return Redirect::back()->withErrors($this->comment->getErrors())->withInput();
	}

	/**
	 * vissar vyn för att uppdatera en kommentar.
	 * @param  int $id id för kommentaren som skall uppdateras
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function edit($id){
		$comment = $this->comment->get($id);
		return View::make('comment.edit')->with('title', 'Edit comments')->with('comment', $comment);
	}

	/**
	 * Ta bort en kommentar
	 * @param  int $id id för kommentaren som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function delete($id){
		if($this->comment->delete($id)){
			return Redirect::back();
		}

		return Redirect::back();
	}

}