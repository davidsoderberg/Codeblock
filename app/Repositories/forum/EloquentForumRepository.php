<?php namespace App\Repositories\Forum;

use App\Forum;
use App\Repositories\CRepository;

class EloquentForumRepository extends CRepository implements ForumRepository {

	// hämtar en eller alla forum.
	public function get($id = null)
	{
		if(!is_null($id)){
			return $this->cache($id, Forum::where('id',$id), 'first');
		}else{
			return $this->cache('all', Forum::where('id', '!=', 0));
		}
	}

	// skapar och uppdaterar en forum.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Forum = new Forum;
		} else {
			$Forum = $this->get($id);
		}

		if(isset($input['title'])){
			$Forum->title = $this->stripTrim($input['title']);
		}

		if(isset($input['description'])){
			$Forum->description = $this->stripTrim($input['description']);
		}

		if($Forum->save()){
			return true;
		}else{
			$this->errors = $Forum::$errors;
			return false;
		}
	}

	// tar bort en forum.
	public function delete($id){
		$Forum = $this->get($id);
		if($Forum == null){
			return false;
		}
		return $Forum->delete();
	}

}