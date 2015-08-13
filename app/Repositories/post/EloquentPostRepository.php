<?php namespace App\Repositories\Post;

use App\Post;
use App\Star;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Repositories\CRepository;

class EloquentPostRepository extends CRepository implements PostRepository {

	public $id;

	// getter för id.
	public function getId(){
		return $this->id;
	}

	// hämtar ett eller alla block.
	public function get($id = null)
	{
		if(is_null($id)){
			$posts =  Post::all();
		}else{
			if(is_numeric($id)) {
				$post = Post::find($id);
			}else{
				$post = Post::where('slug', $id)->first();
			}
			$posts = $post;
		}
		return $posts;
	}

	// hämtar block som har en vis kategori eller är skapade den senaste veckan.
	public function getByCategory($id)
	{
		$posts = $this->get();
		$postsCollection = new Collection();
		foreach ($posts as $post) {
			if($id != 0){
				if($post->category->id == $id){
					if($post->private != 1){
						$postsCollection->add($post);
					}else{
						if(Auth::check()){
							if(Auth::user()->id == $post->user_id){
								$postsCollection->add($post);
							}
						}
					}
				}
			}else{
				// Skapar carbon objekt och sätter rätt tidszon
				$now = Carbon::now();
				$now->timezone = 'Europe/Stockholm';
				// Skapa en tidsstämpel på nu
				$nowTimestamp = strtotime($now);
				//Skapar en tidsstämpel som va för en vecka sedan;
				$weekAgoTimestamp = strtotime($now->subWeek());
				// kollar om blocket är skapat mellan dessa två tidsstämplar och lägger till det i post arrayen.
				if(strtotime($post->created_at) >= $weekAgoTimestamp && strtotime($post->created_at) <= $nowTimestamp){
					if($post->private != 1){
						$postsCollection->add($post);
					}
				}
			}
		}
		return $postsCollection;
	}

	public function getPopular($limit = 10, $min = 0){
		$posts =  Post::limit($limit)->get()->sortByDesc('starcount');
		$postsCollection = new Collection();
		foreach($posts as $post){
			if($post->starcount > $min){
				$postsCollection->add($post);
			}
		}
		return $postsCollection;
	}

	public function getNewest(){
		$posts = $this->get();
		$postsCollection = new Collection();
		foreach ($posts as $post) {
			// Skapar carbon objekt och sätter rätt tidszon
			$now = Carbon::now();
			$now->timezone = 'Europe/Stockholm';
			// Skapa en tidsstämpel på nu
			$nowTimestamp = strtotime($now);
			//Skapar en tidsstämpel som va för en vecka sedan;
			$weekAgoTimestamp = strtotime($now->subWeek());
			// kollar om blocket är skapat mellan dessa två tidsstämplar och lägger till det i post arrayen.
			if(strtotime($post->created_at) >= $weekAgoTimestamp && strtotime($post->created_at) <= $nowTimestamp){
				if($post->private != 1){
					$postsCollection->add($post);
				}
			}
		}
		return $postsCollection;
	}

	// hämtar block som har en viss ettiket.
	public function getByTag($id){
		$posts = $this->get();
		$postsCollection = new Collection();
		foreach ($posts as $post) {
			foreach ($post->tags as $tag) {
				if($id == $tag->id){
					if($post->private != 1){
						$postsCollection->add($post);
					}
					break;
				}
			}
		}
		return $postsCollection;
	}

	public function sort($posts, $sort = "date"){
		if($sort != 'name') {
			return $posts->sortByDesc(function ($item) use ($sort) {
				$sort = strtolower($sort);
				switch($sort) {
					case 'stars':
						return $item->stars['count'];
						break;
					case 'comments':
						return count($item->comments);
						break;
					default:
						return $item->created_at;
						break;
				}
			});
		}
		return $posts->sortBy(function ($item) {
			return $item->name;
		});
	}

	// duplicerar ett kodblock.
	public function duplicate($id){
		$post = $this->get($id);
		$input = array();
		$input['tags'] = array();
		foreach ($post->tags as $tag) {
			$input['tags'][] = $tag->id;
		}
		$existingPost = Post::where('name', '=', $post->name.' '.Auth::user()->id)->get();
		if(count($existingPost) < 1){
			$input['name'] = $post->name.' '.Auth::user()->id;
			$input['cat_id'] = $post->cat_id;
			$input['description'] = $post->description;
			$input['code'] = html_entity_decode($post->code);
			$input['private'] = 1;
			$input['org'] = $post->id;
			return $this->createOrUpdate($input);
		}
		return false;
	}

