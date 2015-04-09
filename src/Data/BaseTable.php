<?php namespace DevSwert\LaCrud\Data;

class BaseTable {
    
    /**
     * An array that save an attributes for an entity
     *
     * @var array
     */
	private $fields = array();

    /**
     * Magic method that setting a attibutes of an entity
     *
     * @param $value   The nre value for the attribute
     * @param $name    Key or attribute's name
     * @return void
     */
	public function __set($name, $value){
		$this->fields[$name] = $value;
	}

    /**
     * Magic method for access a attribute of a entity, in 
     * the case of not exists throw a basic exception.
     *
     * @param $name    Key or attribute's name
     * @return mixed
     */
	public function __get($name){
		if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Property undefined by __get(): ' . $name .
            ' on ' . $trace[0]['file'] .
            ' in line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
	}
}