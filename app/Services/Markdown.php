<?php namespace App\Services;

class Markdown extends \ParsedownExtra{

	private $parseAll = false;

	public function __construct($parseAll){
		if($parseAll === false || $parseAll === true) {
			$this->parseAll = $parseAll;
		}
	}

	protected function inlineImage($Excerpt){
		if($this->parseAll){
			return parent::inlineImage($Excerpt);
		}
		return;
	}

	protected function inlineLink($Excerpt){
		$Excerpt = parent::inlineLink($Excerpt);
		if(!$this->parseAll) {
			$Excerpt['element']['attributes']['rel'] = 'nofollow';
		}
		return $Excerpt;
	}
}