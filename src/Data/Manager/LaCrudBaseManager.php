<?php namespace DevSwert\LaCrud\Data\Manager;

use Carbon\Carbon;

abstract class LaCrudBaseManager {

    private $attributes;
    private $manyRelations = array();
    private $configManyRelations = array();
    protected $entity;
    protected $errors;
    public $rules = array();
    public $fieldsNotEdit = array();
    public $disabledTextEditor = array();

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

    final public function update($pk,$value,$encryptFields,$relations){
        $this->configManyRelations = $relations;
        $this->attributes = \Input::all();
        $this->filterInformation();
        if( $this->isValid() ){
            $register = $this->entity->where($pk,'=',$value)->first();

            try{
                $this->assignValues($encryptFields,$register);

                $register->table = $this->entity->table;
                $register->save();
                if( count($this->manyRelations) > 0 ){
                    $this->entity = $register;
                    $this->assignRelationsValues();
                }
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

    final public function save($encryptFields,$relations){
        $this->configManyRelations = $relations;
        $this->attributes = \Input::all();

        $this->filterInformation();
        if( $this->isValid() ){
            $this->assignValues( $encryptFields );
            try{
                $this->entity->save();
                if( count($this->manyRelations) > 0 ){
                    $this->assignRelationsValues();
                }
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
        if(\Input::has('_forcedelete')){
            $id = \Input::get('_forcedelete');
            if( \Crypt::decrypt($id) == $value ){
                return $this->forceDelete($pk,$value);
            }
            else{
                $this->errors = trans('lacrud::notifications.invalid_key_forcedelete');
                return false;
            }
        }

        $register = $this->entity->where($pk,'=',$value)->first();
        try{
            $register->table = $this->entity->table;
            $register->delete();
            return true;
        }
        catch(\PDOException $e){
            if($e->getCode() == '23000'){
                $this->errors = trans('lacrud::notifications.error_dependency');
                 \Session::flash('error_code', 23000);
            }
            else{
                $this->errors = $e->getMessage();
            }
            return false;
        }
    }

    final public function upload(){}

    //Functionals methods
    final private function forceDelete($pk,$value){
        //Verificar si tiene fake relations
        //Verificar si hay relaciones reales
        //Verificar si hay many relations
        return false;
    }

    final private function assignValues($encryptFields, &$entity = null){
        
        foreach ($this->attributes as $key => &$value){
            if( strlen($key) >= 11 && substr($key, 0, 13) == "manyRelations" ){
                $tmp = explode("#",$key);
                $this->manyRelations[$tmp[1]] = $value;
            }
            else{
                $datetimesFields = \Session::get('fields.datetime', null);
                if( in_array($key,$encryptFields) )
                    $value = \Hash::make($value);
                if( is_array($datetimesFields) && array_key_exists($key, $datetimesFields) ){
                    
                    if($datetimesFields[$key] == 'date'){
                        $value = ($value == '') ? date('d-m-Y') : $value;
                        $baseDate = $value.' '.date('H:i:s');
                    }
                    else{
                        $baseTime = array_key_exists($key.'-time', $this->attributes) ? $this->attributes[$key.'-time'] : date('H:i:s');
                        
                        if( array_key_exists($key.'-time', $this->attributes) ) {
                            unset($this->attributes[$key.'-time']);
                        }

                        $value = ($value == '') ? date('d-m-Y') : $value;
                        $baseDate = $value.' '.$baseTime;
                    }

                    $carbon = Carbon::createFromFormat('d-m-Y H:i:s',$baseDate);
                    $value = ($datetimesFields[$key] == 'date') ? $carbon->toDateString() : $carbon->toDateTimeString();
                }
                if( is_null($entity) )
                    $this->entity->{$key} = $value;
                else
                    $entity->{$key} = $value;
            }
        }

    }

    final private function assignRelationsValues(){
        foreach ($this->manyRelations as $key => $values){
            if( array_key_exists($key, $this->configManyRelations) ){
                $local_key = ( array_key_exists('local_key', $this->configManyRelations[$key]) ) ? $this->configManyRelations[$key]['local_key'] : 'id';
                \DB::table( $this->configManyRelations[$key]['pivot']['table'] )->where( $this->configManyRelations[$key]['pivot']['local_key'] , '=', $this->entity->$local_key)->delete();
                $inserts_values = array();
                $cont = 0;
                foreach ($values as $remote_key){
                    $tmp = array(
                        $this->configManyRelations[$key]['pivot']['local_key'] => $this->entity->$local_key,
                        $this->configManyRelations[$key]['pivot']['remote_key'] => $remote_key
                    );
                    if( array_key_exists('order',  $this->configManyRelations[$key]['pivot']) )
                        $tmp[$this->configManyRelations[$key]['pivot']['order']] = $cont;
                    array_push($inserts_values, $tmp);
                    $cont++;
                }
                \DB::table( $this->configManyRelations[$key]['pivot']['table'] )->insert($inserts_values);
            }
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