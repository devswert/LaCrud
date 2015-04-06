<?php namespace DevSwert\LaCrud\Theme;

use DevSwert\LaCrud\Controller\LaCrudBaseController;
use DevSwert\LaCrud\Utils;
use Carbon\Carbon;

final class TemplateBuilder{
	use Utils;

	private $controller;
	private $base_theme;
	private $formBuilder;

	private $templateHeader;
	private $templateFooter;
	private $templateForbidden;
	private $templateLayout;

	public function __construct(LaCrudBaseController $controller){
		$this->controller = $controller;
		$this->formBuilder = new FormBuilder($this->resolveFolderTheme(),$this->controller->configuration->theme());

		$themePublishFolder = $this->resolveFolderTheme();
		if(!is_dir($themePublishFolder)){
			$this->throwException('Don\'t exist a Theme for LaCrud');
		}
		$this->base_theme = 'lacrud::'.$this->controller->configuration->theme().'.';

		$headerPath    = base_path().'/resources/views/partials/header.blade.php';
		$footerPath    = base_path().'/resources/views/partials/footer.blade.php';
		$forbiddenPath = base_path().'/resources/views/partials/403.blade.php';
		$layoutPath    = base_path().'/resources/views/layout.blade.php';

		$this->templateHeader    = ( file_exists($headerPath) ? 'partials.header' : $this->base_theme.'partials.header' );
		$this->templateFooter    = ( file_exists($footerPath) ? 'partials.footer' : $this->base_theme.'partials.footer' );
		$this->templateForbidden = ( file_exists($forbiddenPath) ? 'partials.403' : $this->base_theme.'partials.403' );
		$this->templateLayout    = ( file_exists($layoutPath) ? 'layout' : $this->base_theme.'layout' );
	}

