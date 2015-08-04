<?php namespace App\Services;

class Markdown extends \ParsedownExtra{

	protected function inlineImage($Excerpt){
		return;
	}

	protected function inlineLink($Excerpt){
		$Excerpt = parent::inlineLink($Excerpt);
		$Excerpt['element']['attributes']['rel'] = 'nofollow';
		return $Excerpt;
	}
}