<?php

class FunctionalCase extends Illuminate\Foundation\Testing\TestCase {

	use TestTrait;

	public function __call($method, $args) {
		if(in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
			return $this->call($method, $args[0]);
		}

		throw new BadMethodCallException;
	}

	public function assertHtmlHasWord($needle, $caseSensitive = false){
		$haystack = $this->response->getContent();
		if($caseSensitive) {
			$needle = strtolower($needle);
			$haystack = strtolower($haystack);
		}
		$condition = strpos($haystack, $needle) !== false && count(preg_match('/'.$needle.'/',$haystack)) > 0;
		$message = 'Your html does not contain "'.$needle.'".';
		return PHPUnit_Framework_Assert::assertTrue($condition, $message);
	}

}
