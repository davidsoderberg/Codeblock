<?php namespace App\Models;

use App\Services\HateoasTrait;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\CacheTrait;

/**
 * Class Model
 * @package App\Models
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
	use RevisionableTrait;
	use HateoasTrait;
	use CacheTrait;

	/**
	 * Property to store $this in.
	 *
	 * @var
	 */
	public static $self;

	/**
	 * Constructor for model.
	 *
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		\Illuminate\Database\Eloquent\Model::__construct($attributes);

		Self::$self = $this;

		if (Self::$append) {
			$this->addLinks();
		}
	}

	/**
	 * Property to store if attributes should be appended.
	 *
	 * @var bool
	 */
	public static $append = false;

	/**
	 * Property to store errors in.
	 *
	 * @var
	 */
	public static $errors;

	/**
	 * Array with fields to add to hidden array.
	 *
	 * @var array
	 */
	protected $addHidden = [];

	/**
	 * Array with hidden fields for model.
	 *
	 * @var array
	 */
	protected $hidden = ["updated_at"];

	/**
	 * Enables revision for this model.
	 *
	 * @var bool
	 */
	protected $revisionEnabled = false;

	/**
	 * Setter for revisionEnabled.
	 */
	public function setRevisionEnabled()
	{
		$this->revisionEnabled = !$this->revisionEnabled;
	}

	/**
	 * Add more fields to hidden array.
	 */
	public function addToHidden()
	{
		$this->addHidden($this->addHidden);
	}

	/**
	 * Appends links to model.
	 */
	public function addLinks()
	{
		$this->appends[] = 'links';
	}

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute()
	{
		return [];
	}

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = [];

	/**
	 * Fetch models to reload.
	 *
	 * @return array
	 */
	public function getModelsToReload()
	{
		return $this->modelsToReload;
	}

	/**
	 * Fetch answer based on boolean.
	 *
	 * @param $boolean
	 *
	 * @return string
	 */
	public function getAnswer($boolean)
	{
		if ($boolean === true || $boolean === false || $boolean === 0 || $boolean === 1 || $boolean === "1" || $boolean === "0") {
			if ($boolean == 1 || $boolean == true) {
				return 'Yes';
			}

			return 'No';
		}

		return $boolean;
	}

	/**
	 * Boot method form model
	 */
	public static function boot()
	{
		\Illuminate\Database\Eloquent\Model::boot();

		static::saving(function ($object) {
			return $object::isValid($object);
		});

		static::saved(function ($object) {
			Self::reloadModels($object);

			return true;
		});

		static::deleted(function ($object) {
			Self::reloadModels($object);

			return true;
		});
	}

	/**
	 * Flush models cache.
	 *
	 * @param Model $object
	 */
	protected static function reloadModels(\App\Models\Model $object)
	{
		$models = $object->getModelsToReload();
		$models[] = get_class($object);
		$models = array_unique($models);
		foreach ($models as $model) {
			if (!str_contains($model, 'App\\Models\\')) {
				$model = 'App\\Models\\' + $model;
			}
			if (class_exists($model)) {
				Self::$self->flushCache(new $model());
			}
		}
	}

	/**
	 * Creates slug.
	 *
	 * @link https://laracasts.com/discuss/channels/general-discussion/how-to-validate-a-slug-unique-in-laravel-5
	 *
	 * @param $value
	 * @param string $column
	 *
	 * @return string
	 */
	public function getSlug($value, $column = 'slug')
	{
		$slug = Str::slug($value);
		$latestSlug = $this->whereRaw($column . " LIKE '^{$slug}(-[0-9]+)?$' and id != '{$this->id}'")
			->latest('id')
			->pluck($column);

		if ($latestSlug) {
			$slugPieces = explode('-', $latestSlug);
			$number = intval(end($slugPieces));
			$slug .= '-' . ($number + 1);
		}

		return $slug;
	}

	/**
	 * Fetch hidden fields for model object.
	 *
	 * @param Model $object
	 *
	 * @return array
	 */
	private static function getHiddenFields($object)
	{
		$hiddenFields = [];
		if ($object instanceof User) {
			$hiddenFields['password'] = $object->getAuthPassword();
		}

		return $hiddenFields;
	}

	/**
	 * Validates model.
	 *
	 * @link http://forumsarchive.laravel.io/viewtopic.php?pid=46571
	 *
	 * @param $data
	 * @param array $rules
	 *
	 * @return bool
	 */
	public static function isValid($data, $rules = [])
	{
		$id = null;
		if (is_object($data)) {
			$hiddenFields = self::getHiddenFields($data);
			$data = $data->toArray();
			if (isset($data['id'])) {
				$id = $data['id'];
			}

			$data += $hiddenFields;
		}

		if (count($rules) == 0) {
			$rules = static::$rules;
		}

		if (is_numeric($id)) {
			array_walk($rules, function (&$item) use ($id) {
				if (stripos($item, ':id:') !== false) {
					$item = str_ireplace(':id:', $id, $item);
				}
			});
		}

		$v = Validator::make($data, $rules);
		if ($v->passes()) {
			return true;
		} else {
			static::$errors = $v->messages();

			return false;
		}
	}
}