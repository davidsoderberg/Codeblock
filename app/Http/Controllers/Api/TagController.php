<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Tag\TagRepository;

/**
 * Class TagController
 * @package App\Http\Controllers\Api
 */
class TagController extends ApiController
{

    /**
     * Shows a tag.
     *
     * @param TagRepository $tag
     * @param null          $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Tag", description="Get all or one tag")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/api/v1/tags/{id?}")
     * @ApiParams(name="id", type="integer", nullable=true, description="tag id")
     */
    public function Tags(TagRepository $tag, $id = null)
    {
        return $this->response([$this->stringData => $this->getCollection($tag, $id)], 200);
    }

    /**
     * Creating or updating a tag.
     * @permission create_update_tags
     *
     * @param TagRepository $tag
     * @param null          $id
     *
     * @return mixed
     */
    private function createOrUpdateTag(TagRepository $tag, $id = null)
    {
        if ($tag->createOrUpdate($this->request->all(), $id)) {
            return $this->response([$this->stringMessage => 'Your tag has been saved'], 201);
        }

        return $this->response([$this->stringErrors => $tag->getErrors()], 400);
    }

    /**
     * Creating a tag.
     * @permission create_update_tags
     *
     * @param TagRepository $tag
     *
     * @return mixed
     *
     * @ApiDescription(section="Tag", description="Create tag")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/tags")
     * @ApiParams(name="name", type="string", nullable=false, description="tag name")
     */
    public function createTag(TagRepository $tag)
    {
        return $this->createOrUpdateTag($tag);
    }

    /**
     * Updating a tag.
     * @permission create_update_tags
     *
     * @param TagRepository $tag
     * @param null          $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Tag", description="Update tag")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/api/v1/tags/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="tag id")
     * @ApiParams(name="name", type="string", nullable=false, description="tag name")
     */
    public function updateTag(TagRepository $tag, $id)
    {
        return $this->createOrUpdateTag($tag, $id);
    }

    /**
     * Deletes a tag.
     * @permission delete_tags
     *
     * @param  TagRepository $tagRepository
     * @param  int           $id
     *
     * @return object
     *
     * @ApiDescription(section="Tag", description="Delete tag")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/api/v1/tags/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="tag id")
     */
    public function deleteTag(TagRepository $tagRepository, $id)
    {
        if ($tagRepository->delete($id)) {
            return $this->response([$this->stringMessage => 'The tag has been deleted.'], 200);
        }

        return $this->response([$this->stringErrors => 'The tag could not be deleted.'], 204);
    }
}
