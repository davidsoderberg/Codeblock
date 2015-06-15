<?php namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Model extends \Illuminate\Database\Eloquent\Model {
	use \Venturecraft\Revisionable\RevisionableTrait;

	public static $errors;

	protected $revisionEnabled = false;

	public function setRevisionEnabled(){
		$this->revisionEnabled = !$this->revisionEnabled;
	}

	public static function boot()
	{
		parent::boot();
		// körs på alla modell object som sparas.
		static::saving(function($object){
			return $object::isValid($object);
		});
	}

	// From: https://laracasts.com/discuss/channels/general-discussion/how-to-validate-a-slug-unique-in-laravel-5
	public function getSlug($value, $column = 'slug') {
		$slug = Str::slug($value);
		$slugCount = count($this->whereRaw($column." LIKE '^{$slug}(-[0-9]+)?$' and id != '{$this->id}'")->get());
		return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
	}

	// validerings metoden som kör rule variablen från alla modeller när det modell objektet sparas.
	public static function isValid($data)
	{
		$id = null;
		if(is_object($data)){
			$data = $data->toArray();
			if(isset($data['id'])){
				$id = $data['id'];
			}
		}

		$rules = static::$rules;

		if(is_numeric($id)){
			// found on: http://forumsarchive.laravel.io/viewtopic.php?pid=46571
			array_walk($rules, function(&$item) use ($id)
			{
				if(stripos($item, ':id:') !== false){
					$item = str_ireplace(':id:', $id, $item);
				}
			});
		}

		$v = Validator::make($data, $rules);
		if ($v->passes()) {
			return true;
		}else{
			static::$errors = $v->messages();
			return false;
		}
	}
}