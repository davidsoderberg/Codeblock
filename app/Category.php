<?php namespace App;

/**
 * Class Category
 * @package App
 */
class Category extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'categories';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = array('name');

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
	    'name' => 'required|min:3|unique:categories,name,:id:',
	);

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Post'];

	/**
	 * Fetch posts this category has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function posts() {
		return $this->hasMany( 'App\Post' );
	}

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute(){
		return $this->hateoas($this->id, 'categories');
	}

}