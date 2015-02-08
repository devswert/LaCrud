<?php 

namespace DevSwert\LaCrud\Controller;

use DevSwert\LaCrud\Configuration;
use DevSwert\LaCrud\Data\Manager\LaCrudBaseManager;
use DevSwert\LaCrud\Data\Repository\LaCrudBaseRepository;

class LaCrudController extends LaCrudBaseController {

	function __construct(LaCrudBaseRepository $repository,LaCrudBaseManager $manager,Configuration $config){
		$this->repository = $repository;
		$this->manager = $manager;
		$this->configuration = $config;
	}

	public function index(){
		return $this->baseIndex();
	}

	public function create(){
		return $this->baseCreate();
	}

	public function store(){
		return $this->baseStore();
	}

	public function show($id){
		return $this->baseShow($id);
	}

	public function edit($id){
		return $this->baseEdit($id);
	}

	public function update($id){
		return $this->baseUpdate($id);
	}


	public function destroy($id){
		return $this->baseDestroy($id);
	}

}
