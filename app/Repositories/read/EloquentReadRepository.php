<?php namespace App\Repositories\Read;

use App\Read;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

class EloquentReadRepository extends CRepository implements ReadRepository {

	private function get(){
		return $this->cache('all', Read::where('id', '!=', 0));
	}

	public function hasRead($topic_id)
	{
		if(Auth::check()) {
			$user_id = Auth::user()->id;
			$is_null = CollectionService::filter($this->get(), 'topic_id', $topic_id);
			$is_null = CollectionService::filter($is_null, 'user_id', $user_id, 'first');
			if(is_null($is_null)) {
				$Read = new Read;
				$Read->topic_id = $topic_id;
				$Read->user_id = $user_id;
				$Read->save();
			}
		}
	}

	public function UpdatedRead($topic_id){
		foreach(CollectionService::filter($this->get(), 'topic_id', $topic_id) as $Read){
			$Read->delete();
		}
	}

}