<?php namespace App\Repositories\Tag;

use App\Tag;
use App\Repositories\CRepository;

class EloquentTagRepository extends CRepository implements TagRepository {

	// hämtar en eller alla ettiketer.
	public function get($id = null)
	{
		if(is_null($id)){
			return Tag::all();
		}else{
			return Tag::find($id);
		}
	}

	// skapar eller uppdaterar en ettiket.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Tag = new Tag;
		} else {
			$Tag = Tag::find($id);
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
		$Tag = Tag::find($id);
		if($Tag == null){
			return false;
		}
		if(!empty($Tag->posts[0])){
			$Tag->posts->detach();
		}
		return $Tag->delete();
	}

}