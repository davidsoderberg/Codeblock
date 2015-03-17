<?php namespace App;

class Permission extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissions';

	protected $fillable = array('name', 'permission');

	protected $guarded = array('id');

	public static $rules = array(
		'name' => 'required|min:3|unique:permissions,name',
		'permission' => 'required|alpha_dash|unique:permissions,permission',
	);

	public function roles()
	{
		return $this->belongsToMany('App\Role');
	}
}