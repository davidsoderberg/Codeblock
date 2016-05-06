<?php namespace App\Services;

use Collective\Html\FormFacade;

/**
 * Class FormBuilder
 * @package App\Services
 */
class FormBuilder extends \Collective\Html\FormBuilder
{

	/**
	 * Render honeypot.
	 *
	 * @return string
	 */
	public function Honeypot()
	{
		return '<div class="display-none">' . FormFacade::input('text', 'honeyName', '',
			array('placeholder' => 'Leave this field empty')) . '</div>';
	}

	/**
	 * Close form tag.
	 *
	 * @param boolean $honeypot
	 *
	 * @return string
	 */
	public function close($honeypot = true)
	{
		if ($honeypot) {
			$honeypot = $this->Honeypot();
		} else {
			$honeypot = '';
		}
		return $honeypot . parent::close();
	}
}