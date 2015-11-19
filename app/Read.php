<?php namespace App;

/**
 * Class read
 * @package App
 */
class read extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reads';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('user_id', 'topic_id');

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = array(
		'user_id' => 'required|integer',
		'topic_id' => 'required|integer',
	);

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\User', 'App\Topic'];

}