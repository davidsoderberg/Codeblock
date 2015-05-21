<?php namespace App;

class Role extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	protected $fillable = array('name', 'default');

	protected $guarded = array('id');

	public static $rules = array(
		'name' => 'required|min:3|unique:roles,name,:id:'
	);

	public function permissions()
	{
		return $this->belongsToMany('App\Permission');
	}
}