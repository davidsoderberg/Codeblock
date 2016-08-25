<?php namespace App\Http\Controllers;

use App\Models\NotificationType;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Post\PostRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Class CommentController
 * @package App\Http\Controllers
 */
class CommentController extends Controller
{

    /**
     * Constructor for CommentController
     *
     * @param CommentRepository $comment
     */
    public function __construct(CommentRepository $comment)
    {
        parent::__construct();
        $this->comment = $comment;
    }

    /**
     * Render index view for comments.
     * @permission view_comments
     * @return object
     */
    public function index()
    {
        return View::make('comment.index')->with('title', 'Comments')->with('comments', $this->comment->get());
    }

    /**
     * Render list of comments.
     *
     * @return mixed
     */
    public function listComments()
    {
        return View::make('comment.index')->with('title', 'Comments')->with('comments', Auth::user()->comments);
    }

    /**
     * Creates or updates a comment.
     *
     * @param PostRepository $post
     * @param  int $id
     *
     * @return object
     */
    public function createOrUpdate(PostRepository $post, $id = null)
    {
        if ($this->comment->createOrUpdate($this->request->all(), $id)) {
            if (!is_null($id)) {
                if (Str::contains(URL::previous(), 'posts')) {
                    return Redirect::action('PostController@show', $this->comment->get($id)->post_id)
                        ->with('success', 'This comment have been updated.');
                } else {
                    return Redirect::back()->with('success', 'This comment have been updated.');
                }
            }

            $post = $post->get($this->request->get('post_id'));
            $this->mentioned($this->request->get('comment'), $post);
            $this->client->new_comment($this->comment->Comment, Auth::user()->id, $post->id);

            if (! in_array(Auth::user()->id, $this->client->getUsers('presence-post_' . $post->id))) {
                if (Auth::user()->id != $post->user_id) {
                    if (! $this->client->send($post, $post->user_id)) {
                        $this->send_notification($post->user_id, NotificationType::COMMENT, $post);
                    }
                }
            }

            return Redirect::back()->with('success', 'Your comment have been created.');
        }

        return Redirect::back()->withErrors($this->comment->getErrors())->withInput();
    }

    /**
     * Edit a comment
     *
     * @permission edit_comments:optional
     *
     * @param  int $id
     *
     * @return object
     */
    public function edit($id)
    {
        $comment = $this->comment->get($id);
        if (Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()
                ->hasPermission($this->getPermission(), false)
        ) {
            return View::make('comment.edit')->with('title', 'Edit comments')->with('comment', $comment);
        } else {
            return Redirect::back()->with('error', 'You do not have permission to edit that comment.');
        }
    }

    /**
     * Deletes a comment.
     *
     * @permission delete_comments:optional
     *
     * @param  int $id
     *
     * @return object
     */
    public function delete($id)
    {
        try {
            $comment = $this->comment->get($id);
            if (Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()
                    ->hasPermission($this->getPermission(), false)
            ) {
                if ($this->comment->delete($id)) {
                    return Redirect::back()->with('success', 'That comment has now been deleted.');
                }
            } else {
                return Redirect::back()->with('error', 'You do not have permission to delete that comment.');
            }
        } catch (\Exception $e) {
        }

        return Redirect::back()->with('error', 'We could not delete that comment.');
    }
}
