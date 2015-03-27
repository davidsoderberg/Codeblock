<?php namespace App;

class read extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reads';

	protected $fillable = array('user_id', 'topic_id');

	protected $guarded = array('id');

	public static $rules = array(
		'user_id' => 'required|integer',
		'topic_id' => 'required|integer',
	);

}