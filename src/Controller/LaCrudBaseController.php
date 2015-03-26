<?php namespace DevSwert\LaCrud\Controller;

use DevSwert\LaCrud\Theme\TemplateBuilder;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class LaCrudBaseController extends BaseController{

	use DispatchesCommands, ValidatesRequests;

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

	final public function canEdit(){
		return $this->canEdit;
	}

	final public function unsetAdd(){
		$this->canAdd = false;
		return $this;
	}

	final public function canAdd(){
		return $this->canAdd;
	}

	final public function unsetDelete(){
		$this->canDelete = false;
		return $this;
	}

	final public function canDelete(){
		return $this->canDelete;
	}

	final public function unsetExport(){
		$this->canExport = false;
		return $this;
	}

	final public function canExport(){
		return $this->canExport;
	}

	final public function unsetRead(){
		$this->canRead = false;
		return $this;
	}

	final public function canRead(){
		return $this->canRead;
	}

	final public function unsetPrint(){
		$this->canPrint = false;
		return $this;
	}

	final public function canPrint(){
		return $this->canPrint;
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
			$message = trans('lacrud::notifications.not_add');
			return $this->notAccess($message);
		}

		return $this->render();
	}

	final protected function baseStore(){
		if( !$this->canAdd ){
			$message = trans('lacrud::notifications.not_add');
			return $this->notAccess($message);
		}

		if( $this->manager->save($this->repository->isPassword,$this->repository->manyRelations) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) ) .'.index' )
				->with('success_message',trans('lacrud::notifications.success_add'));
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) ) .'.create' )
				->withInput()
				->withErrors($this->manager->getErrors());
		}
	}

	final protected function baseUpdate($id){
		if( !$this->canEdit ){
			$message = trans('lacrud::notifications.not_edit');
			return $this->notAccess($message);
		}

		$pk = $this->repository->getPrimaryKey();
		if( $this->manager->update($pk,$id,$this->repository->isPassword,$this->repository->manyRelations) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('success_message',trans('lacrud::notifications.success_edit'));
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.edit', array( 'id' => $id ) )
				->withInput()
				->withErrors($this->manager->getErrors());
		}
	}

	final protected function baseShow($id){
		if( !$this->canRead ){
			$message = trans('lacrud::notifications.not_read');
			return $this->notAccess($message);
		}
		return $this->render($id);
	}

	final protected function baseEdit($id){
		if( !$this->canEdit ){
			$message = trans('lacrud::notifications.not_edit');
			return $this->notAccess($message);
		}
		return $this->render($id);
	}

	final protected function baseDestroy($id){
		if( !$this->canDelete ){
			$message = trans('lacrud::notifications.not_delete');
			return $this->notAccess($message);
		}

		$pk = $this->repository->getPrimaryKey();
		if( $this->manager->delete($pk,$id) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('success_message',trans('lacrud::notifications.success_delete'));
		}
		else{
			$message = $this->manager->getErrors();
			$this->templateBuilder = new TemplateBuilder($this);
			$message = (\Session::has('error_code') && \Session::get('error_code') == 23000) ? $message." ".$this->templateBuilder->confirmHardDelete() : $message;
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('error_message',$message);
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