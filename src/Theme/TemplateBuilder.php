<?php namespace DevSwert\LaCrud\Theme;

use DevSwert\LaCrud\Controller\LaCrudBaseController;

final class TemplateBuilder{

	private $controller;
	private $base_theme;
	private $formBuilder;

	public function __construct(LaCrudBaseController $controller){
		$this->controller = $controller;
		$this->formBuilder = new FormBuilder($this->resolveFolderTheme(),$this->controller->configuration->theme());
	}

	public function deniedForAccess($message){
		$themePublishFolder = $this->resolveFolderTheme();
		$this->base_theme = (is_dir($themePublishFolder)) ? 'packages.DevSwert.LaCrud.'.$this->controller->configuration->theme() : 'lacrud::Default.';

		return view($this->base_theme.'partials.403',array(
			'header' => $this->getHeaderTheme(true,1),
			'template' => $this->base_theme,
			'message' => $message,
			'footer' => $this->getFooterTheme()
		));
	}
	
	public function render($parentFunction,$id){
		$view = $this->resolveTemplateName($parentFunction);

		switch ($view) {
			case 'index':
				return $this->renderList();
				break;
			case 'create':
				return $this->renderCreate();
				break;
			case 'show':
				if( is_null($id) ){
					return $this->throwException('ID is required for show register');
				}
				return $this->showRegister($id);
				break;
			case 'edit':
				if( is_null($id) ){
					return $this->throwException('ID is required for edit register');
				}
				return $this->showRegisterForEdit($id);
				break;
			default:
				return $this->throwException('"'.$view .'" view not found for render');
				break;
		}
	}

	private function renderList(){
		$columns = $this->controller->repository->getColumns();
		$keys = $this->controller->repository->getHeaders($columns);
		$headers = $this->controller->repository->getHeaders($columns,true);
		$data = $this->controller->repository->filterData($this->controller->repository->get());

		$themePublishFolder = $this->resolveFolderTheme();
		$this->base_theme = (is_dir($themePublishFolder)) ? 'packages.DevSwert.LaCrud.'.$this->controller->configuration->theme() : 'lacrud::Default.';

		return view($this->base_theme.'pages.index',array(
			'header' => $this->getHeaderTheme(true),
			'template' => $this->base_theme,
			'headers' => $headers,
			'keys' => $keys,
			'data' => $data,
			'entity' => \Request::segment(count(explode('/', \Request::path()))),
			'footer' => $this->getFooterTheme()
		));
	}

	private function renderCreate(){
		$columnsSchema = $this->controller->repository->getColumns();
		$columns = $this->clearColumns($columnsSchema);

		$themePublishFolder = $this->resolveFolderTheme();
		$this->base_theme = (is_dir($themePublishFolder)) ? 'packages.DevSwert.LaCrud.'.$this->controller->theme : 'lacrud::Default';

		return view($this->base_theme.'.pages.create',array(
			'header' => $this->getHeaderTheme(),
			'template' => $this->base_theme,
			'columns' => $columns,
			'form' => $this->formBuilder->generateFormAddOrEdit($columns),
			'entity' => \Request::segment(count(explode('/', \Request::path())) -1 ),
			'footer' => $this->getFooterTheme()
		));
	}

	private function showRegister($value){
		$columnsSchema = $this->controller->repository->getColumns();
		$primaryKey = $this->controller->repository->getPrimaryKey();

		$information = $this->controller->repository->find($primaryKey,$value);
		$data = array();

		foreach ($columnsSchema as $key => $value){
			if(!in_array($key, $this->controller->repository->fieldsNotSee)){
				if( array_key_exists($key, $this->controller->repository->nameDisplayForeignsKeys) )
		            $data[$key] = $this->controller->repository->searchAliasValue($key,$information->$key);
		        if( array_key_exists($key, $this->controller->repository->fakeRelation) )
		            $data[$key] = $this->controller->repository->searchFakeValue($key,$information->$key);
		        else
					$data[$key] = $information->$key;
			}
		}

		$themePublishFolder = $this->resolveFolderTheme();
		$this->base_theme = (is_dir($themePublishFolder)) ? 'packages.DevSwert.LaCrud.'.$this->controller->theme : 'lacrud::Default';

		return view($this->base_theme.'.pages.show',array(
			'header' => $this->getHeaderTheme(),
			'template' => $this->base_theme,
			'columns' => $data,
			'pk' => $primaryKey,
			'alias' => $this->controller->repository->displayAs,
			'entity' => \Request::segment(count(explode('/', \Request::path())) -1 ),
			'footer' => $this->getFooterTheme()
		));
	}

