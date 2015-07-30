<?php namespace App\Repositories\Category;

use App\Category;
use App\Repositories\CRepository;

class EloquentCategoryRepository extends CRepository implements CategoryRepository {

	// hämtar en eller alla kategorier.
	public function get($id = null)
	{
		if(is_null($id)){
			return Category::all();
		}else{
			if(is_numeric($id)) {
				return Category::find($id);
			}else{
				return Category::where('name', $id)->first();
			}
		}
	}

	// skapar och uppdaterar en kategori.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Category = new Category;
		} else {
			$Category = Category::find($id);
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
		$Category = Category::find($id);
		if($Category == null){
			return false;
		}
		return $Category->delete();
	}

}