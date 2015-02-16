<?php namespace DevSwert\LaCrud;

	use DevSwert\LaCrud\Controller\LaCrudController;
	use DevSwert\LaCrud\Data\Manager\LaCrudManager;
	use DevSwert\LaCrud\Data\Repository\LaCrudRepository;
	use DevSwert\LaCrud\Data\Entity\LaCrudBaseEntity;
	use Illuminate\Support\Facades\Route;

	class LaCrud{
	 
		private $prefix;
		private $appName;

	    public function RegisterCrud($routes){
	    	foreach ($routes as $route => $controller){
	    		$table = str_replace("_", "-",(is_numeric($route)) ? $controller : $route);
	    		$final = str_replace("_", "-",  (isset($this->prefix)) ? $this->prefix.'/'.$table : $table);
	    		//$final = (isset($this->prefix)) ? $this->prefix.'/'.$table : $table;

	    		$entity = new LaCrudBaseEntity();
				$entity->table = ((is_numeric($route)) ? $controller : $route);

				$manager = new LaCrudManager($entity);

				$repository = new LaCrudRepository($entity);
				$repository->routes($routes);

				$config = new Configuration();
				$config->title(ucfirst(((is_numeric($route)) ? $controller : $route)));
				$config->userInfo(\Auth::user());

				$controllerFinalName = $this->appName.'\\Http\\Controllers\\'.$controller;
    			$functional = (!is_numeric($route)) ? new $controllerFinalName($repository,$manager,$config) : new LaCrudController($repository,$manager,$config);

				Route::get($final, array('as' => 'lacrud.'.$table.'.index',function() use($functional){
	    			return $functional->index();
	    		}));

		    	Route::get($final.'/create', array('as' => 'lacrud.'.$table.'.create',function() use($functional){
		    		return $functional->create();
	    		}));

	    		Route::post($final, array('as' => 'lacrud.'.$table.'.store',function() use($functional){
		    		return $functional->store();
	    		}));

	    		Route::get($final.'/{id}', array('as' => 'lacrud.'.$table.'.show',function($id) use($functional){
		    		return $functional->show($id);
	    		}));

	    		Route::get($final.'/{id}/edit', array('as' => 'lacrud.'.$table.'.edit',function($id) use($functional){
		    		return $functional->edit($id);
	    		}));

	    		Route::put($final.'/{id}', array('as' => 'lacrud.'.$table.'.update',function($id) use($functional){
		    		return $functional->update($id);
	    		}));

	    		Route::delete($final.'/{id}', array('as' => 'lacrud.'.$table.'.delete',function($id) use($functional){
		    		return $functional->destroy($id);
	    		}));
			}
	    	return $this;
	    }

	    public function prefix($prefix){
	    	$this->prefix = $prefix;
	    	return $this;
	    }

	    public function appName($name){
	    	$this->appName = $name;
	    	return $this;
	    }
	 
	}
