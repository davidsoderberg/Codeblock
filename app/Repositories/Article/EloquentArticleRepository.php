<?php namespace App\Repositories\Article;

use App\Article;
use App\Repositories\CRepository;

class EloquentArticleRepository extends CRepository implements ArticleRepository {

	// hämtar en eller alla artikel.
	public function get($id = null)
	{
		if(is_null($id)){
			return Article::all();
		}else{
			return Article::find($id);
		}
	}

	// skapar och uppdaterar en artikel.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Article = new Article();
		} else {
			$Article = Article::find($id);
		}

		if(isset($input['title'])){
			$Article->title = $this->stripTrim($input['title']);
		}

		if(isset($input['body'])){
			$Article->body = $this->stripTrim($input['body']);
		}

		if($Article->save()){
			return true;
		}else{
			$this->errors = $Article::$errors;
			return false;
		}
	}

	// tar bort en artikel.
	public function delete($id){
		$Article = Article::find($id);
		if($Article == null){
			return false;
		}
		return $Article->delete();
	}

}