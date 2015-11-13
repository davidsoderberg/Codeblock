<?php namespace App;

use Illuminate\Support\Facades\Auth;

class Forum extends Model
{
	public static function boot() {
		parent::boot();
		static::deleting(function($object) {
			foreach ($object->topics as $topic) {
				$topic->delete();
			}
		});
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forums';

	protected $fillable = array('title', 'description');

	protected $guarded = array('id');

	public static $rules = array(
		'title'  => 'required|min:3',
		'description' => 'required|min:3',
	);

	protected $modelsToReload = ['App\Topic', 'App\Reply'];

	public function topics()
	{
		return $this->hasMany('App\Topic', 'forum_id', 'id');
	}

	public function replies(){
		$topics = $this->topics;
		$replies = 0;
		foreach($topics as $topic){
			$replies += count($topic->replies);
		}
		return $replies;
	}

	public function lastReply(){
		$topics = $this->topics;
		$latestReply = null;
		foreach($topics as $topic){
			$reply = $topic->replies->last();
			if(!is_null($reply)) {
				if(is_null($latestReply) || strtotime($latestReply->created_at) < strtotime($reply->created_at)) {
					$latestReply = $reply;
				}
			}
		}
		return $latestReply;
	}

	public function hasUnreadTopics(){
		if(Auth::check()){
			$hasread = array();
			foreach($this->topics as $topic){
				if(count($topic->replies) > 0) {
					$hasread[] = Auth::user()->hasRead($topic->id);
				}
			}
			return in_array(false, $hasread);
		}
		return true;
	}

	public function getlinksAttribute(){
		return $this->hateoas($this->id, 'forums');
	}
}
