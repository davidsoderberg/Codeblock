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

	protected $appends = array('postlink', 'userlink', 'printstatus');

	public function getpostlinkAttribute(){
		return \HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($this->post_id)), $this->post->name);
	}

	public function getuserlinkAttribute(){
		return \HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($this->user_id)), $this->user['username']);
	}

	public function getprintstatusAttribute(){
		if($this->status == 0 ) {
			return 'Hidden';
		} else {
			return 'Shown';
		}
	}
}