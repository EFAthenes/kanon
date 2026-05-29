<?php declare(strict_types=1);
class test_middleware extends KMiddleware
{
    function __construct() 
    {
        
    }
    function __destruct() 
    {
        
    }    
    public function handle() : bool
    {
        return true;
    }
    public function terminate() : bool
    {
        return true;
    }
}
