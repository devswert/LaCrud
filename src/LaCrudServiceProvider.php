<?php namespace DevSwert\LaCrud;

use Illuminate\Support\ServiceProvider;
use Doctrine\DBAL\Types\Type;

class LaCrudServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services and set the content to publish.
	 *
	 * @return void
	 */
	public function boot(){
		Type::addType('enum', 'DevSwert\LaCrud\Type\Enum');
		$this->loadViewsFrom(base_path('resources/views/vendor/LaCrud'), 'lacrud');
		$this->loadTranslationsFrom(base_path('resources/lang/LaCrud'), 'lacrud');

		if(app()->runningInConsole()){
			$zip = new \ZipArchive;
		    if ($zip->open(__DIR__.'/public/Default.zip') === TRUE){
		        $zip->extractTo(__DIR__.'/public');
		        $zip->close();
		    }
		}

		$this->publishes([
		    __DIR__.'/public/Default' => public_path('/LaCrud/Default'),
		    __DIR__.'/views/Default' => base_path('resources/views/vendor/LaCrud/Default'),
		    __DIR__.'/lang/en' => base_path('resources/lang/LaCrud/en'),
		    __DIR__.'/lang/es' => base_path('resources/lang/LaCrud/es'),
		]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register(){
		\App::bind('lacrud', function(){
		    return new LaCrud;
		});
	}

}
