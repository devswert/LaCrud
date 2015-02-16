<?php namespace DevSwert\LaCrud\Data\Manager;

use DevSwert\LaCrud\Data\Entity\LaCrudBaseEntity;

class LaCrudManager extends LaCrudBaseManager{

    function __construct(LaCrudBaseEntity $entity){
        $this->entity = $entity;
    }

    public function doAfterDelete(){}
    public function doAfterInsert(){}
    public function doAfterUpdate(){}
    public function doAfterUpload(){}

    public function doBeforeDelete(){}
    public function doBeforeInsert(){}
    public function doBeforeUpdate(){}
    public function doBeforeUpload(){}

    public function skipDelete(){}
    public function skipInsert(){}
    public function skipUpdate(){}
    public function skipUpload(){}
}