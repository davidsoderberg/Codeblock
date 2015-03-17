<?php namespace App;

class Social extends Model
{
	protected $fillable = array('social', 'social_id', 'user_id');

	protected $guarded = array('id');

	public static $rules = array(
		'social'  => 'required',
		'social_id' => 'required',
		'user_id' => 'required|integer'
	);
}
