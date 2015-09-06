<?php namespace App\Http\Controllers;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\Forum\ForumRepository;
use App\Repositories\Post\PostRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Reply\ReplyRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Rate\RateRepository;
use App\Repositories\Topic\TopicRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {

	/**
	 * Shows a category.
	 * @param CategoryRepository $category
	 * @param null $id
	 * @return mixed
	 */
	public function Categories(CategoryRepository $category, $id = null) {
		return Response::json(array('data' => $category->get($id)), 200);
	}

	/**
	 * Shows a tag.
	 * @param TagRepository $tag
	 * @param null $id
	 * @return mixed
	 */
	public function Tags(TagRepository $tag, $id = null){
		return Response::json(array('data' => $tag->get($id)), 200);
	}

	/**
	 * Shows a post.
	 * @param PostRepository $post
	 * @param null $id
	 * @return mixed
	 */
	public function Posts(PostRepository $post, $id = null){
		return Response::json(array('data' => $post->get($id)), 200);
	}

	/**
	 * Shows a user.
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function Users(UserRepository $user, $id = null){
		return Response::json(array('data' => $this->addHidden($user->get($id))), 200);
	}

	/**
	 * Shows a forum.
	 * @param ForumRepository $forum
	 * @param null $id
	 * @return mixed
	 */
	public function forums(ForumRepository $forum, $id = null){
		return Response::json(array('data' => $forum->get($id)), 200);
	}

	/**
	 * Shows a topic.
	 * @param TopicRepository $topic
	 * @param null $id
	 * @return mixed
	 */
	public function topics(TopicRepository $topic, $id = null){
		return Response::json(array('data' => $topic->get($id)), 200);
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
			return Response::json(array('message' => 'Your category has been saved'), 201);
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
			return Response::json(array('message' => 'Your tag has been saved'), 201);
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
			return Response::json(array('message' => 'Your block has been saved'), 201);
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
			return Response::json(array('message' => 'Your comment has been saved'), 201);
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
				return Response::json(array('message' => 'Your user has been created, use the link in the mail to activate your user.'), 201);
			}else{
				return Response::json(array('message' => 'Your user has been saved.'), 201);
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
			return Response::json(array('message' => 'Your reply has been saved'), 201);
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
			return Response::json(array('message' => 'Your topic has been saved'), 201);
		}
		return Response::json(array('errors' => $topic->getErrors()), 400);
	}

	/**
	 * Tar bort en tråd.
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
						return Response::json(array('message' => 'Your topic has been deleted.'));
					}
				}
			}

		} catch(\Exception $e){}
		return Response::json(array('errors' => 'That topic could not be deleted.'));
	}

	/**
	 * Ta bort en ettiket
	 * @permission delete_tags
	 * @param  int $id id för ettiketen som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function deleteTag($id){
		if($this->tag->delete($id)){
			return Response::json(array('message' => 'The tag has been deleted.'));
		}
		return Response::json(array('errors' => 'The tag could not be deleted.'));
	}

	/**
	 * Ta bort ett block.
	 * @permission delete_post:optional
	 * @param  int $id Id för blocket som skall tas bort.
	 * @return array     Typ av medelande och meddelande
	 */
	public function deletePost($id)
	{
		$post = $this->post->get($id);
		if(!is_null($post)) {
			if(Auth::check() && Auth::user()->id == $post->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($this->post->delete($id)) {
					return Response::json(array('message' => 'Your codeblock has been deleted.'));
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
			return Response::json(array('message' => 'Your forum has been deleted.'));
		}
		return Response::json(array('errors' => 'We could not delete that forum.'));
	}

	/**
	 * Ta bort en kategori
	 * @permission delete_categories
	 * @param  int $id id för kategori som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function deleteCategory($id){
		if($this->category->delete($id)){
			return Response::json(array('message' => 'The category has been deleted.'));
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
						return Response::json(array('message' => 'Your reply has been deleted.'));
					}
				}
			}
		}
		return Response::json(array('errors' => 'Your reply could not be deleted.'));
	}

	/**
	 * Ta bort en kommentar
	 * @permission delete_comments:optional
	 * @param  int $id id för kommentaren som skall tas bort.
	 * @return object     med värden dit användaren skall skickas.
	 */
	public function deleteComment($id){
		try {
			$comment = $this->comment->get($id);
			if(Auth::check() && Auth::user()->id == $comment->user_id || Auth::user()->hasPermission($this->getPermission(), false)) {
				if($this->comment->delete($id)) {
					return Response::json(array('message' => 'That comment has now been deleted.'));
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
				return Response::json(array('message', 'You have now add a star to this codblock.'), 201);
			}
			return Response::json(array('message', 'You have now removed a star from this codblock.'), 201);
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
			return Response::json(array('message' => 'Your up rated a comment.'), 200);
		}else {
			if($rate->rate($id, '-')) {
				return Response::json(array('message' => 'Your down rated a comment.'), 200);
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