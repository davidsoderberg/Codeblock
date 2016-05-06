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
	 * @param null $id
	 *
	 * @return mixed
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
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function createOrUpdateTag(TagRepository $tag, $id = null)
	{
		if ($tag->createOrUpdate($this->request->all(), $id)) {
			return $this->response([$this->stringMessage => 'Your tag has been saved'], 201);
		}

		return $this->response([$this->stringErrors => $tag->getErrors()], 400);
	}

	/**
	 * Deletes a tag.
	 * @permission delete_tags
	 *
	 * @param  TagRepository $tagRepository
	 * @param  int $id
	 *
	 * @return object
	 */
	public function deleteTag(TagRepository $tagRepository, $id)
	{
		if ($tagRepository->delete($id)) {
			return $this->response([$this->stringMessage => 'The tag has been deleted.'], 200);
		}

		return $this->response([$this->stringErrors => 'The tag could not be deleted.'], 204);
	}

}
