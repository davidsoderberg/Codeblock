<?php namespace App\Repositories\Reply;

use App\Reply;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

class EloquentReplyRepository extends CRepository implements ReplyRepository {

	public $Reply;

	// hämtar en eller alla svar.
	public function get($id = null)
	{
		if(is_null($id)){
			return $this->cache('all', Reply::where('id', '!=', 0));
		}else{
			return $this->cache($id, Reply::where('id',$id), 'first');
		}
	}

	// skapar och uppdaterar ett svar.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Reply = new Reply;
		} else {
			$Reply = $this->get($id);
		}

		if(isset($input['reply'])){
			$Reply->reply = $this->stripTrim($input['reply']);
		}

		if(isset($input['topic_id'])){
			$Reply->topic_id = $this->stripTrim($input['topic_id']);
		}

		if(!isset($Reply->user_id)){
			$Reply->user_id = Auth::user()->id;
		}

		if($Reply->save()){
			$this->Reply = $Reply;
			return true;
		}else{
			$this->errors = $Reply::$errors;
			return false;
		}
	}

	// tar bort ett svar.
	public function delete($id){
		$Reply = $this->get($id);
		if($Reply == null){
			return false;
		}
		return $Reply->delete();
	}

}