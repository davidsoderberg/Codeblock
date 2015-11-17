<?php namespace App\Services\Annotation;

use App\Repositories\IRepository;
use ReflectionClass;
use Illuminate\Support\Str;

/**
 * Class Annotation
 * @package App\Services\Annotation
 */
abstract class Annotation {

	/**
	 * @var ReflectionClass
	 */
	private $class;
	/**
	 * @var array
	 */
	private $values;
	/**
	 * @var
	 */
	protected $annotation;

	private $shouldExclude = true;

	private $exclude = '@exclude';

	/**
	 * @param $class
	 * @param boolean $exclude
	 */
	public function __construct($class, $exclude = true) {
		$this->values = [];
		$this->class = new ReflectionClass($class);
		$this->setExclude($exclude);
		$this->fetchValues();
	}

	/**
	 * @param $boolean
	 */
	public function setExclude($boolean) {
		if($boolean === true || $boolean === false) {
			$this->shouldExclude = $boolean;

			return;
		}
		if($this->shouldExclude) {
			$this->shouldExclude = false;

			return;
		}
		$this->shouldExclude = true;
	}

	/**
	 * Get values from method.
	 *
	 * @param null $method
	 *
	 * @return array|string
	 */
	protected function getValues($method = null) {
		if(is_null($method)) {
			return $this->values;
		}
		if(isset($this->values[$method])) {
			return $this->values[$method];
		}

		return '';
	}

	/**
	 * Fetch values from all method from a class.
	 */
	private function fetchValues() {
		$parent = $this->class->getParentClass();
		foreach($this->class->getMethods() as $method) {
			if(!$parent->hasMethod($method->getName())) {
				$comment = $method->getDocComment();
				if($this->isExcluded($comment) && Str::contains($comment, $this->annotation)) {
					$comment = explode($this->annotation, $comment);
					$AnnotationComment = explode(' ', $comment[1]);
					$AnnotationValue = trim($AnnotationComment[1]);
					if(!in_array($AnnotationValue, $this->values)) {
						$this->values[$method->getName()] = $AnnotationValue;
					}
				}
			}
		}
	}

	/**
	 * Fetches all method names.
	 * @return array
	 */
	public function getMethods() {
		return array_keys($this->values);
	}


	/**
	 * Gets all classes that should be walked through.
	 *
	 * @return array
	 */
	protected function getClasses($path = '/Http/Controllers') {
		$handle = opendir(app_path() . $path);
		$classes = [];
		while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
				$class = explode('.', $entry);
				$classes[] = $class[0];
			}
		}

		return $classes;
	}

	public function delete(IRepository $repo) {
		$models = $repo->get();
		foreach($models as $model) {
			$repo->delete($model->id);
		}
	}

	/**
	 * @param $comment
	 *
	 * @return bool
	 */
	private function isExcluded($comment) {
		$excluded = true;
		if($this->shouldExclude) {
			$excluded = !Str::contains($comment, $this->exclude);
		}

		return $excluded;
	}

}