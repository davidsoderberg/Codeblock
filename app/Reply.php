<?php namespace App;

class Reply extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'replies';

	protected $fillable = array('reply', 'topic_id', 'user_id');

	protected $guarded = array('id');

	protected $modelsToReload = ['App\Topic', 'App\User', 'App\Forum'];

	protected $hidden = array('user', 'updated_at');

	public static $rules = array(
		'reply'  => 'required|min:3',
		'topic_id' => 'required|integer',
		'user_id' => 'required|integer',
	);

	public function user(){
		return $this->belongsTo( 'App\User', 'user_id' );
	}

	public function topic(){
		return $this->belongsTo( 'App\Topic', 'topic_id' );
	}

	public function getusernameAttribute(){
		return $this->user->username;
	}

	public function getlinksAttribute(){
		return $this->hateoas($this->id, 'replies');
	}

	protected $appends = array('username');
}
