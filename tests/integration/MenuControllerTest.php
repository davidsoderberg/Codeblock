<?php

class MenuControllerTest extends IntegrationCase {

	/** @test */
	public function it_verifies_that_pages_load_properly()
	{
		$this->visit('/');
	}

}