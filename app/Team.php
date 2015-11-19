<?php namespace App;

use App\ModelTraits\TeamTrait;

/**
 * Class Team
 * @package App
 */
class Team extends Model {

	use TeamTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'teams';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('name', 'owner_id');

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
		'name'  => 'required|min:3|unique:teams,name,:id:',
		'owner_id' => 'integer'
	);

}