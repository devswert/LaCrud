<?php namespace DevSwert\LaCrud;

trait Utils{

	//Throw exceptions
    function throwException($message){
    	$trace = debug_backtrace();
		trigger_error(
            $message.
            ' on ' . $trace[0]['file'] .
            ' in line ' . $trace[0]['line'],
            E_USER_ERROR);
		return null;
    }
} 