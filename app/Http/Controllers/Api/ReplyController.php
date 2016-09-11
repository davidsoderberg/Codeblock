<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Reply\ReplyRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

/**
 * Class ReplyController
 * @package App\Http\Controllers\Api
 */
class ReplyController extends ApiController
{

    /**
     * Creating a reply.
     *
     * @param ReplyRepository $reply
     * @param null            $id
     *
     * @exclude
     * @permission create_reply:optional
     *
     * @return mixed
     *
     * @ApiDescription(section="Reply", description="Create reply")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/replies")
     * @ApiParams(name="reply", type="string", nullable=false, description="reply")
     * @ApiParams(name="user_id", type="integer", nullable=true, description="user id")
     * @ApiParams(name="topic_id", type="integer", nullable=false, description="topic id")
     */
    public function createReply(ReplyRepository $reply)
    {
        return $this->createOrUpdateReply($reply);
    }

    /**
     * Creating or updating a reply.
     *
     * @param ReplyRepository $reply
     * @param null            $id
     *
     * @return mixed
     */
    private function createOrUpdateReply(ReplyRepository $reply, $id = null)
    {
        if ( ! is_null($id)) {
            $user_id = $reply->get($id)->user_id;
            if ($user_id != Auth::user()->id && ! Auth::user()->hasPermission('create_reply', false)) {
                return $this->response([$this->stringErrors => [$this->stringUser => 'You have not created that reply']],
                    400);
            }
        }
        if ($reply->createOrUpdate(Input::all(), $id)) {
            return $this->response([$this->stringMessage => 'Your reply has been saved'], 201);
        }

        return $this->response([$this->stringErrors => $reply->getErrors()], 400);
    }

    /**
     * Updating a reply.
     *
     * @param ReplyRepository $reply
     * @param null            $id
     *
     * @exclude
     * @permission create_reply:optional
     *
     * @return mixed
     *
     * @ApiDescription(section="Reply", description="Update reply")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/api/v1/replies/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="reply id")
     * @ApiParams(name="reply", type="string", nullable=false, description="reply")
     * @ApiParams(name="user_id", type="integer", nullable=true, description="user id")
     * @ApiParams(name="topic_id", type="integer", nullable=false, description="topic id")
     */
    public function updateReply(ReplyRepository $reply, $id)
    {
        return $this->createOrUpdateReply($reply, $id);
    }

    /**
     * Deletes a reply.
     * @permission delete_reply:optional
     *
     * @param ReplyRepository $replyRepository
     * @param                 $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Reply", description="Delete reply")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/api/v1/replies/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="reply id")
     */
    public function deleteReply(ReplyRepository $replyRepository, $id)
    {
        if (count($replyRepository->get()) > 1) {
            $reply = $replyRepository->get($id);
            if ( ! is_null($reply)) {
                if (Auth::user()
                        ->hasPermission($this->getPermission(), false) || Auth::user()->id == $reply->user_id
                ) {
                    if ($replyRepository->delete($id)) {
                        return $this->response([$this->stringMessage => 'Your reply has been deleted.'], 200);
                    }
                }
            }
        }

        return $this->response([$this->stringErrors => 'Your reply could not be deleted.'], 204);
    }
}
