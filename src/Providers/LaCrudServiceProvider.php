<?php namespace DevSwert\LaCrud\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\DBAL\Types\Type;
use DevSwert\LaCrud\LaCrud;

class LaCrudServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Type::addType('enum', 'DevSwert\LaCrud\Type\Enum');
		$this->loadViewsFrom(__DIR__.'/../views', 'lacrud');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		\App::bind('lacrud', function(){
		    return new LaCrud;
		});
	}

}
