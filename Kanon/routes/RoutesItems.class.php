<?php declare(strict_types=1);
class RoutesItems extends KRoutesItems
{
    // ADD NEW ROUTES ITEMS
    //##################################
    public static string $HOME="home";
    public static string $CONNECTION="connection";
    public static string $DECONNECTION="deconnection";
    public static string $BASIC_VIEW="basic_view";
    public static string $TEST_JSON="test_json";
    
    public static string $COMPONENTS="components";
    public static string $DOCUMENTATION="documentation";
    public static string $PROJECTS="projects";
    
    // END NEW ROUTES ITEMS
    //##################################
    
    //--------------------------------------------------------------------------
    public function __construct() 
    {
        parent::__construct();
    }    
}
