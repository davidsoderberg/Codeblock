<?php namespace App\Providers;

use App\Services\HtmlBuilder;
use Illuminate\Html\HtmlServiceProvider;
use App\Services\FormBuilder;

class MacroServiceProvider extends HtmlServiceProvider {
	protected function registerHtmlBuilder() {
		$this->app->singleton('html', function($app)
		{
			return new HtmlBuilder($app['url']);
		});
	}

	protected function registerFormBuilder()
	{
		$this->app->bindShared('form', function($app)
		{
			$form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

			return $form->setSessionStore($app['session.store']);
		});
	}
}