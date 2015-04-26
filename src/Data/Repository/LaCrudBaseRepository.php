<?php namespace DevSwert\LaCrud\Data\Repository;

use Doctrine\DBAL\Types\Type;
use DevSwert\LaCrud\Data\BaseTable;
use DevSwert\LaCrud\Utils;

abstract class LaCrudBaseRepository {
    use Utils;

    /**
     * An instance's LaCrudBaseEntity.
     *
     * @var LaCrudBaseEntoty
     */
    public $entity;

    /**
     * An array with the fields that not display 
     * in the list and detail templates
     *
     * @var array
     */
    public $fieldsNotSee = array();

    /**
     * An array with alias of fields.
     *
     * @var array
     */
    public $displayAs = array();

    /**
     * An array with the fields encrypted.
     *
     * @var array
     */
    public $isEncrypted = array();

    /**
     * An array with fields required.
     *
     * @var array
     */
    public $requiredFields = array();

    /**
     * An array with the fields type text
     * that not use a texteditor template
     *
     * @var array
     */
    public $unsetTextEditor = array();

    /**
     * An array with the field that see in 
     * the select with foreigns keys.
     *
     * @var array
     */
    public $nameDisplayForeignsKeys = array();

    /**
     * An array with the configuration of fake
     * relations for the entity.
     *
     * @var array
     */
    public $fakeRelation = array();

    /**
     * An array with the configuration of many 
     * to many relations for the entity.
     *
     * @var array
     */
    public $manyRelations = array();

    /**
     * An array with the fileds type file.
     *
     * @var array
     */
    public $uploads = array();

    /**
     * An instance's QueryBuilder.
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * It defined if get all register of table
     *
     * @var boolean
     */
    protected $all = false;

    /**
     * An array with the fields type enum.
     *
     * @var array
     */
    protected $enumFields = array();

    /**
     * An array with the routes registers.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * An array that save the errors to display
     * for each field.
     *
     * @var string
     */
    private $error;

    /**
     * Alias to like method of Laravel
     *
     * @param $field   
     * @param $value
     * @return QueryBuilder
     */
    abstract public function like($field,$value);

    /**
     * Alias to where method of Laravel
     *
     * @param $field   
     * @param $operation   
     * @param $value
     * @return QueryBuilder
     */
    abstract public function where($field,$operation,$value);

    /**
     * Alias to limit function of Laravel
     *
     * @param $limit
     * @return QueryBuilder
     */
    abstract public function limit($limit);

    /**
     * Alias to orderBy function of Laravel
     *
     * @param $field
     * @param $order
     * @return QueryBuilder
     */
    abstract public function orderBy($field,$order);

    /**
     * Alias to orLike function of Laravel
     *
     * @param $field
     * @param $order
     * @return QueryBuilder
     */
    abstract public function orLike($field,$value);

    /**
     * Alias to orWhere function of Laravel
     *
     * @param $field
     * @param $operation
     * @param $order
     * @return QueryBuilder
     */
    abstract public function orWhere($field,$operation,$value);

    /**
     * Alias to get function of Laravel
     *
     * @return mixed
     */
    abstract public function get();

    final public function find($field,$value){
    	return $this->entity->where($field,'=',$value)->first();
    }

    final public function getColumns($table = null){
        $table = (is_null($table)) ? $this->entity->table : $table;

        $connection = \DB::connection()->getDoctrineSchemaManager($table);
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'enum');

