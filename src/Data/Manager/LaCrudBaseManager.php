<?php namespace DevSwert\LaCrud\Data\Manager;

use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Exception\NotReadableException;

abstract class LaCrudBaseManager {

    /**
     * This save the content of Input::all 
     *
     * @var mixed
     */
    private $attributes;

    /**
     * An array that save the configuration of 
     * relations many to many of entity.
     *
     * @var array
     */
    private $manyRelations = array();

    /**
     * Array neesary for the process with manu relations
     *
     * @var array
     */
    private $configManyRelations = array();

    /**
     * Array that save the configuration of the fields in 
     * the entety of type 'file'.
     *
     * @var array
     */
    private $uploadFields = array();

    /**
     * An intance's of LaCrudBaseEntity
     *
     * @var LaCrudBaseEntity
     */
    protected $entity;

    /**
     * Array with errors for Laravel Sesions
     *
     * @var mixed
     */
    protected $errors;

    /**
     * Array with rules of validation for add or edit 
     * a register in the CRUD process
     *
     * @var array
     */
    public $rules = array();

    /**
     * Array with rules for create a register, this is
     * more important than $rules 
     *
     * @var array
     */
    public $rulesCreate = array();

    /**
     * Array with rules for edit a register, this is
     * more important than $rules 
     *
     * @var array
     */
    public $rulesEdit = array();

    /**
     * Array with the field's entity that doesn't edit
     * in the forms templates 'new and edit register'
     *
     * @var array
     */
    public $fieldsNotEdit = array();

    /**
     * Array with the name's fields of type text
     * but this load without texteditor.
     *
     * @var array
     */
    public $disabledTextEditor = array();

    /**
     * Methods in evaluation
     *
     */
    /*
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
    */

