<?php namespace DevSwert\LaCrud\Controller;

use DevSwert\LaCrud\Theme\TemplateBuilder;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class LaCrudBaseController extends BaseController{

	use DispatchesCommands, ValidatesRequests;

	/**
	 * An instance's LaCrudBaseRepository.
	 *
	 * @var string
	 */
	public $repository;

	/**
	 * An instance's LaCrudBaseManager.
	 *
	 * @var string
	 */
	public $manager;

	/**
	 * An instance's Configuration.
	 *
	 * @var string
	 */
	public $configuration;

	/**
	 * An instance's TemplateBuilder.
	 *
	 * @var string
	 */
	private $templateBuilder;

	/**
	 * Define if can display the field created_at
	 *
	 * @var boolean
	 */
	private $showCreatedAt = false;

	/**
	 * Define if can display the field updated_at
	 *
	 * @var boolean
	 */
	private $showUpdatedAt = false;

	/**
	 * Define if can display the field deleted_at
	 *
	 * @var boolean
	 */
	private $showDeletedAt = false;

	/**
	 * Define if can edit a register
	 *
	 * @var boolean
	 */
	private $canEdit = true;

	/**
	 * Define if can create a register
	 *
	 * @var boolean
	 */
	private $canAdd = true;

	/**
	 * Define if can delete a register
	 *
	 * @var boolean
	 */
	private $canDelete = true;

	/**
	 * Define if can export a register
	 *
	 * @var boolean
	 */
	private $canExport = true;

	/**
	 * Define if can print a register
	 *
	 * @var boolean
	 */
	private $canPrint = true;

	/**
	 * Define if can read/show a register
	 *
	 * @var boolean
	 */
	private $canRead = true;

	/**
	 * Access method to list all register
	 *
	 * @return View Object   
	 */
	public function index(){
		return $this->render();
	}

	/**
	 * Access method to create a register
	 *
	 * @return View Object   
	 */
	public function create(){
		return $this->baseCreate();
	}

	/**
	 * Access method to store a register
	 *
	 * @return View Object   
	 */
	public function store(){
		return $this->baseStore();
	}

	/**
	 * Access method to show/read a register
	 *
	 * @return View Object   
	 */
	public function show($id){
		return $this->baseShow($id);
	}

	/**
	 * Access method to edit(form) a register
	 *
	 * @return View Object   
	 */
	public function edit($id){
		return $this->baseEdit($id);
	}

	/**
	 * Access method to update a register
	 *
	 * @return View Object   
	 */
	public function update($id){
		return $this->baseUpdate($id);
	}

	/**
	 * Access method to delete/destroy a register
	 *
	 * @return View Object   
	 */
	public function destroy($id){
		return $this->baseDestroy($id);
	}

	/**
	 * Diabled the option for edit regiters
	 *
	 * @return $this  LaCrudBaseController
	 */
	final public function unsetEdit(){
		$this->canEdit = false;
		return $this;
	}

	/**
	 * Getter for permission to edit
	 *
	 * @return boolean
	 */
	final public function canEdit(){
		return $this->canEdit;
	}

	/**
	 * Diabled the option for add regiters
	 *
	 * @return boolean
	 */
	final public function unsetAdd(){
		$this->canAdd = false;
		return $this;
	}

	/**
	 * Getter for permission to add
	 *
	 * @return boolean
	 */
	final public function canAdd(){
		return $this->canAdd;
	}

	/**
	 * Diabled the option for delete regiters
	 *
	 * @return boolean
	 */
	final public function unsetDelete(){
		$this->canDelete = false;
		return $this;
	}

	/**
	 * Getter for permission to delete
	 *
	 * @return boolean
	 */
	final public function canDelete(){
		return $this->canDelete;
	}

	/**
	 * Diabled the option for export regiters
	 *
	 * @return boolean
	 */
	final public function unsetExport(){
		$this->canExport = false;
		return $this;
	}

	/**
	 * Getter for permission to export
	 *
	 * @return boolean
	 */
	final public function canExport(){
		return $this->canExport;
	}

	/**
	 * Diabled the option for read regiters
	 *
	 * @return boolean
	 */
	final public function unsetRead(){
		$this->canRead = false;
		return $this;
	}

	/**
	 * Getter for permission to read
	 *
	 * @return boolean
	 */
	final public function canRead(){
		return $this->canRead;
	}

	/**
	 * Diabled the option for print regiters
	 *
	 * @return boolean
	 */
	final public function unsetPrint(){
		$this->canPrint = false;
		return $this;
	}

	/**
	 * Getter for permission to print
	 *
	 * @return boolean
	 */
	final public function canPrint(){
		return $this->canPrint;
	}

	/**
	 * Setter for show the field created_at
	 *
	 * @return $this  LaCrudBaseController
	 */
	final public function showCreatedAt(){
		$this->showCreatedAt = true;
		return $this;
	}

	/**
	 * Setter for show the field updated_at
	 *
	 * @return $this  LaCrudBaseController
	 */
	final public function showUpdatedAt(){
		$this->showUpdatedAt = true;
		return $this;
	}

	/**
	 * Setter for show the field deleted_at
	 *
	 * @return $this  LaCrudBaseController
	 */
	final public function showDeletedAt(){
		$this->showDeletedAt = true;
		return $this;
	}

	/**
	 * Getter of created_at
	 *
	 * @return boolean
	 */
	final public function getCreatedAt(){
		return $this->showCreatedAt;
	}

	/**
	 * Getter of updated_at
	 *
	 * @return boolean
	 */
	final public function getUpdatedAt(){
		return $this->showUpdatedAt;
	}

	/**
	 * Getter of deleted_at
	 *
	 * @return boolean
	 */
	final public function getDeletedAt(){
		return $this->showDeletedAt;
	}

	/**
	 * Return the list of register of an entity
	 *
	 * @return View
	 */
	final protected function baseIndex(){
		return $this->render();
	}

	/**
	 * Return a form for create an entity if
	 * has a permission for add.
	 *
	 * @return View
	 */
	final protected function baseCreate(){
		if( !$this->canAdd ){
			$message = trans('lacrud::notifications.not_add');
			return $this->notAccess($message);
		}

		return $this->render();
	}

	/**
	 * This method add new register, if dont'n permission for
	 * create a register return a View of 'fobidden access',
	 * if has a error validation return to same View
	 * create with the inputs and its errors
	 *
	 * @return View
	 */
	final protected function baseStore(){
		if( !$this->canAdd ){
			$message = trans('lacrud::notifications.not_add');
			return $this->notAccess($message);
		}

		if( $this->manager->save($this->repository->isEncrypted,$this->repository->manyRelations,$this->repository->uploads) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) ) .'.index' )
				->with('success_message',trans('lacrud::notifications.success_add'));
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) ) .'.create' )
				->withInput()
				->withErrors($this->manager->getErrors());
		}
	}

	/**
	 * This method update the register only if has access,
	 * if this has errors return to template edit form
	 * with the errors validaton and inputs values
	 *
	 * @return View
	 */
	final protected function baseUpdate($id){
		if( !$this->canEdit ){
			$message = trans('lacrud::notifications.not_edit');
			return $this->notAccess($message);
		}

		$pk = $this->repository->getPrimaryKey();
		if( $this->manager->update($pk,$id,$this->repository->isEncrypted,$this->repository->manyRelations,$this->repository->uploads) ){
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.index' )
				->with('success_message',trans('lacrud::notifications.success_edit'));
		}
		else{
			return \Redirect::route( 'lacrud.'. \Request::segment(count(explode('/', \Request::path())) - 1 ) .'.edit', array( 'id' => $id ) )
				->withInput()
				->withErrors($this->manager->getErrors());
		}
	}

	/**
	 * Return a View with the information of a 
	 * register indicated for Primary Key (id)
	 *
	 * @return View
	 */
	final protected function baseShow($id){
		if( !$this->canRead ){
			$message = trans('lacrud::notifications.not_read');
			return $this->notAccess($message);
		}
		return $this->render($id);
	}

	/**
	 * Return a View with the form for edit a register
	 * only if has access.
	 *
	 * @return View
	 */
	final protected function baseEdit($id){
		if( !$this->canEdit ){
			$message = trans('lacrud::notifications.not_edit');
			return $this->notAccess($message);
		}
		return $this->render($id);
	}

	/**
	 * This method receive a primary key value of entity 
	 * and delete the register only if has access, if
	 * the proccess is correct redirect to the 
	 * index list of entity.
	 *
	 * @return View
	 */
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

	/**
	 * This is the access point for all actions on the CRUD and
	 * dispatcher for FormBuilder for built the correct form.
	 *
	 * @return View
	 */
	final public function render($id = null){
		$callers = debug_backtrace();
		$parentFunction = $callers[1]['function'];

		$this->templateBuilder = new TemplateBuilder($this);
		return $this->templateBuilder->render($parentFunction,$id);
	}

	/**
	 * Return the when doesn't have access for the resource.
	 *
	 * @return View
	 */
	final private function notAccess($message){
		$this->templateBuilder = new TemplateBuilder($this);
		return $this->templateBuilder->deniedForAccess($message);
	}

}