        return $connection->listTableColumns($table);
    }

    final public function getPrimaryKey(){
        $connection = \DB::connection()->getDoctrineSchemaManager($this->entity->table);
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'enum');
        $table = $connection->listTableDetails($this->entity->table);

        if($table->hasPrimaryKey()){
            $field = $table->getPrimaryKey()->getColumns();
            return $field[0];
        }
        return false;
    }

    final public function getForeignKeys(){
        $connection = \DB::connection()->getDoctrineSchemaManager($this->entity->table);
        return $connection->listTableForeignKeys($this->entity->table);
    }

    final public function getHeaders($columns,$withDisplayAs = false){
        $response = array();
        foreach($columns as $column){
            if(!in_array($column->getName(),$this->fieldsNotSee)){
                $accept = false;

                if( array_key_exists($column->getName(),$this->displayAs)){
                    if(  !in_array($this->displayAs[$column->getName()], $this->fieldsNotSee) ){
                        $accept = true;
                    }
                }
                else{
                    $accept = true;
                }

                if($accept){
                    if($withDisplayAs){
                        $name = (array_key_exists($column->getName(),$this->displayAs)) ? $this->displayAs[$column->getName()] : $column->getName();
                        $name = ucfirst(str_replace('_',' ',$name ));
                    }
                    else
                        $name = $column->getName();
                    array_push($response,$name);
                }
            }
        }
        array_push($response, 'actions');
        return $response;
    }

    final public function getTypes($columns){
        $response = array();
        foreach($columns as $column) {
            array_push($response,$column->getName());
        }
        return $response;
    }

    final public function filterData($data){
        $collection = array();
        foreach($data as $row){
            $object = new BaseTable();
            foreach ($row['attributes'] as $key => $value) {
                if(!in_array($key, $this->fieldsNotSee)){
                    if( array_key_exists($key, $this->nameDisplayForeignsKeys) ){
                        $value = $this->searchAliasValue($key,$value);
                    }
                    if( array_key_exists($key, $this->fakeRelation) ){
                        $value = $this->searchFakeValue($key,$value);
                    }
                    if(array_key_exists($key, $this->displayAs) ){
                        if(!in_array($this->displayAs[$key], $this->fieldsNotSee)){
                            $object->{$key} = $value;
                        }
                    }
                    else{
                        $object->$key = $value;
                    }
                }
            }
            array_push($collection, $object);
        }
        return $collection;
    }

    final public function findIsForeignKey($column,$foreignsKeys){
        $response = $this->findNativeRealtion($column,$foreignsKeys);
        if(count($response) == 0){
            $response = $this->findFakeRelation($column);
        }
        return $response;
    }

    final private function findNativeRealtion($column,$foreignsKeys){
        $response = array();
        foreach ($foreignsKeys as $key){
            if($column->getName() === $key->getLocalColumns()[0]){
                $display = ( array_key_exists($column->getName(), $this->nameDisplayForeignsKeys) ) ? $this->nameDisplayForeignsKeys[$column->getName()] : null;
                $response = $this->getValuesForeignKey($key->getForeignTableName(),$key->getForeignColumns()[0],$display);
                break;
            }
        }
        return $response;
    }

    final private function findFakeRelation($column){
        if( array_key_exists($column->getName(), $this->fakeRelation) ){
            return $this->getValuesForeignKey(
                $this->fakeRelation[$column->getName()]['table'],
                $this->fakeRelation[$column->getName()]['field'],
                $this->fakeRelation[$column->getName()]['alias']
            );
        }
        else
            return array();
    }

    final private function getValuesForeignKey($table,$pk,$alias = null){
        $options = array();
        $query = \DB::table($table)->select($pk);
        if(!is_null($alias))
            $query->addSelect($alias);
        foreach ($query->get() as $row){
            if(!is_null($alias))
                $options[$row->{$alias}] = $row->{$pk};
            else
                array_push($options, $row->{$pk});
        }
        return $options;
    }

    final public function findManyRelations($local_pk = null){
        $collectionRelations = [];
        if( count($this->manyRelations) > 0 ){
            foreach ($this->manyRelations as $key => $relations){
                array_push($collectionRelations, $this->getOptionsForManyRelations($key,$relations,$local_pk) );
            }
        }
        return $collectionRelations;
    }

    final private function getOptionsForManyRelations($key,$relation = array(),$local_pk){
        if( $this->validateRelations($relation) ){
            $remoteKey = ( array_key_exists('key', $relation['remote']) ) ? $relation['remote']['key'] : 'id';
            $queryOptions = \DB::table($relation['remote']['table'])->select($remoteKey.' as key');
            $querySelected = \DB::table($relation['pivot']['table'])->select($relation['pivot']['remote_key'].' as key');

            if( array_key_exists('display',$relation['remote']) && $relation['remote']['display'] != "" ){
                $queryOptions->addSelect($relation['remote']['display'].' as display');
            }
            if(!is_null($local_pk)){
                $querySelected->where($relation['pivot']['local_key'],'=',$local_pk);
            }

            return array(
                'name' => strtolower( $key ),
                'name_display' => ucfirst( str_replace("_", " ", $key) ),
                'options'  => $this->addSelectedKey($queryOptions->get(),$querySelected->get())
            );
        }
        $this->throwException($this->error);
    }

    final private function addSelectedKey($options,$selected){
        $response = array();
        if(is_object($options))
            $options = $this->parseToArray($options);
        if(is_object($selected))
            $selected = $this->parseToArray($selected);

        foreach ($options as $value){
            $isSelected = $this->ifSelectedInManyRelation($value->key,$selected);
            array_push($response, array(
                'key' => $value->key,
                'display' => $value->display,
                'isSelected' => $isSelected,
                'select' => ($isSelected) ? 'selected' : ''
            ));
        }

        return $response;
    }

    final private function ifSelectedInManyRelation($pk,&$selected){
        $isSelected = false;
        foreach ($selected as $key => $values){
            $values = get_object_vars($values);
            if($values['key'] == $pk){
                unset($selected[$key]);
                $isSelected = true;
                break;
            }
        }
        return $isSelected;
    }

    final private function validateRelations($relation){
        if( count($relation) != 2 && count($relation) != 3){
            $this->error = '"Many relations" only support 2 or 3 options';
            return false;
        }

        $passed = true;
        //Validate local key
        if( array_key_exists('local_key',$relation) ){
            $columns = $this->getColumns();
            if(!array_key_exists($relation['local_key'], $columns) ){
                $this->error = 'The local primary key '.$relation['local_key']." doesn't exist on table ".$this->entity->table();
                $passed = false;
            }
        }

        //Validate Remote and Pivot Table
        if( $passed ){
            if( array_key_exists('pivot', $relation) && array_key_exists('remote', $relation) ){
                foreach ($relation as $type => $options){
                    if(strtolower($type) == 'pivot'){
                        try {
                            $columns = $this->getColumns($options['table']);
                            if( count($columns) == 0 ){
                                $this->error = 'The table '.$options['table']." don't exist";
                                $passed = false;
                                break;
                            }
                            if( !array_key_exists($options['local_key'] , $columns ) || !array_key_exists($options['remote_key'], $columns) ){
                                $this->error = "One of fields don't exist on table ".$options['table'];
                                $passed = false;
                                break;
                            }
                        }catch (Exception $e) {
                            $this->error = $e->getMessage();
                            $passed = false;
                            break;
                        }
                    }
                    if(strtolower($type) == 'remote'){
                        try {
                            $columns = $this->getColumns($options['table']);
                            if( !array_key_exists($options['display'] , $columns ) ){
                                $this->error = "One of fields don't exist on table ".$options['table'];
                                $passed = false;
                                break;
                            }
                            if( array_key_exists('key', $options) && !array_key_exists($options['key'] , $columns ) ){
                                $this->error = "The remote key ".$options['key']." don't exist on table ".$options['table'];
                                $passed = false;
                                break;
                            }
                        }catch (Exception $e) {
                            $this->error = $e->getMessage();
                            $passed = false;
                            break;
                        }
                    }
                }
            }
            else{
                $this->error = 'Lack one of options "pivot" or "remote" in the declaration';
                $passed = false;
            }
        }
        return $passed;
    }

    final private function parseToArray($object){
        $response = array();
        foreach ($object as $row){
            array_push($response, get_object_vars($row));
        }
        return $response;
    }

    final public function searchAliasValue($field,$value){
        $foreignsKeys = $this->getForeignKeys();
        foreach ($foreignsKeys as $key){
            if($field === $key->getLocalColumns()[0]){
                $data = \DB::table($key->getForeignTableName())->where($key->getForeignColumns()[0],'=',$value)->groupBy($key->getForeignColumns()[0])->first();
                break;
            }
        }
        if(isset($data)){
            return $data->{$this->nameDisplayForeignsKeys[$field]};
        }
        return $value;
    }

    final public function searchFakeValue($original,$value){
        $table = $this->fakeRelation[$original]['table'];
        $field = $this->fakeRelation[$original]['field'];
        $alias = ( array_key_exists('alias', $this->fakeRelation[$original]) ) ? $this->fakeRelation[$original]['alias'] : null;

        $data = \DB::table($table)->where($field,'=',$value)->groupBy($field)->first();
        if(isset($data)){
            return $data->$alias;
        }
        return $value;
    }

    //Enum utils
    final public function findEnumFields($column){
        switch (\DB::connection()->getConfig('driver')) {
			case 'pgsql':
				$query = "SELECT column_name FROM information_schema.columns WHERE table_name = '".$this->entity->table."'";
				$column_name = 'column_name';
				$reverse = true;
				break;
 
		case 'mysql':
			$query = 'SHOW COLUMNS FROM '.$this->entity->table.' WHERE Field = "'.$column->getName().'"';
			$column_name = 'Field';
			$reverse = false;
			break;
 
		case 'sqlsrv':
			$parts = explode('.', $this->entity->table);
			$num = (count($parts) - 1);
			$table = $parts[$num];
			$query = "SELECT column_name FROM ".\DB::connection()->getConfig('database').".INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".$table."'";
			$column_name = 'column_name';
			$reverse = false;
			break;
 
		default:
			$error = 'Database driver not supported: '.\DB::connection()->getConfig('driver');
			throw new Exception($error);
			break;
		}
 
		$columns = array(); 
		foreach(\DB::select($query) as $column){
			preg_match('/^enum\((.*)\)$/', $column->Type, $matches);

			if( is_array($matches) && count($matches) == 2 ){
				$columns = $this->getEnumValues(explode(',',$matches[1]));
			}
		}
 		if($reverse){
			$columns = array_reverse($columns);
		}
		return $columns;
    }

    final private function getEnumValues($elements){
        $enum = array();
        foreach( $elements as $value ){
            array_push($enum, trim(trim( $value, "'" )," "));
        }
        return $enum;
    }

    final public function getOptionsEnum($column){
    	if($column->getType()->getName() == 'enum'){
    		return $this->findEnumFields($column);
    	}
    	return array();
    }

    final public function routes($routes = array()){
        if( is_array($routes) && count($routes) > 0){
            $this->routes = $routes;
        }
        else{
            return $this->routes;
        }
    }
}
