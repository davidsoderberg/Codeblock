<?php namespace App;

class Notification extends Model {

	protected $table = 'notifications';

	protected $fillable = array(
		'user_id',
		'type',
		'subject',
		'body',
		'object_id',
		'object_type',
		'sent_at',
		'from_id'
	);

	protected $guarded = array('id');

	public static $rules = array(
		'user_id' => 'required|integer',
		'type'  => 'required|min:3',
		'subject' => 'required|min:3',
		'body' => 'required|min:3',
		'object_id' => 'integer',
		'object_type' => 'min:3',
		'sent_at' => 'required|date',
		'from_id' => 'required|integer',
	);

}