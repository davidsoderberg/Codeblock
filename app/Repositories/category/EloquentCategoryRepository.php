<?php namespace App\Repositories\Category;

use App\Category;
use App\Repositories\CRepository;
use App\Services\CollectionService;

class EloquentCategoryRepository extends CRepository implements CategoryRepository {

	// hämtar en eller alla kategorier.
	public function get($id = null)
	{
		if(is_null($id)){
			return $this->cache('all', Category::where('id', '!=', 0));
		}else{
			if(is_numeric($id)) {
				return CollectionService::filter($this->get(), 'id', $id, 'first');
			}else{
				return CollectionService::filter($this->get(), 'name', $id, 'first');
			}
		}
	}

	// skapar och uppdaterar en kategori.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Category = new Category;
		} else {
			$Category = $this->get($id);
		}

		if(isset($input['name'])){
			$Category->name = $this->stripTrim($input['name']);
		}

		if($Category->save()){
			return true;
		}else{
			$this->errors = $Category::$errors;
			return false;
		}
	}

	// tar bort en kategori.
	public function delete($id){
		$Category = $this->get($id);
		if($Category != null) {
			return $Category->delete();
		}
		return false;
	}

}