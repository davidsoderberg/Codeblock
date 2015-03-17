<?php namespace App;

class Category extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'categories';

	protected $fillable = array('name');

	protected $guarded = array('id');

	public static $rules = array(
	    'name' => 'required|min:3|unique:categories,name,:id:',
	);

	public function posts() {
		return $this->hasMany( 'App\Post' );
	}

}