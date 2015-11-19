<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
Use App\Repositories\Forum\ForumRepository;

/**
 * Class ForumController
 * @package App\Http\Controllers\Api
 */
class ForumController extends ApiController {

	/**
	 * Shows a forum.
	 *
	 * @param ForumRepository $forum
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function forums(ForumRepository $forum, $id = null) {
		return $this->response([$this->stringData => $this->getCollection($forum, $id)], 200);
	}

	/**
	 * Deletes a forum.
	 * @permission delete_forums
	 *
	 * @param $forumRepository
	 * @param $id
	 *
	 * @return mixed
	 */
	public function deleteForum(ForumRepository $forumRepository, $id) {
		if($forumRepository->delete($id)) {
			return $this->response([$this->stringMessage => 'Your forum has been deleted.'], 200);
		}

		return $this->response([$this->stringErrors => 'We could not delete that forum.'], 204);
	}

}
