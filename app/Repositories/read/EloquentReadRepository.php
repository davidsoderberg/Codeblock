<?php namespace App\Repositories\Read;

use App\Read;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;

class EloquentReadRepository extends CRepository implements ReadRepository {

	public function hasRead($topic_id)
	{
		if(Auth::check()) {
			$Read = new Read;
			$Read->topic_id = $topic_id;
			$Read->user_id = Auth::user()->id;
			$Read->save();
		}
	}

	public function UpdatedRead($topic_id){
		$Reads = Read::where('topic_id', $topic_id);
		foreach($Reads as $Read){
			$Read->delete();
		}
	}

}