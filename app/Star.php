<?php namespace App;

class Star extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'stars';

	protected $fillable = array('post_id', 'user_id');

	protected $guarded = array('id');

	public static $rules = array(
		'post_id'  => 'required|integer',
		'user_id'  => 'required|integer'
	);

	protected $modelsToReload = ['App\Post', 'App\User'];

	public function post() {
		return $this->belongsTo( 'App\Post', 'post_id' );
	}

}