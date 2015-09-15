<?php

class UnitCase extends Illuminate\Foundation\Testing\TestCase {

	use TestTrait;

	public function setUp(){
		parent::setUp();
		\Illuminate\Support\Facades\Cache::flush();
		$this->resetEvents();
	}

}
