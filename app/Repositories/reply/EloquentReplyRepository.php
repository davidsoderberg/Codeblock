<?php namespace App\Repositories\Reply;

use App\Reply;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;

class EloquentReplyRepository extends CRepository implements ReplyRepository {

	public $Reply;

	// hämtar en eller alla kategorier.
	public function get($id = null)
	{
		if(is_null($id)){
			return Reply::all();
		}else{
			return Reply::find($id);
		}
	}

	// skapar och uppdaterar en kategori.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Reply = new Reply;
		} else {
			$Reply = Reply::find($id);
		}

		if(isset($input['reply'])){
			$Reply->reply = $input['reply'];
		}

		if(isset($input['topic_id'])){
			$Reply->topic_id = $this->stripTrim($input['topic_id']);
		}

		$Reply->user_id = Auth::user()->id;

		if($Reply->save()){
			$this->Reply = $Reply;
			return true;
		}else{
			$this->errors = $Reply::$errors;
			return false;
		}
	}

	// tar bort en kategori.
	public function delete($id){
		$Reply = Reply::find($id);
		if($Reply == null){
			return false;
		}
		return $Reply->delete();
	}

}