<?php namespace App\Repositories\Read;

use App\Read;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;

class EloquentReadRepository extends CRepository implements ReadRepository {

	public function hasRead($topic_id)
	{
		if(Auth::check()) {
			$user_id = Auth::user()->id;
			if(is_null(Read::where('topic_id', $topic_id)->where('user_id', $user_id)->first())) {
				$Read = new Read;
				$Read->topic_id = $topic_id;
				$Read->user_id = $user_id;
				$Read->save();
			}
		}
	}

	public function UpdatedRead($topic_id){
		foreach(Read::where('topic_id', $topic_id)->get() as $Read){
			$Read->delete();
		}
	}

}