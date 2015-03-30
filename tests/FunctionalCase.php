<?php

class FunctionalCase extends TestCase {

	public function __call($method, $args) {
		if(in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
			return $this->call($method, $args[0]);
		}

		throw new BadMethodCallException;
	}

}
