<?php namespace App\Providers;

use App\Services\HtmlBuilder;
use Collective\Html\HtmlServiceProvider;
use App\Services\FormBuilder;

/**
 * Class MacroServiceProvider
 * @package App\Providers
 */
class MacroServiceProvider extends HtmlServiceProvider
{

	public function register()
	{
		parent::register();

		$this->app->singleton('html', function ($app) {
			return new HtmlBuilder($app['url'], $app['view']);
		});

		$this->app->singleton('form', function ($app) {
			$form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());

			return $form->setSessionStore($app['session.store']);
		});
	}
}