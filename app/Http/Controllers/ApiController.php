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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {

	/**
	 * @param CategoryRepository $category
	 * @param null $id
	 * @return mixed
	 */
	public function Categories(CategoryRepository $category, $id = null) {
		return Response::json(array('data' => $category->get($id)), 200);
	}

	/**
	 * @param TagRepository $tag
	 * @param null $id
	 * @return mixed
	 */
	public function Tags(TagRepository $tag, $id = null){
		return Response::json(array('data' => $tag->get($id)), 200);
	}

	/**
	 * @param PostRepository $post
	 * @param null $id
	 * @return mixed
	 */
	public function Posts(PostRepository $post, $id = null){
		return Response::json(array('data' => $post->get($id)), 200);
	}

	/**
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function Users(UserRepository $user, $id = null){
		return Response::json(array('data' => $user->get($id)), 200);
	}

	/**
	 * @param ForumRepository $forum
	 * @param null $id
	 * @return mixed
	 */
	public function forums(ForumRepository $forum, $id = null){
		return Response::json(array('data' => $forum->get($id)), 200);
	}

	/**
	 * @param TopicRepository $topic
	 * @param null $id
	 * @return mixed
	 */
	public function topics(TopicRepository $topic, $id = null){
		return Response::json(array('data' => $topic->get($id)), 200);
	}

	/**
	 * @permission create_update_categories
	 * @param CategoryRepository $category
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateCategory(CategoryRepository $category, $id = null){
		if($category->createOrUpdate(Input::all(), $id)){
			return Response::json(array('message' => 'Your category has been saved'), 201);
		}
		return Response::json(array('errors' => $category->getErrors()), 400);
	}

	/**
	 * @permission create_update_tags
	 * @param TagRepository $tag
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateTag(TagRepository $tag, $id = null){
		if($tag->createOrUpdate(Input::all(), $id)){
			return Response::json(array('message' => 'Your tag has been saved'), 201);
		}
		return Response::json(array('errors' => $tag->getErrors()), 400);
	}

	/**
	 * @param PostRepository $post
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdatePost(PostRepository $post, $id = null){
		if($post->createOrUpdate(Input::all(), $id)){
			return Response::json(array('message' => 'Your block has been saved'), 201);
		}
		return Response::json(array('errors' => $post->getErrors()), 400);
	}

	/**
	 * @param CommentRepository $comment
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateComment(CommentRepository $comment, $id = null){
		if($comment->createOrUpdate(Input::all(), $id)){
			return Response::json(array('message' => 'Your comment has been saved'), 201);
		}
		return Response::json(array('errors' => $comment->getErrors()), 400);
	}

	/**
	 * @param UserRepository $user
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateUser(UserRepository $user, $id = null){
		if($user->createOrUpdate(Input::all(), $id)){
			if(is_null($id)){
				return Response::json(array('message' => 'Your user has been created, use the link in the mail to activate your user.'), 201);
			}else{
				return Response::json(array('message' => 'Your user has been saved.'), 201);
			}
		}
		return Response::json(array('errors' => $user->getErrors()), 400);
	}

	/**
	 * @param ReplyRepository $reply
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateReply(ReplyRepository $reply, $id = null){
		if($reply->createOrUpdate(Input::all(), $id)){
			return Response::json(array('message' => 'Your reply has been saved'), 201);
		}
		return Response::json(array('errors' => $reply->getErrors()), 400);
	}

	/**
	 * @param TopicRepository $topic
	 * @param ReplyRepository $reply
	 * @param null $id
	 * @return mixed
	 */
	public function createOrUpdateTopic(TopicRepository $topic, ReplyRepository $reply, $id = null){
		$input = Input::all();
		if($topic->createOrUpdate(Input::all(), $id)){
			$input['topic_id'] = $topic->topic->id;
			if(!$this->reply->createOrUpdate($input)) {
				$topic->delete($topic->topic->id);
				return Response::json(array('errors' => $reply->getErrors()), 400);
			}
			return Response::json(array('message' => 'Your topic has been saved'), 201);
		}
		return Response::json(array('errors' => $topic->getErrors()), 400);
	}

	/**
	 * @param UserRepository $user
	 * @return mixed
	 */
	public function forgotPassword(UserRepository $user){
		if($user->forgotPassword(Input::all())){
			return Response::json(array('message' => 'A new password have been sent to you.'), 200);
		}
		return Response::json(array('message' => "Your email don't exists in our database."), 400);
	}

	/**
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
	 * @return mixed
	 */
	public function Auth(){
		if (Auth::attempt(array('username' => trim(strip_tags(Input::get('username'))), 'password' => trim(strip_tags(Input::get('password')))))) {
			return Response::json(array('token' => \JWT::encode(array('id' => Auth::user()->id), env('APP_KEY'))), 200);
		}
		return Response::json(array('message', 'You could not rate that comment, please try agian'), 400);
	}
}