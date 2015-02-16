<?php namespace DevSwert\LaCrud\Data;


class BaseTable {
	private $fields = array();

	public function __set($name, $value){
		$this->fields[$name] = $value;
	}

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