	private function showRegisterForEdit($primary){
		$primaryKey = $this->controller->repository->getPrimaryKey();
		$information = $this->controller->repository->find($primaryKey,$primary);

		$columnsSchema = $this->controller->repository->getColumns();
		$columns = $this->clearColumns($columnsSchema,$information,$primary);


		foreach ($columns as $key => $value){
			if( array_key_exists('isAutoincrement', $value) && $value['isAutoincrement'] ){
				unset($columns[$key]);
			}
		}

		$themePublishFolder = $this->resolveFolderTheme();
		$this->base_theme = (is_dir($themePublishFolder)) ? 'packages.DevSwert.LaCrud.'.$this->controller->theme : 'lacrud::Default';

		return view($this->base_theme.'.pages.edit',array(
			'header' => $this->getHeaderTheme(),
			'template' => $this->base_theme,
			'form' => $this->formBuilder->generateFormAddOrEdit($columns),
			'pk' => $primary,
			'alias' => $this->controller->repository->displayAs,
			'entity' => \Request::segment(count(explode('/', \Request::path())) -2 ),
			'footer' => $this->getFooterTheme()
		));
	}

	//Utils
    private function resolveTemplateName($functionName){
    	$base = strtolower(substr($functionName, 0,4));
    	if($base == 'base'){
	    	$template = substr($functionName, 4);
	    	return strtolower($template);
    	}
    	return strtolower($functionName);
    }

    private function resolveFolderTheme(){
    	$this->controller->configuration->theme((is_null($this->controller->configuration->theme())) ? 'Default' : $this->controller->configuration->theme());
		return app_path().'/views/packages/DevSwert/LaCrud/'.$this->controller->configuration->theme();
    }

    private function getHeaderTheme($isIndex = false,$positionEntityOnURL = 0){
    	$basic = array(
			'title' => $this->controller->configuration->title(),
			'subtitle' => $this->controller->configuration->subtitle(),
			'userinfo' => $this->controller->configuration->userInfo(),
			'entity' => \Request::segment(count(explode('/', \Request::path())) - $positionEntityOnURL ),
			'isIndex' => $isIndex,
			'entityNames' => $this->resolveRoutesPublish()
		);
		$moreInfo = $this->purifyHeaderInfo($this->controller->configuration->moreDataHeader());
		$information = array_merge($moreInfo,$basic);
    	return view($this->base_theme.'.partials.header',$information);
    }

    private function getFooterTheme(){
    	return view($this->base_theme.'.partials.footer',$this->controller->configuration->moreDataFooter());
    }

    private function purifyHeaderInfo($data){
    	if(array_key_exists('title', $data))
    		unset($data['title']);
    	if(array_key_exists('subtitle', $data))
    		unset($data['subtitle']);
    	if(array_key_exists('userinfo', $data))    		
    		unset($data['userinfo']);
    	return $data;
    }

    private function clearColumns($columnsSchema,$model = null, $primary = 0){
    	$columns = array();
		$primaryKey = $this->controller->repository->getPrimaryKey();
		$foreignKeys = $this->controller->repository->getForeignKeys();
        foreach($columnsSchema as $column) {

        	$canAddColumn = false;
        	if( $column->getName() == 'created_at' && $this->controller->getCreatedAt() ){
        		$canAddColumn = true;
	        }
	        else if( $column->getName() == 'updated_at' && $this->controller->getUpdatedAt() ){
        		$canAddColumn = true;
	        }
	        else if( in_array($column->getName() , $this->controller->manager->fieldsNotEdit ) ){
	        	$canAddColumn = false;
	        }
	        else if( $column->getName() != 'created_at' && $column->getName() != 'updated_at' ){
	        	$canAddColumn = true;
	        }

	        if($canAddColumn){
	        	$nameColumn = (array_key_exists($column->getName(), $this->controller->repository->displayAs)) ? $this->controller->repository->displayAs[$column->getName()] : $column->getName();
		        if(is_object($model)){
		        	try{
			    		$value = $model->{$column->getName()};
			    	}
			    	catch(Exeption $e){
			    		$value = '';
			    	}
			    }
			    else
			    	$value = '';

	            array_push($columns,array(
	            	'name' => $column->getName(),
	            	'name_display' => ucfirst(str_replace('_',' ',$nameColumn )),
	            	'type' => $column->getType()->getName(),
	            	'default' => $column->getDefault(),
	            	'length' => $column->getLength(),
	            	'options' => $this->controller->repository->getOptionsEnum($column),
	            	'isPrimary' => ($primaryKey == $column->getName()) ? true : false,
	            	'isAutoincrement' => $column->getAutoincrement(),
	            	'value' => $value,
	            	'isPassword' => ( in_array($column->getName(), $this->controller->repository->isPassword) ) ? true : false,
	            	'hasForeignKeys' => $this->controller->repository->findIsForeignKey($column,$foreignKeys)
	            ));
	        }
        }
        $columns['hasManyRelation'] = $this->controller->repository->findManyRelations($primary);
        return $columns;
    }

    private function resolveRoutesPublish(){
    	$response = array();
    	foreach ($this->controller->repository->routes() as $route => $controller){
    		array_push($response,array(
    			'table' => str_replace("_", "-",(is_numeric($route)) ? $controller : $route),
    			'name'  => ucfirst( str_replace("_", " ", (is_numeric($route)) ? $controller : $route) )
    		));
    	}
    	return $response;
    }

    //Throw exceptions
    private function throwException($message){
    	$trace = debug_backtrace();
		trigger_error(
            $message.
            ' on ' . $trace[0]['file'] .
            ' in line ' . $trace[0]['line'],
            E_USER_ERROR);
		return null;
    }
}