<?php namespace App\Http\Controllers;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\Post\PostRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Rate\RateRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller {

	public function Categories(CategoryRepository $category) {
		return Response::json(array('data' => $category->get()), 200);
	}

	public function Tags(TagRepository $tag){
		return Response::json(array('data' => $tag->get()), 200);
	}

	public function Posts(PostRepository $post){
		return Response::json(array('data' => $post->get()), 200);
	}

	public function Users(UserRepository $user){
		return Response::json(array('data' => $user->get()), 200);
	}

	public function CreateCategory(CategoryRepository $category){
		if($category->createOrUpdate(Input::all())){
			return Response::json(array('message' => 'Your category has been saved'), 201);
		}
		return Response::json(array('errors' => $category->getErrors()), 400);
	}

	public function CreateTag(TagRepository $tag){
		if($tag->createOrUpdate(Input::all())){
			return Response::json(array('message' => 'Your tag has been saved'), 201);
		}
		return Response::json(array('errors' => $tag->getErrors()), 400);
	}

	public function CreatePost(PostRepository $post){
		if($post->createOrUpdate(Input::all())){
			return Response::json(array('message' => 'Your block has been saved'), 201);
		}
		return Response::json(array('errors' => $post->getErrors()), 400);
	}

	public function CreateComment(CommentRepository $comment){
		if($comment->createOrUpdate(Input::all())){
			return Response::json(array('message' => 'Your comment has been saved'), 201);
		}
		return Response::json(array('errors' => $comment->getErrors()), 400);
	}

	public function Star($id, PostRepository $post){
		$star = $post->createOrDeleteStar($id);
		if($star[0]){
			if($star[1] == 'create'){
				return Response::json(array('success', 'You have now add a star to this codblock.'), 201);
			}
			return Response::json(array('success', 'You have now removed a star from this codblock.'), 201);
		}
		return Response::json(array('error', 'Something went wrong, please try again.'), 400);
	}

	public function Rate($id, RateRepository $rate){
		if($rate->rate($id, '+')){
			return Response::json(array('message' => 'Your up rated a comment.'), 200);
		}else {
			if($rate->rate($id, '-')) {
				return Response::json(array('message' => 'Your down rated a comment.'), 200);
			}
		}
		return Response::json(array('error', 'You could not rate that comment, please try agian'), 400);
	}

	public function Auth(){
		if (Auth::attempt(array('username' => trim(strip_tags(Input::get('username'))), 'password' => trim(strip_tags(Input::get('password')))))) {
			return Response::json(array('token' => \JWT::encode(array('id' => Auth::user()->id), env('APP_KEY'))), 200);
		}
		return Response::json(array('error', 'You could not rate that comment, please try agian'), 400);
	}
}