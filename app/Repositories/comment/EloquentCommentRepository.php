<?php namespace App\Repositories\Comment;

use App\Comment;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;

class EloquentCommentRepository extends CRepository implements CommentRepository {

	// hämtar en eller alla kommentarer.
	public function get($id = null)
	{
		if(!is_null($id)){
			$comment = $this->cache($id, Comment::where('id',$id), 'first');
		}else{
			$comment = $this->cache('all', Comment::where('id', '!=', 0));
		}
		return $comment;
	}

	// skapar eller uppdaterar en kommentar.
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
			$Comment->comment = $input['comment'];
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

	// tar bort en kommentar.
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