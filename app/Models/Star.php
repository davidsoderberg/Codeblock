<?php namespace App\Models;

/**
 * Class Star
 * @package App\Models
 */
class Star extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'stars';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = ['post_id', 'user_id'];

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = [
		'post_id' => 'required|integer',
		'user_id' => 'required|integer',
	];

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Models\Post', 'App\Models\User'];

	/**
	 * Fetch post this star belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function post()
	{
		return $this->belongsTo('App\Models\Post', 'post_id');
	}

}