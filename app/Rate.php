<?php namespace App;

class Rate extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rates';

	public static $rules = array();

	protected $modelsToReload = ['App\Comment'];

	protected $fillable = array('user_id', 'comment_id', 'type');
}