<?php namespace App;

class Role extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	protected $fillable = array('name', 'grade');

	protected $guarded = array('id');

	public static $rules = array(
		'name' => 'required|min:3|unique:roles,name',
		'grade' => 'integer|unique:roles,grade',
	);

	public function permissions()
	{
		return $this->belongsToMany('App\Permission');
	}
}