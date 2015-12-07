<?php namespace App\Models;

/**
 * Class Social
 * @package App\Models
 */
class Social extends Model
{
	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('social', 'social_id', 'user_id');

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
		'social'  => 'required',
		'social_id' => 'required',
		'user_id' => 'required|integer'
	);

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Models\User'];
}
