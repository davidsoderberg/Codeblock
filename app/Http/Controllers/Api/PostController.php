<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Post\PostRepository;
use App\Repositories\Star\StarRepository;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;


/**
 * Class PostController
 * @package App\Http\Controllers\Api
 */
class PostController extends ApiController {

	/**
	 * Shows a post.
	 *
	 * @param PostRepository $post
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function Posts(PostRepository $post, $id = null) {
		return $this->response([$this->stringData => $this->getCollection($post, $id)], 200);
	}

	/**
	 * Creating or updating a post.
	 *
	 * @param PostRepository $post
	 * @param null $id
	 *
	 * @return mixed
	 */
	public function createOrUpdatePost(PostRepository $post, $id = null) {
		if(!is_null($id)) {
			$user_id = $post->get($id)->user_id;
			if($user_id != Auth::user()->id) {
				return $this-response([$this->stringErrors => [$this->stringUser => 'You have not that created that codeblock']], 400);
			}
		}
		if($post->createOrUpdate($this->request->all(), $id)) {
			return $this->response([$this->stringMessage => 'Your block has been saved'], 201);
		}

		return $this->response([$this->stringErrors => $post->getErrors()], 400);
	}

	/**
	 * Deletes a post.
	 * @permission delete_post:optional
	 *
	 * @param  PostRepository $postRepository
	 * @param  int $id
	 *
	 * @return array
	 */
	public function deletePost(PostRepository $postRepository, $id) {
		$post = $postRepository->get($id);
		if(!is_null($post)) {
				if(Auth::check() && Auth::user()->id == $post->user_id || Auth::user()
				                                                              ->hasPermission($this->getPermission(), false)
				) {
					if($postRepository->delete($id)) {
						return $this->response([$this->stringMessage => 'Your codeblock has been deleted.'], 200);
					}
				} else {
					return $this->response([$this->stringErrors => 'You do not have permission to delete that codeblock.'], 400);
				}
		}

		return $this->response([$this->stringErrors => 'We could not delete that codeblock.'], 400);
	}

	/**
	 * Star a post.
	 *
	 * @param StarRepository $starRepository
	 * @param PostRepository $post
	 * @param $id
	 *
	 * @return mixed
	 */
	public function Star(StarRepository $starRepository,PostRepository $post, $id) {
		$star = $post->createOrDeleteStar($starRepository, $id);
		if($star[0]) {
			if($star[1] == 'create') {
				return $this->response([$this->stringMessage, 'You have now add a star to this codblock.'], 201);
			}

			return $this->response([$this->stringMessage, 'You have now removed a star from this codblock.'], 201);
		}

		return $this->response([$this->stringMessage, 'Something went wrong, please try again.'], 400);
	}
}
