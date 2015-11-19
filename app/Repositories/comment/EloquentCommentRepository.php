<?php namespace App\Repositories\Comment;

use App\Comment;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentCommentRepository
 * @package App\Repositories\Comment
 */
class EloquentCommentRepository extends CRepository implements CommentRepository {

	/**
	 * Fetch on or all comments.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get($id = null)
	{
		if(!is_null($id)){
			$comment = CollectionService::filter($this->get(), 'id', $id, 'first');
		}else{
			$comment = $this->cache('all', Comment::where('id', '!=', 0));
		}
		return $comment;
	}

	/**
	 * Creates or updates a comment.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool
	 */
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Comment = new Comment;
			$Comment->user_id = Auth::user()->id;
			if(isset($input['post_id'])) {
				$Comment->post_id = $this->stripTrim($input['post_id']);
			}
		} else {
			$Comment = $this->get($id);
		}

		if(isset($input['comment'])){
			$Comment->comment = $this->stripTrim($input['comment']);
		}

		if(isset($input['status'])){
			$Comment->status = $this->stripTrim($input['status']);
		}

		if(isset($input['parent'])){
			$Comment->parent = $this->stripTrim($input['parent']);
		}

		if($Comment->save()){
			return true;
		}else{
			$this->errors = $Comment::$errors;
			return false;
		}
	}

	/**
	 * Deletes a comment.
	 *
	 * @param $id
	 *
	 * @return bool|mixed
	 */
	public function delete($id){
		$Comment = $this->get($id);
		if($Comment != null) {
			foreach($Comment->rates as $rate) {
				$rate->delete();
			}
			return $Comment->delete();
		}
		return false;
	}

}