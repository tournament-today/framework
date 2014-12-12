<?php namespace Syn\Framework;

use Config, App, View;
use Illuminate\Support\ServiceProvider;
use Syn\Framework\Validators\ExtendedValidator;
use Validator;

class FrameworkServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this -> package('syn/framework');

		$this->app->bindIf('command.syn.migration', function ($app) {
			$packagePath = $app['path.base'].'/vendor';

			return new Commands\MigrationCommand($app['migrator'], $packagePath);
		});
		$this->commands(
			'command.syn.migration'
		);

		Validator::resolver(function($translator, $data, $rules, $messages)
		{
			return new ExtendedValidator($translator, $data, $rules, $messages);
		});

		require_once "Helpers/Trans.php";
	}
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		App::make('view.finder')->addLocation(base_path('app/views/' . Config::get('app.template')));
		
		View::share('template', Config::get('app.template'));
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
