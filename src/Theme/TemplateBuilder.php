<?php namespace DevSwert\LaCrud\Theme;

use DevSwert\LaCrud\Controller\LaCrudBaseController;
use DevSwert\LaCrud\Utils;
use Carbon\Carbon;

final class TemplateBuilder{
	use Utils;

	/**
	 * An instance of LaCrudController
	 *
	 * @var string
	 */
	private $controller;

	/**
	 * A string that contains the base path of theme.
	 *
	 * @var string
	 */
	private $base_theme;

	/**
	 * An instance of FormBuilder
	 *
	 * @var string
	 */
	private $formBuilder;

	/**
	 * Has a template route of header template.
	 *
	 * @var string
	 */
	private $templateHeader;

	/**
	 * Has a template route of footer template.
	 *
	 * @var string
	 */
	private $templateFooter;

	/**
	 * Has a template route of forbidden access template.
	 *
	 * @var string
	 */
	private $templateForbidden;

	/**
	 * Has a template route of layout's theme.
	 *
	 * @var string
	 */
	private $templateLayout;

	/**
	 * In the moment of instance the class create the $formbuiler class and 
	 * define the templetes for use in the header, footer, layout and
	 * forbidden access. Too define the base_path of theme. 
	 *
	 * @param $controller   LaCrudBaseController
	 * @return void
	 */
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

	/**
	 * Render the view Forbidden access in the 
	 * any CRUD operation.
	 *
	 * @param $message  Message for render in the view
	 * @return string View render
	 */
	public function deniedForAccess($message){
		return view($this->templateForbidden,array(
			'header' => $this->getHeaderTheme(true,1),
			'template' => $this->templateLayout,
			'message' => $message,
			'footer' => $this->getFooterTheme()
		));
	}
	
	/**
	 * This method is a dispatcher for the type template
	 * for render.
	 *
	 * @param $parentFunction  The name of function that call in the controller
	 * @param $id  The value in the case of edit, delete or show a register.
	 * @return string View render
	 */
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

	/**
	 * Returns the render view of Hard delete confirmed.
	 *
	 * @return string The render view
	 */
	public function confirmHardDelete(){
		$entity = \Request::segment(count(explode('/', \Request::path()) ) -1 );
		$id = \Request::segment(count(explode('/', \Request::path()) ) );
		return view($this->base_theme.'.forms.hardDelete',array('entity' => $entity,'id' => $id));
	}

	/**
	 * Returns the list template with all data necessary
	 * for display in this request.
	 *
	 * @return string Render view
	 */
	private function renderList(){
		$columns = $this->controller->repository->getColumns();
		$keys = $this->controller->repository->getHeaders($columns);
		$headers = $this->controller->repository->getHeaders($columns,true);
		$data = $this->controller->repository->filterData($this->controller->repository->get());

		return view($this->base_theme.'pages.index',array(
			'header' => $this->getHeaderTheme(true),

			'title' => $this->controller->configuration->title(),
			'subtitle' => $this->controller->configuration->subtitle(),
			'isIndex' => true,
			'permission' => $this->getPermissions(),

			'template' => $this->templateLayout,
			'headers' => $headers,
			'keys' => $keys,
			'data' => $data,
			'permission' => $this->getPermissions(),
			'entity' => \Request::segment(count(explode('/', \Request::path()))),
			'footer' => $this->getFooterTheme()
		));
	}

	/**
	 * Returns the create template with all inputs
	 * for create a new register in the system.
	 *
	 * @return string  Render view
	 */
	private function renderCreate(){
		$columnsSchema = $this->controller->repository->getColumns();
		$columns = $this->clearColumns($columnsSchema);

		return view($this->base_theme.'pages.create',array(
			'header' => $this->getHeaderTheme(),

			'title' => $this->controller->configuration->title(),
			'subtitle' => $this->controller->configuration->subtitle(),
			'isIndex' => false,
			'permission' => $this->getPermissions(),

			'template' => $this->templateLayout,
			'columns' => $columns,
			'permission' => $this->getPermissions(),
			'form' => $this->formBuilder->generateFormAddOrEdit($columns),
			'entity' => \Request::segment(count(explode('/', \Request::path())) -1 ),
			'footer' => $this->getFooterTheme()
		));
	}

	/**
	 * Returns the show template with all information of a register.
	 *
	 * @param $value    The primary key value to display.
	 * @return string   The current value of the $str property
	 */
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

			'title' => $this->controller->configuration->title(),
			'subtitle' => $this->controller->configuration->subtitle(),
			'isIndex' => false,
			'permission' => $this->getPermissions(),

