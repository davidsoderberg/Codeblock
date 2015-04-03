<?php namespace App\Services;

use Exception;
use ReflectionClass;
use Illuminate\Support\Str;

class AnnotationService{

	private $class;
	private $annotation;
	private $values;

	public function __construct($class, $annotation){
		$this->values = array();
		$this->class = new ReflectionClass($class);
		$this->annotation = $annotation;
		$this->fetchValues($annotation);
	}

	public function getValues($method = null){
		if(is_null($method)) {
			return $this->values;
		}
		return $this->values[$method];
	}

	private function fetchValues($annotation){
		$parent = $this->class->getParentClass();
		foreach($this->class->getMethods() as $method) {
			if(!$parent->hasMethod($method->getName())) {
				$comment = $method->getDocComment();
				if(Str::contains($comment, $annotation)) {
					$comment = explode($annotation, $comment);
					$AnnotationComment = explode(' ', $comment[1]);
					$AnnotationValue = trim($AnnotationComment[1]);
					if(!in_array($AnnotationValue, $this->values)) {
						$this->values[$method->getName()] = $AnnotationValue;
					}else{
						Throw new Exception($AnnotationValue.'in class'.$this->class->getName().' does already exists.');
					}
				}
			}
		}
	}

}