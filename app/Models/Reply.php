<?php namespace App\Models;

/**
 * Class Reply
 * @package App\Models
 */
class Reply extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'replies';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('reply', 'topic_id', 'user_id');

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
	protected $modelsToReload = ['App\Models\Topic', 'App\Models\User', 'App\Models\Forum'];

	/**
	 * Array with hidden fields for user.
	 *
	 * @var array
	 */
	protected $hidden = array('user', 'updated_at');

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = array(
		'reply'  => 'required|min:3',
		'topic_id' => 'required|integer',
		'user_id' => 'required|integer',
	);

	/**
	 * Fetch user this reply belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user(){
		return $this->belongsTo( 'App\Models\User', 'user_id' );
	}

	/**
	 * Fetch topic this reply belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function topic(){
		return $this->belongsTo( 'App\Models\Topic', 'topic_id' );
	}

	/**
	 * Fetch username for user this reply belongs to.
	 *
	 * @return mixed
	 */
	public function getusernameAttribute(){
		return $this->user->username;
	}

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute(){
		return $this->hateoas($this->id, 'replies');
	}

	/**
	 * Appends an array of attributes on model.
	 *
	 * @var array
	 */
	protected $appends = array('username');
}
