<?php namespace App\Repositories\Topic;

use App\Topic;
use App\Repositories\CRepository;

class EloquentTopicRepository extends CRepository implements TopicRepository {

	// hämtar en eller alla kategorier.
	public function get($id = null)
	{
		if(is_null($id)){
			return Topic::all();
		}else{
			return Topic::find($id);
		}
	}

	// skapar och uppdaterar en kategori.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Topic = new Topic;
		} else {
			$Topic = Topic::find($id);
		}

		if(isset($input['title'])){
			$Topic->title = $this->stripTrim($input['title']);
		}

		if(isset($input['description'])){
			$Topic->description = $this->stripTrim($input['description']);
		}

		if($Topic->save()){
			return true;
		}else{
			$this->errors = $Topic::$errors;
			return false;
		}
	}

	// tar bort en kategori.
	public function delete($id){
		$Topic = Topic::find($id);
		if($Topic == null){
			return false;
		}
		return $Topic->delete();
	}

}