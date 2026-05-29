<?php
/**
 * Description of KExcelclass
 *
 */
require_once(__DIR__.'/lib/SqlFormatter.class.php');
class KsqlFormatter
{
    public function __construct()
    {
        
    }
    
    public static function show(mixed $query) : string
    {
        return SqlFormatter::format("".$query,true);
    }
    
    public static function highlight(mixed $query) : string
    {
        return SqlFormatter::highlight("".$query);
    }
    
    public static function format(mixed $query) : string
    {
        return SqlFormatter::format("".$query,false);
    }    
}