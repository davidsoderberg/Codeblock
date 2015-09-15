<?php namespace App\Repositories\Tag;

use App\Tag;
use App\Repositories\CRepository;

class EloquentTagRepository extends CRepository implements TagRepository {

	// hämtar en eller alla ettiketer.
	public function get($id = null)
	{
		if(is_null($id)){
			return $this->cache('all', Tag::where('id', '!=', 0));
		}else{
			if(is_numeric($id)) {
				return $this->cache($id, Tag::where('id',$id), 'first');
			}else{
				return $this->cache($id, Tag::where('name',$id), 'first');
			}
		}
	}

	// skapar eller uppdaterar en ettiket.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Tag = new Tag;
		} else {
			$Tag = $this->get($id);
		}

		if(isset($input['name'])){
			$Tag->name = $this->stripTrim($input['name']);
		}


		if($Tag->save()){
			return true;
		}else{
			$this->errors = Tag::$errors;
			return false;
		}
	}

	// tar bort en ettiket.
	public function delete($id){
		$Tag = $this->get($id);
		if($Tag == null){
			return false;
		}
		if(!empty($Tag->posts[0])){
			$Tag->posts->detach();
		}
		return $Tag->delete();
	}

}