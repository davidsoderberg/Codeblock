<?php namespace App;

class Topic extends Model
{
	public static function boot() {
		parent::boot();
		static::deleting(function($object) {
			foreach ($object->replies as $reply) {
				$reply->delete();
			}
			foreach($object->reads as $read){
				$read->delete();
			}
		});
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'topics';

	protected $fillable = array('title', 'forum_id');

	protected $guarded = array('id');

	protected $hidden = array('forum', 'updated_at');

	protected $with = ['replies'];

	public static $rules = array(
		'title'  => 'required|min:3',
		'forum_id' => 'required|integer',
	);

	public function replies()
	{
		return $this->hasMany('App\Reply', 'topic_id', 'id');
	}

	public function forum(){
		return $this->belongsTo( 'App\Forum', 'forum_id' );
	}

	public function reads(){
		return $this->hasMany('App\Read', 'topic_id', 'id');
	}

	public function getforumtitleAttribute(){
		if($this->forum) {
			return $this->forum->title;
		}
		return "";
	}

	protected $appends = array('forumtitle');
}
