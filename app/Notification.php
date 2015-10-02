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
		'from_id' => 'required|integer',
	);

	protected $modelsToReload = ['App\User'];

	public function sender() {
		return $this->hasOne('App\User', 'id', 'from_id');
	}

	public function receiver(){
		return $this->hasOne('App\User', 'id', 'user_id');
	}

	public function object() {
		return $this->hasOne('App\\'.$this->object_type, 'id', 'object_id');
	}

}