	public function deniedForAccess($message){
		return view($this->templateForbidden,array(
			'header' => $this->getHeaderTheme(true,1),
			'template' => $this->templateLayout,
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

	public function confirmHardDelete(){
		$entity = \Request::segment(count(explode('/', \Request::path()) ) -1 );
		$id = \Request::segment(count(explode('/', \Request::path()) ) );
		return view($this->base_theme.'.forms.hardDelete',array('entity' => $entity,'id' => $id));
	}

	private function renderList(){
		$columns = $this->controller->repository->getColumns();
		$keys = $this->controller->repository->getHeaders($columns);
		$headers = $this->controller->repository->getHeaders($columns,true);
		$data = $this->controller->repository->filterData($this->controller->repository->get());

		return view($this->base_theme.'pages.index',array(
			'header' => $this->getHeaderTheme(true),
			'template' => $this->templateLayout,
			'headers' => $headers,
			'keys' => $keys,
			'data' => $data,
			'permission' => $this->getPermissions(),
			'entity' => \Request::segment(count(explode('/', \Request::path()))),
			'footer' => $this->getFooterTheme()
		));
	}

	private function renderCreate(){
		$columnsSchema = $this->controller->repository->getColumns();
		$columns = $this->clearColumns($columnsSchema);

		return view($this->base_theme.'pages.create',array(
			'header' => $this->getHeaderTheme(),
			'template' => $this->templateLayout,
			'columns' => $columns,
			'permission' => $this->getPermissions(),
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
		        else if( array_key_exists($key, $this->controller->repository->fakeRelation) )
		            $data[$key] = $this->controller->repository->searchFakeValue($key,$information->$key);
		        else
					$data[$key] = $information->$key;
			}
		}

		return view($this->base_theme.'pages.show',array(
			'header' => $this->getHeaderTheme(),
			'template' => $this->templateLayout,
			'columns' => $data,
			'pk' => $primaryKey,
			'permission' => $this->getPermissions(),
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
			if( is_array($value) && array_key_exists('isAutoincrement', $value) && $value['isAutoincrement'] ){
				unset($columns[$key]);
			}
		}

		return view($this->base_theme.'pages.edit',array(
			'header' => $this->getHeaderTheme(),
			'template' => $this->templateLayout,
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
    	return base_path().'/resources/views/vendor/LaCrud/'.$this->controller->configuration->theme();
    }

    private function getHeaderTheme($isIndex = false,$positionEntityOnURL = 0){
    	$basic = array(
			'title' => $this->controller->configuration->title(),
			'subtitle' => $this->controller->configuration->subtitle(),
			'entity' => \Request::segment(count(explode('/', \Request::path())) - $positionEntityOnURL ),
			'isIndex' => $isIndex,
			'permission' => $this->getPermissions(),
			'entityNames' => $this->resolveRoutesPublish()
		);
		$moreInfo = $this->purifyHeaderInfo($this->controller->configuration->moreDataHeader());
		$information = array_merge($moreInfo,$basic);
    	return view($this->templateHeader,$information);
    }

    private function getFooterTheme(){
    	$basic = array(
    		'permission' => $this->getPermissions()
    	);
    	$moreInfo = $this->controller->configuration->moreDataFooter();
    	$information = array_merge($moreInfo,$basic);
    	return view($this->templateFooter,$information);
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
		if(\Session::has('fields.datetime'))
			\Session::forget('fields.datetime');

		if(\Session::has('fields.booleans'))
			\Session::forget('fields.booleans');

		$fieldsDatetime = array();
		$fieldsBooleans = array();
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

	        	$type = $column->getType()->getName();
				if( $type == 'text' ){
					$type = ( array_key_exists($column->getName(),$this->controller->manager->disabledTextEditor) ) ? 'simpletext' : 'text' ;
				}
				if( $type == 'string' ){
					$type = ( array_key_exists($column->getName() , $this->controller->repository->uploads ) ) ? 'upload' : 'string' ;
				}

	        	$nameColumn = (array_key_exists($column->getName(), $this->controller->repository->displayAs)) ? $this->controller->repository->displayAs[$column->getName()] : $column->getName();
		        if(is_object($model)){
		        	try{
		        		if( !in_array($type, ['datetime','date','timestamp']) ){
			    			$value = $model->{$column->getName()};
			    			if( $type == 'boolean' ){
					    		array_push($fieldsBooleans, $column->getName());
					    	}
		        		}
		        		else if($type == 'date'){
		        			$tmp = explode('-',$model->{$column->getName()});
		        			$value = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
		        			$fieldsDatetime[$column->getName()] = $type;
		        		}
		        		else{
		        			$value = [
		        				'date' => $model->{$column->getName()}->format('d-m-Y'),
		        				'time' => $model->{$column->getName()}->toTimeString()
		        			];
		        			$fieldsDatetime[$column->getName()] = $type;
		        		}

		        		if( array_key_exists($column->getName(), $this->controller->repository->uploads) && array_key_exists('isImage', $this->controller->repository->uploads[$column->getName()])  && $this->controller->repository->uploads[$column->getName()]['isImage'] ){
		        			$type = 'image';
		        		}
			    	}
			    	catch(Exeption $e){
			    		$value = '';
			    	}
			    }
			    else{
			    	if( in_array($type, ['datetime','timestamp']) ){
						$value = [
	        				'date' => '',
	        				'time' => ''
	        			];
	        			$fieldsDatetime[$column->getName()] = $type;
			    	}
			    	else if( $type == 'date' ){
			    		$fieldsDatetime[$column->getName()] = $type;
			    		$value = '';
			    	}
			    	else if( $type == 'boolean' ){
			    		array_push($fieldsBooleans, $column->getName());
			    		$value = '';
			    	}
			    	else{
			    		$value = '';
			    	}
			    }

	            array_push($columns,array(
	            	'name' => $column->getName(),
	            	'name_display' => ucfirst(str_replace('_',' ',$nameColumn )),
	            	'type' => $type,
	            	'default' => $column->getDefault(),
	            	'length' => $column->getLength(),
	            	'options' => $this->controller->repository->getOptionsEnum($column),
	            	'isPrimary' => ($primaryKey == $column->getName()) ? true : false,
	            	'isAutoincrement' => $column->getAutoincrement(),
	            	'value' => $value,
	            	'paths' => $this->getPathsIfHave($column),
	            	'isPassword' => ( in_array($column->getName(), $this->controller->repository->isPassword) ) ? true : false,
	            	'hasForeignKeys' => $this->controller->repository->findIsForeignKey($column,$foreignKeys)
	            ));
	        }
        }

        \Session::put('fields.datetime', $fieldsDatetime);
        \Session::put('fields.booleans', $fieldsBooleans);
        $columns['hasManyRelation'] = $this->controller->repository->findManyRelations($primary);
        return $columns;
    }

    private function resolveRoutesPublish(){
    	$response = array();
    	foreach ($this->controller->repository->routes() as $route => $controller){
    		if( !is_array($controller) ){
	    		$table = str_replace("_", "-",(is_numeric($route)) ? $controller : $route);
	    		$name  = ucfirst( str_replace("_", " ", (is_numeric($route)) ? $controller : $route) );
	    		$canAdd = true;
	    	}
	    	else{
	    		$canAdd = ( array_key_exists('showInMenu', $controller) ) ? $controller['showInMenu'] : true;
	    		if( array_key_exists('controller', $controller) ){
	    			$table = str_replace("_", "-",(is_numeric($route)) ? $controller['controller'] : $route);
	    			$name  = ucfirst( str_replace("_", " ", (is_numeric($route)) ? $controller['controller'] : $route) );
	    		}
	    		else{
	    			$table = str_replace("_", "-",(is_numeric($route)) ? $controller : $route);
	    			$name  = ucfirst( str_replace("_", " ", (is_numeric($route)) ? $controller : $route) );
	    		}
	    	}

	    	if($canAdd){
		    	array_push($response,array(
					'table' => $table,
					'name'  => $name
				));
		    }
    	}
    	return $response;
    }

    private function getPermissions(){
    	return array(
    		'add' => $this->controller->canAdd(),
    		'edit' => $this->controller->canEdit(),
    		'delete' => $this->controller->canDelete(),
    		'show' => $this->controller->canRead(),
    		'print' => $this->controller->canPrint(),
    		'export' => $this->controller->canExport()
    	);
    }

    private function getPathsIfHave($column){
    	$paths = null;
	    if( array_key_exists($column->getName() , $this->controller->repository->uploads ) ){
	    	if( is_array( $this->controller->repository->uploads[$column->getName()] ) ){
	    		$paths = [
	    			'public'  => ( array_key_exists('public' , $this->controller->repository->uploads[$column->getName()]) ) ? $this->controller->repository->uploads[$column->getName()]['public'] : '',
	    			'private' => ( array_key_exists('private', $this->controller->repository->uploads[$column->getName()]) ) ? $this->controller->repository->uploads[$column->getName()]['private'] : ''
	    		];
	    	}
	    	else{
	    		$paths = [
	    			'public'  => $this->controller->repository->uploads[$column->getName()],
	    			'private' => ''
	    		];
	    	}
	    }
		return $paths;
    }
}