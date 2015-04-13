<?php namespace App\Repositories\Post;

use App\Post;
use App\Star;
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
			foreach ($posts as $post) {
				$post->category = $post->category($post->category)->first();
				$post->comments = $post->comments;
				$post->posttags = $post->posttags;
				$post->stars = $this->getStars($post->stars);
				//$post->category->lang = $this->jsSwitch($post->category->name);
				$post->org = $this->get($post->org);
				$post->forked = count($this->where(array('org', '=', $post->id)));
			}
			return $posts;
		}else{
			if(is_numeric($id)) {
				$post = Post::find($id);
			}else{
				$post = Post::where('slug', $id)->first();
			}
			if(!is_null($post)){
				$post->category = $post->category($post->category)->first();
				$post->comments = $post->comments;
				$post->posttags = $post->posttags;
				$post->stars = $this->getStars($post->stars);
				//$post->category->name = $this->jsSwitch($post->category->name);
				$post->org = $this->get($post->org);
				$post->forked = count($this->where(array('org', '=', $post->id)));
			}
			return $post;
		}
	}

	// hämtar block som har en vis kategori eller är skapade den senaste veckan.
	public function getByCategory($id)
	{
		$posts = $this->get();
		$postArray = array();
		foreach ($posts as $post) {
			if($id != 0){
				if($post->category->id == $id){
					if($post->private != 1){
						$postArray[] = $post;
					}else{
						if(Auth::check()){
							if(Auth::user()->id == $post->user_id){
								$postArray[] = $post;
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
						$postArray[] = $post;
					}
				}
			}
		}
		return $postArray;
	}

	// hämtar block som har en viss ettiket.
	public function getByTag($id){
		$posts = $this->get();
		$postArray = array();
		foreach ($posts as $post) {
			foreach ($post->posttags as $tag) {
				if($id == $tag->id){
					if($post->private != 1){
						$postArray[] = $post;
					}
					break;
				}
			}
		}
		return $postArray;
	}

	// duplicerar ett kodblock.
	public function duplicate($id){
		$post = $this->get($id);
		$input = array();
		$input['tags'] = array();
		foreach ($post->posttags as $tag) {
			$input['tags'][] = $tag->id;
		}
		$existingPost = $this->where(array('name', '=', $post->name.' '.Auth::user()->id));
		if(count($existingPost) < 1){
			$input['name'] = $post->name.' '.Auth::user()->id;
			$input['category'] = $post->category->id;
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
		$posts = $this->where(array('org', '=', $id));
		foreach ($posts as $post) {
				$post->category = $post->category($post->category)->first();
				$post->posttags = $post->posttags;
				$post->stars = $this->getStars($post->stars);
				//$post->category->lang = $this->jsSwitch($post->category->name);
				$post->org = $this->get($post->org);
				$post->forked = count($this->where(array('org', '=', $post->id)));
		}
		return $posts;
	}

	// hämtar block där värderna stämmer överens med vilkoren som är inputen till denna metod.
	private function where($where){
		if(is_array($where[0])){
			$post = Post::where($where[0][0], $where[0][1], $where[0][2]);
			for ($i=1; $i < count($where) ; $i++) {
				$post = $post->where($where[$i][0], $where[$i][1], $where[$i][2]);
			}
			return $post->get();
		}
		return Post::where($where[0], $where[1], $where[2])->get();
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
			$Post = Post::find($id);
		}

		foreach ($input as $key => $value) {
			if($key != 'tags' && $key != '_token'){
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
				$Post->posttags()->sync($input['tags']);
			}
			return true;
		}else{
			$this->errors = $Post::$errors;
			return false;
		}
	}

	// tar bort ett block.
	public function delete($id){
		$Post = Post::find($id);
		if(!is_null($Post)) {
			return $Post->delete();
		}
		return false;
	}

	// räknar ut antalet stjärnor och vilka som har stjärnmärkt ett block.
	private function getStars($stars){
		$starsCount = count($stars);
		$userArray = array();
		foreach ($stars as $star) {
			$userArray[] = $star->user_id;
		}
		return array('count' => $starsCount, 'userArray' => $userArray);
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

	// bytter ut de kategorier som inte stämmer överens med javascripts kategorierna hos codemirror.
	public function jsSwitch($category){
		$category = strtolower($category);
		$CodeMirrorcategories = array(
			'html' => 'xml',
			'c#' => 'clike',
			'asp.net' => 'clike',
			'php' => array('clike', 'xml', 'javascript', 'css', 'htmlmixed')
		);
		if(array_key_exists($category, $CodeMirrorcategories)){
			$current = $CodeMirrorcategories[$category];
			if(is_array($current)){
				$current = array_merge(array($category), $current);
			}
			return $current;
		}
		return $category;
	}

	// söker bland blocken och skickar tillbaka de blocken den hittar en match hos.
	public function search($term){

		// Kollar om blocket innehåller söktermern i namn eller beskrvining
		$namePosts = Post::where('name', 'LIKE', '%'.$term.'%')->get()->toArray();
		$descriptionPosts = Post::where('description', 'LIKE', '%'.$term.'%')->get()->toArray();

		// Lägger samman båda arrayerna
		$LikePosts = $this->merge_search($namePosts, $descriptionPosts);

		// Hämtar alla block.
		$all_posts = $this->get();
		$categoryTagPosts = array();

		// loopar igen alla inlägg och kollar om termen stämmer överens med ettiket, kategori eller användar-namn.
		foreach ($all_posts as $post) {
			if(strtolower($post->category->name) == strtolower($term) || strtolower($post->user->username) == strtolower($term)){
				$categoryTagPosts[] = $post;
				break;
			}
			foreach ($post->posttags as $tag) {
				if(strtolower($tag->name) == strtolower($term)){
					$categoryTagPosts[] = $post;
					break;
				}
			}
		}

		// Lägger sammman båda två arrayerna som skapas ovan.
		$posts = $this->merge_search($LikePosts, $categoryTagPosts);

		// hämtar dem igen och nu med alla relationer intakta.
		$postArray = array();
		foreach ($posts as $post) {
			$postArray[] = $this->get($post['id']);
		}

		return $postArray;
	}

	// slår ihop två sökningar till ett resultat.
	private function merge_search($resultarray, $array){
		foreach ($array as $value) {
			if(!in_array($value, $resultarray)){
				$resultarray[] = $value;
			}
		}
		return $resultarray;
	}

}