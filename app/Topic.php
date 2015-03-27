<?php namespace App;

class Topic extends Model
{
	public static function boot() {
		parent::boot();
		static::deleting(function($object) {
			foreach ($object->replies as $reply) {
				$reply->delete();
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
}
