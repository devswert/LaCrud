<?php
namespace DevSwert\LaCrud\Data\Manager;

abstract class LaCrudBaseManager {

    private $attributes;
    protected $entity;
    protected $errors;
    public $rules = array();
    public $fieldsNotEdit = array();

    abstract public function doAfterDelete();
    abstract public function doAfterInsert();
    abstract public function doAfterUpdate();
    abstract public function doAfterUpload();

    abstract public function doBeforeDelete();
    abstract public function doBeforeInsert();
    abstract public function doBeforeUpdate();
    abstract public function doBeforeUpload();

    abstract public function skipDelete();
    abstract public function skipInsert();
    abstract public function skipUpdate();
    abstract public function skipUpload();

    final public function update($pk,$value,$encryptFields){
        $this->attributes = \Input::all();
        $this->filterInformation();
        if( $this->isValid() ){
            $register = $this->entity->where($pk,'=',$value)->first();

            try{
                foreach ($this->attributes as $key => $value){
                    if( in_array($key,$encryptFields) )
                        $value = \Hash::make($value);
                    $register->{$key} = $value;
                }
                $register->table = $this->entity->table;
                $register->save();
                return true;
            }
            catch(Exception $e){
                $this->errors = $e->getMessage();
                return false;
            }

            try{
                $this->entity->save();
                return true;
            }
            catch(Exception $e){
                $this->errors = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    final public function save($encryptFields){
        $this->attributes = \Input::all();
        $this->filterInformation();
        $this->assignValues( $encryptFields );
        if( $this->isValid() ){
            try{
                $this->entity->save();
                return true;
            }
            catch(Exception $e){
                $this->errors = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    final public function delete($pk,$value){
        $register = $this->entity->where($pk,'=',$value)->first();
        try{
            $register->table = $this->entity->table;
            $register->delete();
            return true;
        }
        catch(Exception $e){
            $this->errors = $e->getMessage();
            return false;
        }
    }

    final public function upload(){}

    //Functionals methods
    final private function assignValues($encryptFields){
        foreach ($this->attributes as $key => $value){
            if( in_array($key,$encryptFields) )
                $value = \Hash::make($value);
            $this->entity->{$key} = $value;
        }
    }

    final private function filterInformation(){
        foreach ($this->attributes as $key => $value){
            if( in_array($key,$this->fieldsNotEdit) || $key == '_token' || $key == '_method')
                unset($this->attributes[$key]);
        }
    }

    final public function getErrors(){
        return $this->errors;
    }

    final private function isValid(){
        $validator = \Validator::make(
            $this->attributes,
            $this->rules
        );
        if($validator->fails()){
            $this->errors = $validator->messages();
            return false;
        }
        return true;
    }

}