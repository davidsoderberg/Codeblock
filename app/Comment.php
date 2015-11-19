<?php namespace App;

/**
 * Class Comment
 * @package App
 */
class Comment extends Model {

	/**
	 * Boot method for model.
	 */
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

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('comment', 'user_id', 'post_id', 'status');

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Post', 'App\User', 'App\Rate'];

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = array(
		'comment'  => 'required',
		'user_id' => 'integer',
		'post_id' => 'integer',
		'status' => 'integer'
	);

	/**
	 * Fetch user this comment belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user() {
		return $this->belongsTo( 'App\User', 'user_id', 'id');
	}

	/**
	 * Fetch post this comment belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function post() {
		return $this->belongsTo( 'App\Post', 'post_id', 'id');
	}

	/**
	 * Fetch children this comment has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function children() {
		return $this->hasMany( 'App\Comment', 'parent', 'id');
	}

	/**
	 * Fetch rates this comment has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function rates()
	{
		return $this->hasMany('App\Rate', 'comment_id', 'id');
	}

	/**
	 * Appends an array of attributes on model.
	 *
	 * @var array
	 */
	protected $appends = array('postlink', 'userlink', 'printstatus');

	/**
	 * Fetch post link.
	 *
	 * @return mixed
	 */
	public function getpostlinkAttribute(){
		return \HTML::actionlink($url = array('action' => 'PostController@show', 'params' => array($this->post_id)), $this->post->name);
	}

	/**
	 * Fetch user link.
	 *
	 * @return mixed
	 */
	public function getuserlinkAttribute(){
		return \HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($this->user_id)), $this->user['username']);
	}

	/**
	 * Fetch print status.
	 *
	 * @return string
	 */
	public function getprintstatusAttribute(){
		if($this->status == 0 ) {
			return 'Hidden';
		} else {
			return 'Shown';
		}
	}
}