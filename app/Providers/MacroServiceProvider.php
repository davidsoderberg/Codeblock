<?php namespace App\Providers;

use App\Services\HtmlBuilder;
use Illuminate\Html\HtmlServiceProvider;

class MacroServiceProvider extends HtmlServiceProvider {
	protected function registerHtmlBuilder() {
		$this->app->bindShared('html', function($app)
		{
			return new HtmlBuilder($app['url']);
		});
	}
}