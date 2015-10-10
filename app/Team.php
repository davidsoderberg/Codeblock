<?php namespace App;

use App\ModelTraits\TeamTrait;

class Team extends Model {

	use TeamTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'teams';

	protected $fillable = array('name', 'owner_id');

	protected $guarded = array('id');

	public static $rules = array(
		'name'  => 'required|name|unique:teams,name,:id:',
		'owner_id' => 'integer'
	);

}