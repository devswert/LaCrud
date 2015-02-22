<?php namespace DevSwert\LaCrud;
	 
use Illuminate\Support\Facades\Facade;
 
class LaCrudFacade extends Facade {
 
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'lacrud'; }
 
}