    /**
     * This contain the flow for update a register according to the $pk and $value
     * given in the parameters of function. If this process return false set the
     * attribute $errors and the function that called use the messages setters
     * in the attribute.
     *
     * @param $pk              The name of primary key on entity/table.
     * @param $value           The value or id to edit.
     * @param $encryptFields   An array with the names of field that have Hash.
     * @param $relations       An array with the configuration of Many Relations for the entity.
     * @param $uploads         An array with the configuration of fields type 'file'.
     * @return boolean
     */
    final public function update($pk,$value,$encryptFields,$relations,$uploads){
        $this->configManyRelations = $relations;
        $this->attributes = \Input::all();
        $this->uploadFields = $uploads;

        $this->filterInformation();
        if( $this->isValid() ){
            $register = $this->entity->where($pk,'=',$value)->first();

            try{
                $this->assignValues($encryptFields,$register);
                $register->table = $this->entity->table;
                $register->save();
                $this->entity = $register;
                $this->assignRelationsValues();
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

    /**
     * This contain the flow for save a new register from the form, in the case of fails 
     * the process return false, and set the $errors attribute.
     *
     * @param $encryptFields   An array with the names of field that have Hash.
     * @param $relations       An array with the configuration of Many Relations for the entity.
     * @param $uploads         An array with the configuration of fields type 'file'.
     * @return boolean
     */
    final public function save($encryptFields,$relations,$uploads){
        $this->configManyRelations = $relations;
        $this->attributes = \Input::all();
        $this->uploadFields = $uploads;

        $this->filterInformation();
        if( $this->isValid() ){
            $this->assignValues( $encryptFields );
            try{
                $this->entity->save();
                $this->assignRelationsValues();
                return true;
            }
            catch(Exception $e){
                $this->errors = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * This contain the flow for delete a register according the $pk and $value in the 
     * entity, in the case that has a relations depending of his, return to the list
     * template with the alert message of error. In the prox version this has a
     * union with Hard Delete method for delete in cascade.
     *
     * @param $pk              The name of primary key on entity/table.
     * @param $value           The value or id to edit.
     * @return boolean
     */
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

   /**
     * According to $key given, this methods resolve if the field is type
     * 'file' or 'image', in the case that filed's type is 'image' and
     * has a 'resizes' coniguration, do the crop to the images.
     *
     * @param $key       The field's name of entity to update
     * @return string    The real name of file upload.
     */
    final public function upload($key){
        //Working with Images files
        if( is_array($this->uploadFields[$key]) && array_key_exists('isImage', $this->uploadFields[$key]) && $this->uploadFields[$key]['isImage'] ){
            try {
                //Moving the image files to storage config in the app
                $localStorage = \Config::get('filesystems');
                $tmpRoute = $localStorage['disks']['local']['root'].'/LaCrud/';
                $tmpFileName = str_random(10).'-'.$this->attributes[$key]->getClientOriginalName();
                $tmpImage = $this->attributes[$key]->move($tmpRoute,$tmpFileName);

                //Creating the object image
                $imageManager = new ImageManager(array('driver' => 'imagick'));
                $image = $imageManager->make( $tmpImage );

                //Rsolve the permission path on app and sizes
                if( array_key_exists('public',$this->uploadFields[$key]) || array_key_exists('private',$this->uploadFields[$key]) ){
                    if( array_key_exists('public',$this->uploadFields[$key]) ){
                        $tmpFileName = $this->moveUploadFile($key,'public',$image);
                    }
                    if( array_key_exists('private',$this->uploadFields[$key]) ){
                        $tmpFileName = $this->moveUploadFile($key,'private',$image);
                    }
                }
                else{
                    \Storage::disk('local')->delete( 'LaCrud/'.$tmpFileName );
                    throw new \Exception("The field's configuration not has a public or private path to move the file");
                }

            } catch (NotReadableException $e) {
                \Storage::disk('local')->delete( 'LaCrud/'.$tmpFileName );
                throw new NotReadableException($e->getMessage());
            }
        }
        //Working with others files
        else{
            if( is_array($this->uploadFields[$key]) ){
                if( array_key_exists('public',$this->uploadFields[$key]) || array_key_exists('private',$this->uploadFields[$key]) ){
                    if( array_key_exists('public',$this->uploadFields[$key]) ){
                        $tmpFileName = $this->moveUploadFile($key,'public');
                    }
                    if( array_key_exists('private',$this->uploadFields[$key]) ){
                        $tmpFileName = $this->moveUploadFile($key,'private');
                    }
                }
                else{
                    throw new \Exception("The field's configuration not has a public or private path to move the file");
                }
            }
            else{
                $tmpRoute = public_path().'/'.$this->uploadFields[$key];
                $tmpFileName = str_random(10).'-'.$this->attributes[$key]->getClientOriginalName();
                $this->attributes[$key]->move($tmpRoute,$tmpFileName);
            }
        }
        return $tmpFileName;
    }

    /**
     * According to the field this method move the temporal file loader by the user
     * to the path indicated in the controller's configuration.
     *
     * @param $key       The field's name of entity to evaluate
     * @param $type      A string that defined if is 'private' or 'public'
     * @param $image     This can be a Image Object or null by default
     * @return string    The real name of file upload.
     */
    final private function moveUploadFile($key,$type,$image = null){
        if( is_array($this->uploadFields[$key][$type]) ){
            if( array_key_exists('path', $this->uploadFields[$key][$type]) ){
                $base_path = ( $type == 'public' ) ? public_path() : base_path();
                $tmpRoute = $base_path.'/'.$this->uploadFields[$key][$type]['path'];

                if( get_class($image) == 'Intervention\Image\Image' ){
                    $tmpFileName = $image->basename;//substr($image->basename, 21);
                    
                    if( array_key_exists('resizes', $this->uploadFields[$key][$type]) && is_array($this->uploadFields[$key][$type]['resizes']) ){
                        foreach ($this->uploadFields[$key][$type]['resizes'] as $prefix => $dimension){
                            $backupImageOcject = clone $image;
                            $finalDimensions = [];
                            $nameOptionsCrop = ['ImageCropWidth','ImageCropHeight','ImageCropX','ImageCropY'];
                            $cantOfValuesNull = 0;
                            for ($i=0; $i < 4; $i++) { 
                                if( array_key_exists($i, $dimension) ){
                                    if(is_numeric($dimension[$i])){
                                        $finalDimensions[$nameOptionsCrop[$i]] = $dimension[$i];
                                    }
                                    else{
                                        throw new \Exception("The dimensions for resize the field ".$key." aren't valid");
                                    }
                                }
                                else{
                                    $finalDimensions[$nameOptionsCrop[$i]] = null;
                                    $cantOfValuesNull++;
                                }
                            }
                            extract($finalDimensions);
                            if($cantOfValuesNull > 0 && $cantOfValuesNull < 3){
                                $backupImageOcject->resize($ImageCropWidth,$ImageCropHeight)->save($tmpRoute.'/'.$prefix.$tmpFileName);
                            }
                            else{
                                $backupImageOcject->crop($ImageCropWidth,$ImageCropHeight,$ImageCropX,$ImageCropY)->save($tmpRoute.'/'.$prefix.$tmpFileName);
                            }
                        }
                    }

                    $image->save($tmpRoute.'/'.$tmpFileName);
                }
                else{
                    $tmpFileName = str_random(10).'-'.$this->attributes[$key]->getClientOriginalName();
                    $this->attributes[$key]->move($tmpRoute,$tmpFileName);
                }
            }
            else{
                throw new \Exception("The field's configuration not has path to move the file");
            }
        }
        else{
            $base_path = ( $type == 'public' ) ? public_path() : base_path();
            $tmpRoute = $base_path.'/'.$this->uploadFields[$key][$type];
        
            if( get_class($image) == 'Intervention\Image\Image' ){
                $tmpFileName = str_random(10).'-'.substr($image->basename, 21);
                $image->save($tmpRoute.'/'.$tmpFileName);
            }
            else{
                $tmpFileName = str_random(10).'-'.$this->attributes[$key]->getClientOriginalName();
                $this->attributes[$key]->move($tmpRoute,$tmpFileName);
            }
        }
        return $tmpFileName;
    }

    /**
     * Method avalaible in the next version.
     *
     */
    final private function forceDelete($pk,$value){
        //Verificar si tiene fake relations
        //Verificar si hay relaciones reales
        //Verificar si hay many relations
        return false;
    }

    /**
     * Assign the new values to entity given by reference.
     *
     * @param $encryptFields  The configuration of field that be type Hash
     * @param $entity         An ocject type LaCrudBaseEntity
     * @return void
     */
    final private function assignValues($encryptFields, &$entity = null){
        
        foreach ($this->attributes as $key => &$value){
            if( strlen($key) >= 11 && substr($key, 0, 13) == "manyRelations" ){
                $tmp = explode("#",$key);
                $this->manyRelations[$tmp[1]] = $value;
            }
            else{
                $datetimesFields = \Session::get('fields.datetime', null);
                $booleansFields = \Session::get('fields.booleans', array());

                if( in_array($key,$encryptFields) ){
                    if( is_null($entity) )
                        $value = \Hash::make($value);
                    else{
                        $value = ( $entity->{$key} != '' && $value == '' ) ? $entity->{$key} : \Hash::make($value);
                    }
                }
                if( in_array($key,$booleansFields) ){
                    $value = ( $value == 'on' || $value == 1 ) ? true : false;
                }
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

                if( array_key_exists($key, $this->uploadFields) ){
                    $value = $this->upload($key);
                }

                if( is_null($entity) )
                    $this->entity->{$key} = $value;
                else
                    $entity->{$key} = $value;
            }
        }
    }

    /**
     * Assign all data for the table involucrated on the many relation configurated.
     *
     * @return void
     */
    final private function assignRelationsValues(){
        $this->prepareConfigRelations();
        foreach ($this->configManyRelations as $key => $values){
            if( array_key_exists(strtolower($key), $this->configManyRelations) ){
                $local_key = ( array_key_exists('local_key', $this->configManyRelations[$key]) ) ? $this->configManyRelations[$key]['local_key'] : 'id';
                \DB::table( $this->configManyRelations[$key]['pivot']['table'] )->where( $this->configManyRelations[$key]['pivot']['local_key'] , '=', $this->entity->$local_key)->delete();

                if( array_key_exists($key, $this->manyRelations) ){
                    $inserts_values = array();
                    $cont = 0;
                    foreach ($this->manyRelations[$key] as $remote_key){
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
    }

    /**
     * Parse all keys of $configManyRelations to lower case.
     *
     * @return void
     */
    final private function prepareConfigRelations(){
        $newArray = [];
        foreach ($this->configManyRelations as $key => $value){
            $newArray[ strtolower($key) ] = $value;
        }
        $this->configManyRelations = $newArray;
    }

    /**
     * Delete the keys _token, _method and the fields $fieldsNotEdit in the case
     * that someone was send.
     *
     * @return void
     */
    final private function filterInformation(){
        foreach ($this->attributes as $key => $value){
            if( in_array($key,$this->fieldsNotEdit) || $key == '_token' || $key == '_method')
                unset($this->attributes[$key]);
        }
    }

    /**
     * Return the $errors attribute
     *
     * @return mixed
     */
    final public function getErrors(){
        return $this->errors;
    }

    /**
     * Validate if the information given is valid for save or
     * updated a register in the system, this method validate
     * if the class has a rulesEdit or rulesCreate before
     * of validate the information on class's $attributes
     *
     * @return boolean
     */
    final private function isValid(){
        $callers = debug_backtrace();
        $parentFunction = $callers[1]['function'];

        switch ($parentFunction) {
            case 'save':
                $rules = (is_array($this->rulesCreate) && count($this->rulesCreate) > 0 ) ? $this->rulesCreate : $this->rules;
                break;
            case 'update':
                $rules = (is_array($this->rulesEdit) && count($this->rulesEdit) > 0 ) ? $this->rulesCreate : $this->rules;
                break;
            default:
                $rules = $this->rules;
                break;
        }

        $validator = \Validator::make(
            $this->attributes,
            $rules
        );
        if($validator->fails()){
            $this->errors = $validator->messages();
            return false;
        }
        return true;
    }

}