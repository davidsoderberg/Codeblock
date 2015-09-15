<?php namespace App\Repositories\Article;

use App\Article;
use App\Repositories\CRepository;

class EloquentArticleRepository extends CRepository implements ArticleRepository {

	// hämtar en eller alla artikel.
	public function get($id = null)
	{
		if(is_null($id)){
			return $this->cache('all', Article::where('id', '!=', 0));
		}else{
			if(is_numeric($id)) {
				return $this->cache($id, Article::where('id',$id), 'first');
			}else{
				return $this->cache($id, Article::where('slug',$id), 'first');
			}
		}
	}

	// skapar och uppdaterar en artikel.
	public function createOrUpdate($input, $id = null)
	{
		if(is_null($id)) {
			$Article = new Article();
		} else {
			$Article = $this->get($id);
		}

		if(isset($input['title'])){
			$Article->title = $this->stripTrim($input['title']);
			$Article->slug = $Article->getSlug($Article->title);
		}

		if(isset($input['body'])){
			$Article->body = $this->stripTrim($input['body']);
		}

		if($Article->save()){
			$this->flushCache($Article);
			return true;
		}else{
			$this->errors = $Article::$errors;
			return false;
		}
	}

	// tar bort en artikel.
	public function delete($id){
		$Article = $this->get($id);
		if($Article != null) {
			if($Article->delete()) {
				$this->flushCache($Article);

				return true;
			}
		}
		return false;
	}

}