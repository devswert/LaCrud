<?php namespace DevSwert\LaCrud;

trait Utils{

	/**
     * Throw a new basic exepction
     *
     * @param string The message to display
     * @return void
     */
    function throwException($message){
    	$trace = debug_backtrace();
		trigger_error(
            $message.
            ' on ' . $trace[0]['file'] .
            ' in line ' . $trace[0]['line'],
            E_USER_ERROR);
    }
} 