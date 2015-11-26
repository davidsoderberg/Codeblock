<?php namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * Class Forum
 * @package App\Models
 */
class Forum extends Model
{
	/**
	 * Boot method for forum model.
	 */
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

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('title', 'description');

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	public static $rules = array(
		'title'  => 'required|min:3',
		'description' => 'required|min:3',
	);

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Models\Topic', 'App\Models\Reply'];

	/**
	 * Array with models that should be eagerloaded.
	 *
	 * @var array
	 */
	protected $with = ['topics'];

	/**
	 * Fetch topics this forum has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function topics()
	{
		return $this->hasMany('App\Models\Topic', 'forum_id', 'id');
	}

	/**
	 * Count replies for this forum.
	 *
	 * @return int
	 */
	public function replies(){
		$topics = $this->topics;
		$replies = 0;
		foreach($topics as $topic){
			$replies += count($topic->replies);
		}
		return $replies;
	}

	/**
	 * Fetch the last reply for this forum.
	 *
	 * @return null
	 */
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

	/**
	 * Checks if this forum has unread topic.
	 *
	 * @return bool
	 */
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

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute(){
		return $this->hateoas($this->id, 'forums');
	}
}
