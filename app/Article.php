<?php namespace App;

class Article extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'articles';

	protected $fillable = array('title', 'body', 'slug');

	protected $guarded = array('id');

	public static $rules = array(
	    'title'  => 'required|unique:articles,title,:id:',
	    'body' => 'required|min:3',
		'slug' => 'required|min:3|unique:articles,slug,:id:',
	);


}