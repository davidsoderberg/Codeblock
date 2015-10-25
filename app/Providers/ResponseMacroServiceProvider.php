<?php namespace App\Providers;

use Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider {

	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot() {
		Response::macro('xml', function (array $vars, $status = 200, array $header = [], \SimpleXMLElement $xml = null) {
			if(is_null($xml)) {
				$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><response/>');
			}
			foreach($vars as $key => $value) {
				if(is_array($value)) {
					if(is_numeric($key)){
						$key = 'item';
					}
					Response::xml($value, $status, $header, $xml->addChild($key));
				} else {
					$xml->addChild($key, $value);
				}
			}
			if(empty($header)) {
				$header['Content-Type'] = 'application/xml';
			}
			return Response::make($xml->asXML(), $status, $header);
		});
	}

	public function register() {}

}