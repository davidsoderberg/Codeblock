<?php namespace App\Services\Annotation;

use Exception;
use ReflectionClass;
use Illuminate\Support\Str;

class Annotation{

	private $class;
	private $values;
	protected $annotation;

	public function __construct($class){
		$this->values = array();
		$this->class = new ReflectionClass($class);
		$this->fetchValues();
	}

	protected function getValues($method = null){
		if(is_null($method)) {
			return $this->values;
		}
		if(isset($this->values[$method])) {
			return $this->values[$method];
		}
		return '';
	}

	private function fetchValues(){
		$parent = $this->class->getParentClass();
		foreach($this->class->getMethods() as $method) {
			if(!$parent->hasMethod($method->getName())) {
				$comment = $method->getDocComment();
				if(Str::contains($comment, $this->annotation)) {
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

}