<?php namespace App\Services;

use Illuminate\Html\FormFacade;

/**
 * Class FormBuilder
 * @package App\Services
 */
class FormBuilder extends \Illuminate\Html\FormBuilder{

	/**
	 * @return string
	 */
	public function Honeypot(){
		return '<div class="display-none">'.FormFacade::input('text', 'honeyName', '', array('placeholder' => 'Leave this field empty')).'</div>';
	}

	/**
	 * @return string
	 */
	public function close(){
		return $this->Honeypot().parent::close();
	}
}