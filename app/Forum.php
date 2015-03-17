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
}
