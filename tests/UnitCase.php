<?php

class UnitCase extends Illuminate\Foundation\Testing\TestCase {

	use TestTrait;

	public function setUp(){
		parent::setUp();
		$this->resetEvents();
	}

	// Hittad på: https://github.com/laravel/framework/issues/1181
	private function resetEvents()
	{
		$models = array('App\Tag', 'App\User');
		foreach ($models as $model) {
			call_user_func(array($model, 'flushEventListeners'));
			call_user_func(array($model, 'boot'));
		}
	}
}
