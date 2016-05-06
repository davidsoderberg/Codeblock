<?php namespace App\Services;

/**
 * Class Markdown
 * @package App\Services
 */
class Markdown extends \ParsedownExtra
{

	/**
	 * Property store if all should be parsed.
	 *
	 * @var bool
	 */
	private $parseAll = false;

	/**
	 * Constructor for Markdown.
	 *
	 * @param $parseAll
	 */
	public function __construct($parseAll)
	{
		if ($parseAll === false || $parseAll === true) {
			$this->parseAll = $parseAll;
		}
	}

	/**
	 * Parsing image.
	 *
	 * @param $Excerpt
	 *
	 * @return array|void
	 */
	protected function inlineImage($Excerpt)
	{
		if ($this->parseAll) {
			return parent::inlineImage($Excerpt);
		}
		return;
	}

	/**
	 * Parsing link.
	 *
	 * @param $Excerpt
	 *
	 * @return array|void
	 */
	protected function inlineLink($Excerpt)
	{
		$Excerpt = parent::inlineLink($Excerpt);
		if (!$this->parseAll) {
			$Excerpt['element']['attributes']['rel'] = 'nofollow';
		}
		return $Excerpt;
	}
}