<?php
	namespace DevSwert\LaCrud\Facades;
	 
	use Illuminate\Support\Facades\Facade;
	 
	class LaCrud extends Facade {
	 
	    /**
	     * Get the registered name of the component.
	     *
	     * @return string
	     */
	    protected static function getFacadeAccessor() 
	    { 
	        return 'lacrud'; 
	    }
	 
	}