<?php namespace App;

use App\Repositories\CRepository;
use App\Services\HateoasTrait;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Model extends \Illuminate\Database\Eloquent\Model {
	use RevisionableTrait;
	use HateoasTrait;

	public function __construct(array $attributes = []){
		parent::__construct($attributes);
		if(Self::$append){
			$this->addLinks();
		}
	}

	public static $append = false;

	public static $errors;

	protected $addHidden = array();

	protected $hidden = array("updated_at");

	protected $revisionEnabled = false;

	public function setRevisionEnabled(){
		$this->revisionEnabled = !$this->revisionEnabled;
	}

	public function addToHidden(){
		$this->addHidden($this->addHidden);
	}

	public function addLinks(){
		$this->appends[] = 'links';
	}

	public function getlinksAttribute(){
		return [];
	}

	protected $modelsToReload = [];

	public function getModelsToReload(){
		return $this->modelsToReload;
	}

	public function getAnswer($boolean){
		if(
			$boolean === true || $boolean === false ||
			$boolean === 0 || $boolean === 1 ||
			$boolean === "1" || $boolean === "0"
		) {
			if($boolean == 1 || $boolean == true) {
				return 'Yes';
			}
			return 'No';
		}
		return $boolean;
	}

	public static function boot()
	{
		parent::boot();
		// körs på alla modell object som sparas.
		static::saving(function($object){
			return $object::isValid($object);
		});

		static::saved(function($object){
			Self::reloadModels($object);
			return true;
		});

		static::deleted(function($object){
			Self::reloadModels($object);
			return true;
		});
	}

	protected static function reloadModels(\App\Model $object){
		$models = $object->getModelsToReload();
		$models[] = get_class($object);
		$models = array_unique($models);
		foreach($models as $model){
			if(!str_contains($model, 'App\\')){
				$model = 'App\\'+$model;
			}
			CRepository::flush(new $model());
		}
	}

	// From: https://laracasts.com/discuss/channels/general-discussion/how-to-validate-a-slug-unique-in-laravel-5
	public function getSlug($value, $column = 'slug') {
		$slug = Str::slug($value);
		$slugCount = count($this->whereRaw($column." LIKE '^{$slug}(-[0-9]+)?$' and id != '{$this->id}'")->get());
		return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
	}

	// validerings metoden som kör rule variablen från alla modeller när det modell objektet sparas.
	public static function isValid($data, $rules = array())
	{
		$id = null;
		if(is_object($data)){
			$data = $data->toArray();
			if(isset($data['id'])){
				$id = $data['id'];
			}
		}

		if(count($rules) == 0) {
			$rules = static::$rules;
		}

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