<?php namespace App\Http\Controllers;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\CRepository;
use App\Repositories\Forum\ForumRepository;
use App\Repositories\Post\PostRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Reply\ReplyRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Rate\RateRepository;
use App\Repositories\Topic\TopicRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {

	private $page = 1;

	private $perPage = 10;

	private $limit = 0;

	private $sort = '';

	private $collection;

	public function __construct(){
		parent::__construct();
		$this->setParams();
	}

	private function hateoas($id = null){
		$route = $this->request->route();
		$filterOn = $route->getAction()['prefix'];
		$routeArray = array();
		foreach(Route::getRoutes()->getRoutes() as $route){
			if(Str::contains($route->uri(), $filterOn)){
				if(in_array('jwt', $route->middleware())){
					if(!is_null($this->request->get('token'))){
						$routeArray[] = $this->getURL($id, $route);
					}
				}else{
					$routeArray[] = $this->getURL($id, $route);
				}
			}
		}
		return $routeArray;
	}

	/**
	 * @param $id
	 * @param $route
	 * @return array
	 */
	private function getURL($id = null, $route) {
		$method = $route->methods()[0];
		$params = $_GET;
		if(Str::contains($route->uri(), 'id')) {
			$url = $route->uri().$this->joinParams($params);
			if(is_null($id)){
				$url = str_replace('/{id?}', '', $url);
			}else{
				$url = str_replace('{id}', $id, $url);
				$url = str_replace('{id?}', $id, $url);
			}
		}else {
			$url =	$route->uri().$this->joinParams($params);
		}
		return array('method' => $method, 'uri' => $url);
	}

	private function joinParams($params){
		$first = true;
		$string = '';
		foreach($params as $key => $value){
			if($first) {
				$string .= '?';
				$first = false;
			} else {
				$string .= '&';
			}
			$string .= $key.'='.$value;
		}
		return $string;
	}

	private function setParams(){
		if(isset($_GET['pagination'])) {
			if(is_numeric($_GET['pagination'])) {
				$this->perPage = $_GET['pagination'];
			}
		}else{
			$this->perPage = 10;
		}
		if(isset($_GET['page'])){
			if(is_numeric($_GET['page'])){
				$this->page = $_GET['page'];
			}
		}else{
			$this->page = 1;
		}
		if(isset($_GET['limit'])){
			if(is_numeric($_GET['limit'])){
				$this->limit = $_GET['limit'];
			}
		}else{
			$this->limit = 0;
		}
		if(isset($_GET['sort'])) {
			$this->sort = $_GET['sort'];
		}else{
			$this->sort = '';
		}
	}

	private function paginate() {
		if($this->perPage > 0) {
			$this->collection = $this->collection->slice((($this->page - 1) * $this->perPage), $this->perPage, true)->all();
			if(empty($this->collection)) {
				$this->collection = null;
			}else{
				$this->createNewCollection();
			}
		}
	}

	private function sort(){
		if($this->sort != '') {
			if(in_array($this->sort, array_keys($this->collection[0]->toArray()))) {
				$this->collection = $this->collection->sortBy($this->sort);
				$this->createNewCollection();
			}
		}
	}

	private function limit(){
		if($this->limit > 0){
			$this->collection = $this->collection->slice($this->limit, $this->limit, true)->all();
			if(empty($this->collection)) {
				$this->collection = null;
			}else{
				$this->createNewCollection();
			}
		}
	}

	private function createNewCollection() {
		$collection = new Collection();
		foreach($this->collection as $item) {
			$collection->add($item);
		}
		$this->collection = $collection;
	}

	private function getCollection(CRepository $repository, $id = null){
		$this->collection = $this->addHidden($repository->get($id));
		if(is_null($id)) {
			$this->limit();
			$this->sort();
			$this->paginate();
		}
		return $this->collection;
	}


	public function index(){
		return View::make('api')->with('title', 'api');
	}

	/**
	 * Shows a category.
	 * @param CategoryRepository $category
	 * @param null $id
	 * @return mixed
	 */
	public function Categories(CategoryRepository $category, $id = null) {
		return Response::json(array('data' => $this->getCollection($category, $id), 'links' => $this->hateoas($id)), 200);
	}

	/**
	 * Shows a tag.
	 * @param TagRepository $tag
	 * @param null $id
	 * @return mixed
	 */
	public function Tags(TagRepository $tag, $id = null){
		return Response::json(array('data' => $this->getCollection($tag, $id), 'links' => $this->hateoas($id)), 200);
	}

	/**
	 * Shows a post.
	 * @param PostRepository $post
	 * @param null $id
	 * @return mixed
	 */
	public function Posts(PostRepository $post, $id = null){
		return Response::json(array('data' => $this->getCollection($post, $id), 'links' => $this->hateoas($id)), 200);
	}

	/**
	 * Shows a user.
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function Users(UserRepository $user, $id = null){
		return Response::json(array('data' => $this->getCollection($user, $id), 'links' => $this->hateoas($id)), 200);
	}

	/**
	 * Shows a forum.
	 * @param ForumRepository $forum
	 * @param null $id
	 * @return mixed
	 */
	public function forums(ForumRepository $forum, $id = null){
		return Response::json(array('data' => $this->getCollection($forum, $id), 'links' => $this->hateoas($id)), 200);
	}

	/**
	 * Shows a topic.
	 * @param TopicRepository $topic
	 * @param null $id
	 * @return mixed
	 */
	public function topics(TopicRepository $topic, $id = null){
		return Response::json(array('data' => $this->getCollection($topic, $id), 'links' => $this->hateoas($id)), 200);
	}

	/**
	 * Creating or updating a category.
	 * @permission create_update_categories
	 * @param CategoryRepository $category
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateCategory(CategoryRepository $category, $id = null){
		if($category->createOrUpdate($this->request->all(), $id)){
			return Response::json(array('message' => 'Your category has been saved', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('errors' => $category->getErrors()), 400);
	}

	/**
	 * Creating or updating a tag.
	 * @permission create_update_tags
	 * @param TagRepository $tag
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateTag(TagRepository $tag, $id = null){
		if($tag->createOrUpdate($this->request->all(), $id)){
			return Response::json(array('message' => 'Your tag has been saved', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('errors' => $tag->getErrors()), 400);
	}

	/**
	 * Creating or updating a post.
	 * @param PostRepository $post
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdatePost(PostRepository $post, $id = null){
		if(!is_null($id)){
			$user_id = $post->get($id)->user_id;
			if($user_id != Auth::user()->id){
				return Response::json(array('errors' => array('user' => 'You have not that created that codeblock')), 400);
			}
		}
		if($post->createOrUpdate($this->request->all(), $id)){
			return Response::json(array('message' => 'Your block has been saved', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('errors' => $post->getErrors()), 400);
	}

	/**
	 * Creating or updating a comment.
	 * @param CommentRepository $comment
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateComment(CommentRepository $comment, $id = null){
		if(!is_null($id)){
			$user_id = $comment->get($id)->user_id;
			if($user_id != Auth::user()->id ||!Auth::user()->hasPermission('edit_comments', false)){
				return Response::json(array('errors' => array('user' => 'You have not that created that comment')), 400);
			}
		}
		if($comment->createOrUpdate($this->request->all(), $id)){
			return Response::json(array('message' => 'Your comment has been saved', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('errors' => $comment->getErrors()), 400);
	}

	/**
	 * Creating or updating a user.
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateUser(UserRepository $user, $id = null){
		if(!is_null($id)){
			if($id != Auth::user()->id || !Auth::user()->hasPermission('update_users', false)){
				return Response::json(array('errors' => array('user' => 'You are not that user')), 400);
			}
		}
		if($user->createOrUpdate($this->request->all(), $id)){
			if(is_null($id)){
				return Response::json(array('message' => 'Your user has been created, use the link in the mail to activate your user.', 'links' => $this->hateoas($id)), 201);
			}else{
				return Response::json(array('message' => 'Your user has been saved.', 'links' => $this->hateoas($id)), 201);
			}
		}
		return Response::json(array('errors' => $user->getErrors()), 400);
	}

	/**
	 * Creating or updating a reply.
	 * @param ReplyRepository $reply
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateReply(ReplyRepository $reply, $id = null){
		if(!is_null($id)){
			$user_id = $reply->get($id)->user_id;
			if($user_id != Auth::user()->id || !Auth::user()->hasPermission('create_reply', false)){
				return Response::json(array('errors' => array('user' => 'You have not that created that reply')), 400);
			}
		}
		if($reply->createOrUpdate($this->request->all(), $id)){
			return Response::json(array('message' => 'Your reply has been saved', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('errors' => $reply->getErrors()), 400);
	}

	/**
	 * Creating or update a topic.
	 * @param TopicRepository $topic
	 * @param ReplyRepository $reply
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateTopic(TopicRepository $topic, ReplyRepository $reply, $id = null){
		if(!is_null($id)){
			$currentTopic = $topic->get($id);
			$replies = $currentTopic->replies;
			$user_id = $replies[0]->user_id;
			if($user_id != Auth::user()->id || !Auth::user()->hasPermission('create_topic', false)){
				return Response::json(array('errors' => array('user' => 'You have not that created that topic')), 400);
			}
		}
		$input = $this->request->all();
		if($topic->createOrUpdate($this->request->all(), $id)){
			$input['topic_id'] = $topic->topic->id;
			if(is_null($id) && !$reply->createOrUpdate($input)) {
				$topic->delete($topic->topic->id);
				return Response::json(array('errors' => $reply->getErrors()), 400);
			}
			return Response::json(array('message' => 'Your topic has been saved', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('errors' => $topic->getErrors()), 400);
	}

	/**
	 * Tar bort en tr�d.
	 * @permission delete_topic:optional
	 * @param $id
	 * @return mixed
	 */
	public function deleteTopic($id) {
		try {
			$topic = $this->topic->get($id);
			if(!is_null($topic)) {
				$reply = $topic->replies()->first();
				if(Auth::user()->hasPermission($this->getPermission(), false) || Auth::user()->id == $reply->user_id) {
					if($this->topic->delete($id)) {
						return Response::json(array('message' => 'Your topic has been deleted.', 'links' => $this->hateoas($id)));
					}
				}
			}

		} catch(\Exception $e){}
		return Response::json(array('errors' => 'That topic could not be deleted.'));
	}

	/**
	 * Ta bort en ettiket
	 * @permission delete_tags
	 * @param  int $id id f�r ettiketen som skall tas bort.
	 * @return object     med v�rden dit anv�ndaren skall skickas.
	 */
	public function deleteTag($id){
		if($this->tag->delete($id)){
			return Response::json(array('message' => 'The tag has been deleted.', 'links' => $this->hateoas($id)));
		}
		return Response::json(array('errors' => 'The tag could not be deleted.'));
	}

	/**
	 * Ta bort ett block.
	 * @permission delete_post:optional
	 * @param  int $id Id f�r blocket som skall tas bort.
	 * @return array     Typ av medelande och meddelande
	 */
	public function deletePost($id)
	{
		$post = $this->post->get($id);
		if(!is_null($post)) {
			if(Auth::check() && Auth::user()->id == $post->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($this->post->delete($id)) {
					return Response::json(array('message' => 'Your codeblock has been deleted.', 'links' => $this->hateoas($id)));
				}
			}else{
				return Response::json(array('errors' => 'You do not have permission to delete that codeblock.'));
			}
		}
		return Response::json(array('errors' => 'We could not delete that codeblock.'));
	}

	/**
	 * Tar bort ett forum.
	 * @permission delete_forums
	 * @param $id
	 * @return mixed
	 */
	public function deleteForum($id) {
		if($this->forum->delete($id)) {
			return Response::json(array('message' => 'Your forum has been deleted.', 'links' => $this->hateoas($id)));
		}
		return Response::json(array('errors' => 'We could not delete that forum.'));
	}

	/**
	 * Ta bort en kategori
	 * @permission delete_categories
	 * @param  int $id id f�r kategori som skall tas bort.
	 * @return object     med v�rden dit anv�ndaren skall skickas.
	 */
	public function deleteCategory($id){
		if($this->category->delete($id)){
			return Response::json(array('message' => 'The category has been deleted.', 'links' => $this->hateoas($id)));
		}
		return Response::json(array('errors' => 'The category could not be deleted.'));
	}

	/**
	 * Tar bort ett svar.
	 * @permission delete_reply:optional
	 * @param $id
	 * @return mixed
	 */
	public function deleteReply($id) {
		if(count($this->reply->get()) > 1) {
			$reply = $this->reply->get($id);
			if(!is_null($reply)) {
				if(Auth::user()->hasPermission($this->getPermission(), false) || Auth::user()->id == $reply->user_id) {
					if($this->reply->delete($id)) {
						return Response::json(array('message' => 'Your reply has been deleted.', 'links' => $this->hateoas($id)));
					}
				}
			}
		}
		return Response::json(array('errors' => 'Your reply could not be deleted.'));
	}

	/**
	 * Ta bort en kommentar
	 * @permission delete_comments:optional
	 * @param  int $id id f�r kommentaren som skall tas bort.
	 * @return object     med v�rden dit anv�ndaren skall skickas.
	 */
	public function deleteComment($id){
		try {
			$comment = $this->comment->get($id);
			if(Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($this->comment->delete($id)) {
					return Response::json(array('message' => 'That comment has now been deleted.', 'links' => $this->hateoas($id)));
				}
			} else {
				return Response::json(array('errors' => 'You do not have permission to delete that comment.'));
			}
		} catch(\Exception $e){}
		return Response::json(array('errors' => 'We could not delete that comment.'));
	}

	/**
	 * Sending a new password to user.
	 * @param UserRepository $user
	 * @return mixed
	 */
	public function forgotPassword(UserRepository $user){
		if($user->forgotPassword($this->request->all())){
			return Response::json(array('message' => 'A new password have been sent to you.'), 200);
		}
		return Response::json(array('message' => "Your email don't exists in our database."), 400);
	}

	/**
	 * Star a post.
	 * @param PostRepository $post
	 * @param $id
	 * @return mixed
	 */
	public function Star(PostRepository $post, $id){
		$star = $post->createOrDeleteStar($id);
		if($star[0]){
			if($star[1] == 'create'){
				return Response::json(array('message', 'You have now add a star to this codblock.', 'links' => $this->hateoas($id)), 201);
			}
			return Response::json(array('message', 'You have now removed a star from this codblock.', 'links' => $this->hateoas($id)), 201);
		}
		return Response::json(array('message', 'Something went wrong, please try again.'), 400);
	}

	/**
	 * Create a rate.
	 * @param RateRepository $rate
	 * @param $id
	 * @return mixed
	 */
	public function Rate(RateRepository $rate, $id){
		if($rate->rate($id, '+')){
			return Response::json(array('message' => 'Your up rated a comment.', 'links' => $this->hateoas($id)), 200);
		}else {
			if($rate->rate($id, '-')) {
				return Response::json(array('message' => 'Your down rated a comment.', 'links' => $this->hateoas($id)), 200);
			}
		}
		return Response::json(array('message', 'You could not rate that comment, please try agian'), 400);
	}

	/**
	 * Authenticate the api user.
	 * @return mixed
	 */
	public function Auth(){
		try{
			Auth::attempt(array('username' => trim(strip_tags($this->request->get('username'))), 'password' => trim(strip_tags($this->request->get('password')))));
		} catch (\Exception $e){}
		return $this->getJwt();
	}
}