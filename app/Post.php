<?php namespace App;

class Post extends Model {

	public static function boot() {
	    parent::boot();
	    static::deleting(function($object) {
	    	if(!empty($object->posttags[0])){
				$object->posttags()->detach();
			}
			foreach ($object->stars as $star) {
				$star->delete();
			}
			foreach ($object->comments as $comment) {
				$comment->delete();
			}
	    });
	}

	protected $table = 'posts';

	protected $revisionEnabled = true;

	protected $fillable = array('name', 'cat_id', 'description', 'code', 'user_id', 'org', 'slug');

	protected $guarded = array('id');

	public static $rules = array(
	    'name' => 'required|min:3|unique:posts,name,:id:',
	    'cat_id'  => 'required|integer',
	    'description' => 'required|min:3',
	    'code' => 'required|min:3',
	    'user_id' => 'integer',
		'slug' => 'required|min:3|unique:posts,slug,:id:',
	);

	public function category() {
		return $this->belongsTo( 'App\Category', 'cat_id' );
	}

	public function posttags() {
		return $this->belongsToMany('App\Tag','post_tag');
	}

	public function stars()
	{
		return $this->hasMany('App\Star', 'post_id', 'id');
	}

	public function user(){
		return $this->belongsTo( 'App\User', 'user_id' );
	}

	public function comments()
	{
		return $this->hasMany('App\Comment', 'post_id', 'id');
	}

	public function getcategorynameAttribute()
	{
		if(!is_null($this->category)) {
			return $this->category->name;
		}
	}

	protected $appends = array('categoryname');

}