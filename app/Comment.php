<?php namespace App;

class Comment extends Model {

	public static function boot() {
	    parent::boot();
	    static::deleting(function($object) {
	    	foreach ($object->children as $child) {
				$child->delete();
			}
	        foreach ($object->rates as $rate) {
				$rate->delete();
			}
	    });
	}

	protected $table = 'comments';

	protected $fillable = array('comment', 'user_id', 'post_id', 'status');

	protected $guarded = array('id');

	public static $rules = array(
		'comment'  => 'required',
		'user_id' => 'integer',
		'post_id' => 'integer',
		'status' => 'integer'
	);

	public function user() {
		return $this->belongsTo( 'App\User', 'user_id', 'id');
	}

	public function post() {
		return $this->belongsTo( 'App\Post', 'post_id', 'id');
	}

	public function children() {
		return $this->hasMany( 'App\Comment', 'parent', 'id');
	}

	public function rates()
	{
		return $this->hasMany('App\Rate', 'comment_id', 'id');
	}
}