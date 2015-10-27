<?php namespace App\Http\Controllers;

use App\Category;
use App\Model;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\CRepository;
use App\Repositories\Forum\ForumRepository;
use App\Repositories\Post\PostRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Reply\ReplyRepository;
use App\Repositories\Star\StarRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Rate\RateRepository;
use App\Repositories\Topic\TopicRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {

	private $page = 1;

	private $limit = 0;

	private $sort = '';

	private $collection;

	private $stringErrors = 'errors';
	private $stringMessage = 'messsage';
	private $stringData = 'data';
	private $stringUser = 'user';

	public function __construct(){
		parent::__construct();
		Model::$append = true;
		$this->setParams();
	}

	private function response($response, $code){
		$response = Response::json($response, $code);
		if(str_contains($this->request->headers->get('Accept'), '/xml') &&  !str_contains($this->request->headers->get('Accept'), 'xhtml')){
			$response = $response->getData(true);
			return Response::xml($response, $code);
		}
		return $response;
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
		if($this->collection instanceof Collection) {
			/*
			$collection = new Collection();
			foreach($this->collection as $item) {
				$collection->add($item->toArray());
			}
			*/

			$this->collection->values();
		}
	}

	private function getCollection(CRepository $repository, $id = null){
		$this->collection = $this->addHidden($repository->get($id));
		if(is_null($id)) {
			$this->limit();
			$this->sort();
			$this->paginate();
		}
		$this->createNewCollection();
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
		return $this->response(array($this->stringData => $this->getCollection($category, $id)), 200);
	}

	/**
	 * Shows a tag.
	 * @param TagRepository $tag
	 * @param null $id
	 * @return mixed
	 */
	public function Tags(TagRepository $tag, $id = null){
		return $this->response(array($this->stringData => $this->getCollection($tag, $id)), 200);
	}

	/**
	 * Shows a post.
	 * @param PostRepository $post
	 * @param null $id
	 * @return mixed
	 */
	public function Posts(PostRepository $post, $id = null){
		return $this->response(array($this->stringData => $this->getCollection($post, $id)), 200);
	}

	/**
	 * Shows a user.
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function Users(UserRepository $user, $id = null){
		return $this->response(array($this->stringData => $this->getCollection($user, $id)), 200);
	}

	/**
	 * Shows a forum.
	 * @param ForumRepository $forum
	 * @param null $id
	 * @return mixed
	 */
	public function forums(ForumRepository $forum, $id = null){
		return $this->response(array($this->stringData => $this->getCollection($forum, $id)), 200);
	}

	/**
	 * Shows a topic.
	 * @param TopicRepository $topic
	 * @param null $id
	 * @return mixed
	 */
	public function topics(TopicRepository $topic, $id = null){
		return $this->response(array($this->stringData => $this->getCollection($topic, $id)), 200);
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
			return $this->response(array($this->stringMessage => 'Your category has been saved'), 201);
		}
		return $this->response(array($this->stringErrors => $category->getErrors()), 400);
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
			return $this->response(array($this->stringMessage => 'Your tag has been saved'), 201);
		}
		return $this->response(array($this->stringErrors => $tag->getErrors()), 400);
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
				return $this->response(array($this->stringErrors => array($this->stringUser => 'You have not that created that codeblock')), 400);
			}
		}
		$inputs = $this->request->all();
		if(isset($inputs['category'])) {
			$inputs['cat_id'] = $inputs['category'];
			unset($inputs['category']);
		}
		if($post->createOrUpdate($inputs, $id)){
			return $this->response(array($this->stringMessage => 'Your block has been saved'), 201);
		}
		return $this->response(array($this->stringErrors => $post->getErrors()), 400);
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
			if($user_id != Auth::user()->id && !Auth::user()->hasPermission('edit_comments', false)){
				return $this->response(array($this->stringErrors => array($this->stringUser => 'You have not created that comment')), 400);
			}
		}
		if($comment->createOrUpdate($this->request->all(), $id)){
			return $this->response(array($this->stringMessage => 'Your comment has been saved'), 201);
		}
		return $this->response(array($this->stringErrors => $comment->getErrors()), 400);
	}

	/**
	 * Creating or updating a user.
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateUser(UserRepository $user, $id = null){
		if(!is_null($id)){
			if($id != Auth::user()->id && !Auth::user()->hasPermission('update_users', false)){
				return $this->response(array($this->stringErrors => array($this->stringUser => 'You are not that user')), 400);
			}
		}
		if($user->createOrUpdate($this->request->all(), $id)){
			if(is_null($id)){
				return $this->response(array($this->stringMessage => 'Your user has been created, use the link in the mail to activate your user.'), 201);
			}else{
				return $this->response(array($this->stringMessage => 'Your user has been saved.'), 201);
			}
		}
		return $this->response(array($this->stringErrors => $user->getErrors()), 400);
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
			if($user_id != Auth::user()->id && !Auth::user()->hasPermission('create_reply', false)){
				return $this->response(array($this->stringErrors => array($this->stringUser => 'You have not that created that reply')), 400);
			}
		}
		if($reply->createOrUpdate($this->request->all(), $id)){
			return $this->response(array($this->stringMessage => 'Your reply has been saved'), 201);
		}
		return $this->response(array($this->stringErrors => $reply->getErrors()), 400);
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
			if($user_id != Auth::user()->id && !Auth::user()->hasPermission('create_topic', false)){
				return $this->response(array($this->stringErrors => array($this->stringUser => 'You have not that created that topic')), 400);
			}
		}
		$input = $this->request->all();
		if($topic->createOrUpdate($this->request->all(), $id)){
			$input['topic_id'] = $topic->topic->id;
			if(is_null($id) && !$reply->createOrUpdate($input)) {
				$topic->delete($topic->topic->id);
				return $this->response(array($this->stringErrors => $reply->getErrors()), 400);
			}
			return $this->response(array($this->stringMessage => 'Your topic has been saved'), 201);
		}
		return $this->response(array($this->stringErrors => $topic->getErrors()), 400);
	}

	/**
	 * Tar bort en tråd.
	 * @permission delete_topic:optional
	 * @param $id
	 * @return mixed
	 */
	public function deleteTopic(TopicRepository $topicRepository, $id) {
		try {
			$topic = $topicRepository->get($id);
			if(!is_null($topic)) {
				$reply = $topic->replies()->first();
				if(Auth::user()->hasPermission($this->getPermission(), false) || Auth::user()->id == $reply->user_id) {
					if($topicRepository->delete($id)) {
						return $this->response(array($this->stringMessage => 'Your topic has been deleted.'), 200);
					}
				}
			}

		} catch(\Exception $e){}
		return $this->response(array($this->stringErrors => 'That topic could not be deleted.'), 204);
	}

	/**
	 * Ta bort en ettiket
	 * @permission delete_tags
	 * @param  int $id id för ettiketen som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function deleteTag(TagRepository $tagRepository, $id){
		if($tagRepository->delete($id)){
			return $this->response(array($this->stringMessage => 'The tag has been deleted.'), 200);
		}
		return $this->response(array($this->stringErrors => 'The tag could not be deleted.'), 204);
	}

	/**
	 * Ta bort ett block.
	 * @permission delete_post:optional
	 * @param  int $id Id för blocket som skall tas bort.
	 * @return array     Typ av medelande och meddelande
	 */
	public function deletePost(PostRepository $postRepository, $id)
	{
		$post = $postRepository->get($id);
		if(!is_null($post)) {
			if(Auth::check() && Auth::user()->id == $post->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($postRepository->delete($id)) {
					return $this->response(array($this->stringMessage => 'Your codeblock has been deleted.'), 200);
				}
			}else{
				return $this->response(array($this->stringErrors => 'You do not have permission to delete that codeblock.'), 204);
			}
		}
		return $this->response(array($this->stringErrors => 'We could not delete that codeblock.'), 204);
	}

	/**
	 * Tar bort ett forum.
	 * @permission delete_forums
	 * @param $id
	 * @return mixed
	 */
	public function deleteForum(ForumRepository $forumRepository, $id) {
		if($forumRepository->delete($id)) {
			return $this->response(array($this->stringMessage => 'Your forum has been deleted.'), 200);
		}
		return $this->response(array($this->stringErrors => 'We could not delete that forum.'), 204);
	}

	/**
	 * Ta bort en kategori
	 * @permission delete_categories
	 * @param  int $id id för kategori som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function deleteCategory(CategoryRepository $categoryRepository, $id){
		if($categoryRepository->delete($id)){
			return $this->response(array($this->stringMessage => 'The category has been deleted.'), 200);
		}
		return $this->response(array($this->stringErrors => 'The category could not be deleted.'), 204);
	}

	/**
	 * Tar bort ett svar.
	 * @permission delete_reply:optional
	 * @param $id
	 * @return mixed
	 */
	public function deleteReply(ReplyRepository $replyRepository, $id) {
		if(count($replyRepository->get()) > 1) {
			$reply = $replyRepository->get($id);
			if(!is_null($reply)) {
				if(Auth::user()->hasPermission($this->getPermission(), false) || Auth::user()->id == $reply->user_id) {
					if($replyRepository->delete($id)) {
						return $this->response(array($this->stringMessage => 'Your reply has been deleted.'), 200);
					}
				}
			}
		}
		return $this->response(array($this->stringErrors => 'Your reply could not be deleted.'), 204);
	}

	/**
	 * Ta bort en kommentar
	 * @permission delete_comments:optional
	 * @param  int $id id för kommentaren som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function deleteComment(CommentRepository $commentRepository, $id){
		try {
			$comment = $commentRepository->get($id);
			if(Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($commentRepository->delete($id)) {
					return $this->response(array($this->stringMessage => 'That comment has now been deleted.'), 200);
				}
			} else {
				return $this->response(array($this->stringErrors => 'You do not have permission to delete that comment.'), 204);
			}
		} catch(\Exception $e){}
		return $this->response(array($this->stringErrors => 'We could not delete that comment.'), 204);
	}

	/**
	 * Sending a new password to user.
	 * @param UserRepository $user
	 * @return mixed
	 */
	public function forgotPassword(UserRepository $user){
		if($user->forgotPassword($this->request->all())){
			return $this->response(array($this->stringMessage => 'A new password have been sent to you.'), 200);
		}
		return $this->response(array($this->stringMessage => "Your email don't exists in our database."), 400);
	}

	/**
	 * Star a post.
	 * @param PostRepository $post
	 * @param $id
	 * @return mixed
	 */
	public function Star(PostRepository $post, StarRepository $starRepository, $id){
		$star = $post->createOrDeleteStar($starRepository, $id);
		if($star[0]){
			if($star[1] == 'create'){
				return $this->response(array($this->stringMessage, 'You have now add a star to this codblock.'), 201);
			}
			return $this->response(array($this->stringMessage, 'You have now removed a star from this codblock.'), 201);
		}
		return $this->response(array($this->stringMessage, 'Something went wrong, please try again.'), 400);
	}

	/**
	 * Create a rate.
	 * @param RateRepository $rate
	 * @param $id
	 * @return mixed
	 */
	public function Rate(RateRepository $rate, $id){
		if($rate->rate($id, '+')){
			return $this->response(array($this->stringMessage => 'Your up rated a comment.'), 200);
		}else {
			if($rate->rate($id, '-')) {
				return $this->response(array($this->stringMessage => 'Your down rated a comment.'), 200);
			}
		}
		return $this->response(array($this->stringMessage, 'You could not rate that comment, please try agian'), 400);
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