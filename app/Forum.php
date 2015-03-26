<?php namespace App;

class Forum extends Model
{
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
			if(is_null($latestReply) || strtotime($latestReply->created_at) < strtotime($reply->created_at) ){
				$latestReply = $reply;
			}
		}
		return $latestReply;
	}
}
