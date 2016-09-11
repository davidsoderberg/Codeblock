<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Forum\ForumRepository;
use App\Services\Transformer;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ForumController
 * @package App\Http\Controllers\Api
 */
class ForumController extends ApiController
{

    /**
     * Shows a forum.
     *
     * @param ForumRepository $forum
     * @param null $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Forum", description="Get all or one forum")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/api/v1/forums/{id?}")
     * @ApiParams(name="id", type="integer", nullable=true, description="forum id")
     */
    public function forums(ForumRepository $forum, $id = null)
    {
        $forums = $this->getCollection($forum, $id);
        Transformer::walker($forums);
        return $this->response([$this->stringData => $forums], 200);
    }

    /**
     * Deletes a forum.
     * @permission delete_forums
     *
     * @param $forumRepository
     * @param $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Forum", description="Delete forum")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/api/v1/forums/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="forum id")
     */
    public function deleteForum(ForumRepository $forumRepository, $id)
    {
        if ($forumRepository->delete($id)) {
            return $this->response([$this->stringMessage => 'Your forum has been deleted.'], 200);
        }

        return $this->response([$this->stringErrors => 'We could not delete that forum.'], 204);
    }
}