	// hämtar vilka kodblock som är skapade ur ett visst kodblock.
	public function getForked($id){
		return Post::where('org', $id)->get();
	}

	public function undo($input, $id){
		if(is_numeric($id)){
			$post = $this->get($id);
			//$post->setRevisionEnabled();
			if(isset($input['code'])){
				$input['code'] = html_entity_decode($input['code']);
			}
			$return = $this->save($input, $post);
			//$post->setRevisionEnabled();
			return $return;
		}
		return false;
	}

	private function save($input, $Post){
		$except = array('tags', '_token', '_url', 'token', '_method');

		foreach ($input as $key => $value) {
			if(!in_array($key, $except)){
				if($key != 'code'){
					$Post[$key] = $this->stripTrim($input[$key]);
				}else{
					$Post[$key] = htmlentities($input[$key]);
				}
			}
		}

		if($Post->slug == ''){
			$Post['slug'] = $Post->getSlug($Post->name);
		}

		if($Post->save()){
			$this->id = $Post->id;
			if(isset($input['tags'])){
				$Post->tags()->sync($input['tags']);
			}
			return true;
		}else{
			$this->errors = $Post::$errors;
			return false;
		}
	}

	// sparar eller uppdarerar ett block.
	public function createOrUpdate($input, $id = null)
	{
		if(!is_numeric($id)) {
			$Post = new Post;
			if(is_object(Auth::user())){
				$Post->user_id = Auth::user()->id;
			}else{
				Session::flash('error', 'You have not logged in');
				return false;
			}
		} else {
			$Post = $this->get($id);
		}

		return $this->save($input, $Post);
	}

	// tar bort ett block.
	public function delete($id){
		$Post = $this->get($id);
		if(!is_null($Post)) {
			return $Post->delete();
		}
		return false;
	}

	// skapar och tar bort en stjärna för ett block.
	public function createOrDeleteStar($post_id){
		$star = Star::where('user_id', '=', Auth::user()->id)->where('post_id', '=', $post_id)->first();
		$boolean = false;
		$action = 'delete';
		if($star != null){
			$boolean = $star->delete();
		}else{
			$action = 'create';
			$star = new Star;
			$star->post_id = $post_id;
			$star->user_id = Auth::user()->id;
			$boolean = $star->save();
		}
		return array($boolean, $action);
	}

	// söker bland blocken och skickar tillbaka de blocken den hittar en match hos.
	public function search($term, $filter = array('tag' => null, 'category' => null)){

		// Kollar om blocket innehåller söktermern i namn eller beskrvining
		$posts = Post::where('name', 'LIKE', '%'.$term.'%')->get()->merge(Post::where('description', 'LIKE', '%'.$term.'%')->get());

		// loopar igen alla inlägg och kollar om termen stämmer överens med ettiket, kategori eller användar-namn.
		foreach ($this->get() as $post) {
			if(strtolower($post->category->name) == strtolower($term) || strtolower($post->user->username) == strtolower($term)){
				$posts->add($post);
				break;
			}
			foreach ($post->tags as $tag) {
				if(strtolower($tag->name) == strtolower($term)){
					$posts->add($post);
					break;
				}
			}
		}

		if(!is_null($filter['category']) && $filter['category'] != '') {
			$category = $filter['category'];
			$posts = $posts->filter(function ($item) use ($category) {
				if($item->category->id == $category){
					return $item;
				}
			});
		}

		if(!is_null($filter['tag']) && $filter['tag'] != '') {
			$tag = $filter['tag'];
			$posts = $posts->filter(function ($item) use ($tag) {
				foreach($item->tags as $posttag){
					if($posttag->id == $tag){
						return $item;
					}
				}
			});
		}

		// hämtar dem igen och nu med alla relationer intakta.
		$postCollection = new Collection();
		foreach ($posts as $post) {
			if($post['private'] != 1) {
				$postCollection->add($this->get($post['id']));
			}
		}

		return $postCollection->unique();
	}

}