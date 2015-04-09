<?php namespace DevSwert\LaCrud\Controller;

use DevSwert\LaCrud\Configuration;
use DevSwert\LaCrud\Data\Manager\LaCrudBaseManager;
use DevSwert\LaCrud\Data\Repository\LaCrudBaseRepository;

class LaCrudController extends LaCrudBaseController {

	/**
	 * Set the repository, manager and configuration attributes for
	 *  minimal functionality for every controller in LaCrud
	 *
	 * @return void
	 */
	function __construct(LaCrudBaseRepository $repository,LaCrudBaseManager $manager,Configuration $config){
		$this->repository = $repository;
		$this->manager = $manager;
		$this->configuration = $config;
	}

}
