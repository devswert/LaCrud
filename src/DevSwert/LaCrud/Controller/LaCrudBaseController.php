<?php 
namespace DevSwert\LaCrud\Controller;

use DevSwert\LaCrud\Theme\TemplateBuilder;
use SimpleCrud\Http\Controllers\Controller;

abstract class LaCrudBaseController extends Controller{

	public $repository;
	public $manager;
	public $configuration;
	private $templateBuilder;
	private $showCreatedAt = false;
	private $showUpdatedAt = false;

	//Permisos del CRUD
	private $canEdit = true;
	private $canAdd = true;
	private $canDelete = true;
	private $canExport = true;
	private $canPrint = true;
	private $canRead = true;

	//Funciones basicas de Crud
	abstract public function index();
	abstract public function create();
	abstract public function store();
	abstract public function show($id);
	abstract public function edit($id);
	abstract public function update($id);
	abstract public function destroy($id);

	//Seteadores de "can"
	final public function unsetEdit(){
		$this->canEdit = false;
		return $this;
	}

	final public function unsetAdd(){
		$this->canAdd = false;
		return $this;
	}

	final public function unsetDelete(){
		$this->canDelete = false;
		return $this;
	}

	final public function unsetExport(){
		$this->canExport = false;
		return $this;
	}

	final public function unsetRead(){
		$this->canRead = false;
		return $this;
	}

	final public function unsetPrint(){
		$this->canPrint = false;
		return $this;
	}

	final public function showCreatedAt(){
		$this->showCreatedAt = true;
		return $this;
	}

	final public function showUpdatedAt(){
		$this->showUpdatedAt = true;
		return $this;
	}

	final public function getCreatedAt(){
		return $this->showCreatedAt;
	}

	final public function getUpdatedAt(){
		return $this->showUpdatedAt;
	}

	//Minimo funcionamiento del Crud
	final protected function baseIndex(){
		return $this->render();
	}

	final protected function baseCreate(){
		if( !$this->canAdd ){
			$message = "You dont have access for create a new regiter";
			return $this->notAccess($message);
		}

		return $this->render();
	}

	final protected function baseStore(){
		if( !$this->canAdd ){
			$message = "You dont have access for create a new regiter";
			return $this->notAccess($message);
		}

		if( $this->manager->save($this->repository->isPassword) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) ) .'.index' )
				->with('success_message','Register created succesfully');
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) ) .'.create' )
				->withInput()
				->withErrors($this->manager->getErrors());
		}
	}

	final protected function baseUpdate($id){
		if( !$this->canEdit ){
			$message = "You dont have access for update a regiter";
			return $this->notAccess($message);
		}

		$pk = $this->repository->getPrimaryKey();
		if( $this->manager->update($pk,$id,$this->repository->isPassword) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('success_message','Register updated succesfully');
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.edit', array( 'id' => $id ) )
				->withInput()
				->withErrors($this->manager->getErrors());
		}
	}

	final protected function baseShow($id){
		if( !$this->canRead ){
			$message = "You dont have access for view a regiter";
			return $this->notAccess($message);
		}
		return $this->render($id);
	}

	final protected function baseEdit($id){
		if( !$this->canEdit ){
			$message = "You dont have access for edit a regiter";
			return $this->notAccess($message);
		}
		return $this->render($id);
	}

	final protected function baseDestroy($id){
		if( !$this->canDelete ){
			$message = "You dont have access for delete a regiter";
			return $this->notAccess($message);
		}

		$pk = $this->repository->getPrimaryKey();
		if( $this->manager->delete($pk,$id) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('success_message','Register deleted succesfully');
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('error_message',$this->manager->getErrors());
		}
	}

	//Render Views
	final public function render($id = null){
		$callers = debug_backtrace();
		$parentFunction = $callers[1]['function'];

		$this->templateBuilder = new TemplateBuilder($this);
		return $this->templateBuilder->render($parentFunction,$id);
	}

	final private function notAccess($message){
		$this->templateBuilder = new TemplateBuilder($this);
		return $this->templateBuilder->deniedForAccess($message);
	}

}