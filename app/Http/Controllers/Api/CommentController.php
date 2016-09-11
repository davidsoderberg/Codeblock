<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Rate\RateRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class CommentController
 * @package App\Http\Controllers\Api
 */
class CommentController extends ApiController
{

    /**
     * Creating or updating a comment.
     *
     * @param CommentRepository $comment
     * @param null $id
     *
     * @return mixed
     */
    private function createOrUpdateComment(CommentRepository $comment, $id = null)
    {
        if (!is_null($id)) {
            $user_id = $comment->get($id)->user_id;
            if ($user_id != Auth::user()->id && !Auth::user()->hasPermission('edit_comments', false)) {
                return $this->response([$this->stringErrors => [$this->stringUser => 'You have not created that comment']],
                    400);
            }
        }
        if ($comment->createOrUpdate($this->request->all(), $id)) {
            return $this->response([$this->stringMessage => 'Your comment has been saved'], 201);
        }

        return $this->response([$this->stringErrors => $comment->getErrors()], 400);
    }

    /**
     * Creating a comment.
     *
     * @param CommentRepository $comment
     *
     * @return mixed
     *
     * @ApiDescription(section="Comment", description="Create comment")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/comments")
     * @ApiParams(name="comment", type="string", nullable=false, description="comment")
     * @ApiParams(name="post_id", type="integer", nullable=false, description="codeblock id")
     * @ApiParams(name="parent", type="integer", nullable=true, description="parent comment id")
     */
    public function createComment(CommentRepository $comment)
    {
        return $this->createOrUpdateComment($comment);
    }

    /**
     * Updating a comment.
     *
     * @param CommentRepository $comment
     * @param null               $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Comment", description="Update comment")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/api/v1/comments/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="comment id")
     * @ApiParams(name="comment", type="string", nullable=false, description="comment")
     * @ApiParams(name="post_id", type="integer", nullable=false, description="codeblock id")
     * @ApiParams(name="parent", type="integer", nullable=true, description="parent comment id")
     */
    public function updateComment(CommentRepository $comment, $id)
    {
        return $this->createOrUpdateComment($comment, $id);
    }

    /**
     * Deletes a comment.
     * @permission delete_comments:optional
     *
     * @param CommentRepository $commentRepository
     * @param  int $id
     *
     * @return object
     *
     * @ApiDescription(section="Comment", description="Delete comment")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/api/v1/comments/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="comment id")
     */
    public function deleteComment(CommentRepository $commentRepository, $id)
    {
        try {
            $comment = $commentRepository->get($id);
            if (Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()
                    ->hasPermission($this->getPermission(), false)
            ) {
                if ($commentRepository->delete($id)) {
                    return $this->response([$this->stringMessage => 'That comment has now been deleted.'], 200);
                }
            } else {
                return $this->response([$this->stringErrors => 'You do not have permission to delete that comment.'],
                    204);
            }
        } catch (\Exception $e) {
        }

        return $this->response([$this->stringErrors => 'We could not delete that comment.'], 204);
    }

    /**
     * Create a rate.
     *
     * @param RateRepository $rate
     * @param $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Comment", description="Rate comment")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/comments/rate/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="comment id")
     */
    public function Rate(RateRepository $rate, $id)
    {
        if ($rate->rate($id, '+')) {
            return $this->response([$this->stringMessage => 'Your up rated a comment.'], 200);
        } else {
            if ($rate->rate($id, '-')) {
                return $this->response([$this->stringMessage => 'Your down rated a comment.'], 200);
            }
        }

        return $this->response([$this->stringMessage, 'You could not rate that comment, please try agian'], 400);
    }
}