			'template' => $this->templateLayout,
			'columns' => $data,
			'pk' => $primaryKey,
			'permission' => $this->getPermissions(),
			'alias' => $this->controller->repository->displayAs,
			'entity' => \Request::segment(count(explode('/', \Request::path())) -1 ),
			'footer' => $this->getFooterTheme()
		));
	}

	/**
	 * Returns the edit template with all information of
	 * a register for edit the data.
	 *
	 * @param $primary  The primary key value to display.
	 * @return string   The current value of the $str property
	 */
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

			'title' => $this->controller->configuration->title(),
			'subtitle' => $this->controller->configuration->subtitle(),
			'isIndex' => false,
			'permission' => $this->getPermissions(),

			'template' => $this->templateLayout,
			'form' => $this->formBuilder->generateFormAddOrEdit($columns),
			'pk' => $primary,
			'alias' => $this->controller->repository->displayAs,
			'entity' => \Request::segment(count(explode('/', \Request::path())) -2 ),
			'footer' => $this->getFooterTheme()
		));
	}

	/**
	 * Returns name of template according to th method that
	 * call the function render.
	 *
	 * @param $functionName    The base name of function
	 * @return string          The name of function that call this method
	 */
    private function resolveTemplateName($functionName){
    	$base = strtolower(substr($functionName, 0,4));
    	if($base == 'base'){
	    	$template = substr($functionName, 4);
	    	return strtolower($template);
    	}
    	return strtolower($functionName);
    }

    /**
     * Return the name that Theme configured in LaCrud
     *
     * @return string   The path to the theme configured
     */
    private function resolveFolderTheme(){
    	$this->controller->configuration->theme((is_null($this->controller->configuration->theme())) ? 'Default' : $this->controller->configuration->theme());
    	return base_path().'/resources/views/vendor/LaCrud/'.$this->controller->configuration->theme();
    }

    /**
     * Returns header view rendered.
     *
     * @param $isIndex   			 Indicate if is index :v
     * @param $positionEntityOnURL   Indicate the position on the URL where is the name of entity
     * @return string
     */
    public function getHeaderTheme($isIndex = false,$positionEntityOnURL = 0){
    	$basic = array(
			'entity' => \Request::segment(count(explode('/', \Request::path())) - $positionEntityOnURL ),
			'entityNames' => $this->resolveRoutesPublish()
		);
		$moreInfo = $this->purifyHeaderInfo($this->controller->configuration->moreDataHeader());
		$information = array_merge($moreInfo,$basic);
    	return view($this->templateHeader,$information);
    }

    /**
	 * Returns the footer template rendered
	 *
	 * @return string 
	 */
    public function getFooterTheme(){
    	$basic = array(
    		'permission' => $this->getPermissions()
    	);
    	$moreInfo = $this->controller->configuration->moreDataFooter();
    	$information = array_merge($moreInfo,$basic);
    	return view($this->templateFooter,$information);
    }

    /**
     * Delete the basic info in the aditional data in the 
     * more data for the header template
     *
     * @param $data    Array with aditional information for header template
     * @return array
     */
    private function purifyHeaderInfo($data){
    	if(array_key_exists('title', $data))
    		unset($data['title']);
    	if(array_key_exists('subtitle', $data))
    		unset($data['subtitle']);
    	return $data;
    }

    /**
     * This method prepare all columns and create an array with all 
     * information of each field for edit or create a register.
     *
     * @return string The current value of the $str property
     */
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
	        else if( $column->getName() == 'deleted_at' && $this->controller->getDeletedAt() ){
        		$canAddColumn = true;
	        }
	        else if( in_array($column->getName() , $this->controller->manager->fieldsNotEdit ) ){
	        	$canAddColumn = false;
	        }
	        else if( $column->getName() != 'created_at' && $column->getName() != 'updated_at' && $column->getName() != 'deleted_at' ){
	        	$canAddColumn = true;
	        }

	        if($canAddColumn){

	        	$type = $column->getType()->getName();
				if( $type == 'text' ){
					$type = ( in_array($column->getName(),$this->controller->manager->disabledTextEditor) ) ? 'simpletext' : 'text' ;
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
	            	'isEncrypted' => ( in_array($column->getName(), $this->controller->repository->isEncrypted) ) ? true : false,
	            	'hasForeignKeys' => $this->controller->repository->findIsForeignKey($column,$foreignKeys)
	            ));
	        }
        }

        \Session::put('fields.datetime', $fieldsDatetime);
        \Session::put('fields.booleans', $fieldsBooleans);
        $columns['hasManyRelation'] = $this->controller->repository->findManyRelations($primary);
        return $columns;
    }

    /**
     * Resolve the name of each route for craeate a name or
     * alias for use the 'url' method of laravel with
     * alias for each action on crud for all actions
     *
     * @return string The current value of the $str property
     */
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

    /**
     * Return the privilegies for the entity
     *
     * @return array
     */
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

    /**
     * Returns the paths if the fields is type file or upload
     *
     * @return array or null
     */
    private function getPathsIfHave($column){
    	$paths = null;
	    if( array_key_exists($column->getName() , $this->controller->repository->uploads ) ){
	    	if( is_array( $this->controller->repository->uploads[$column->getName()] ) ){
	    		if( array_key_exists('public' , $this->controller->repository->uploads[$column->getName()]) ){
	    			$paths['public'] = $this->controller->repository->uploads[$column->getName()]['public'];
	    		}
	    		if( array_key_exists('private', $this->controller->repository->uploads[$column->getName()]) ){
	    			$paths['private'] = $this->controller->repository->uploads[$column->getName()]['private'];
	    		}
	    	}
	    	else{
	    		$paths = [
	    			'public'  => $this->controller->repository->uploads[$column->getName()]
	    		];
	    	}
	    }
		return $paths;
    }
}