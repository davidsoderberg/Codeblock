<?php namespace App;

class Permission extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissions';

	protected $fillable = array('permission');

	protected $guarded = array('id');

	public static $rules = array(
		'permission' => 'required|alpha_dash|unique:permissions,permission',
	);

	protected $modelsToReload = ['App\Role'];

	public function roles()
	{
		return $this->belongsToMany('App\Role');
	}

	public function getNameAttribute(){
		return ucfirst(str_replace('_', ' ', $this->permission));
	}

	protected $appends = array('name');
}