<?php
define('USE_CACHE',false);
require_once(__DIR__.'/include.php');
ini_set('memory_limit','16048M');


$folderApp=KManageDb::getAppFolder();

$cli=new KCli();
$cli->setColors(KCli::$F_GREEN);
//echo print_r($cli);
$cli->printString("#############################################################".KCli::br());
$cli->printString("#                          K-PROJECT                        #".KCli::br());
$cli->printString("#############################################################".KCli::br());

$folder=new KFile($folderApp);


if($folderApp==""||!$folder->exists()|| !$folder->isDirectory() )
{
    $cli->printStringCustom(KCli::br()."Folder App not found => |".$folderApp."|".KCli::br(3),KCli::$F_RED); 
    exit();
}

$cli->printString("App \t\t=> ". $folderApp.KCli::br());

$updateDb= new KUpdateApp($folderApp);

if(!$updateDb->retrieveVersion())
{
    $cli->printStringCustom(KCli::br()."No version Found |".$folderApp."|".KCli::br(3),KCli::$F_RED); 
    exit();    
}

$cli->printString("Version \t=> ". $updateDb->getVersion().KCli::br());


if(!$updateDb->getAvailableVersions())
{
    $cli->printStringCustom(KCli::br()."Cannot access to the repository of versions".KCli::br(3),KCli::$F_RED); 
    exit();    
}

//$cli->printString("File \t=> ". print_r($updateDb->getArrayVersions(),true).KCli::br());


if($updateDb->isMyVersionUpdatable())
{
    
    $cli->printString(KCli::br(2)."New Version is available : ");
    $cli->printStringCustom($updateDb->getUpdateVersion().KCli::br(2),KCli::$B_CYAN);

    $cli->printString("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@".KCli::br());
    $cli->printString("@==> Make a backup of the Code and the Database before any update!@".KCli::br());
    $cli->printString("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@".KCli::br());

    $cli->printString(KCli::br()."Do you want to continue update (y) or no (n) ? : ");


    $response=$cli->waitPromptConfirmation(null,null,$cli);

    if($response===true)
    {
        $cli->printString(KCli::br()."Making version ..... ");
        $cli->printString(KCli::br()."------------- ");
        $cli->printString(KCli::br(2)."1) Creating temporay directory".KCli::br());
        if(!$updateDb->makeUpdateDir())
        {
            $cli->printStringCustom(KCli::br()."Cannot make temporary directory check the right in ".__DIR__.KCli::br(),KCli::$B_RED);
            exit();
        }
        else
        {
            
            $cli->printString(KCli::br(2)."Directorty created : ".$updateDb->getUpdateDir().KCli::br());

            $cli->printString(KCli::br(2)."2) Start download  : ".$updateDb->getUpdateVersionUrl().KCli::br());       
            if(!$updateDb->downloadUpdatePackage())
            {
                $cli->printStringCustom(KCli::br()."Cannot make temporary directory check the right in ".__DIR__.KCli::br(),KCli::$B_RED);      
            }   
            else
            {
                $cli->printString(KCli::br()."Download complete! ".KCli::br());
                $cli->printString(KCli::br(2)."3) Unziping Package : ".KCli::br());
                
                if(!$updateDb->unzipPackage())
                {
                    $cli->printStringCustom(KCli::br()."Cannot unzip ".KCli::br(),KCli::$B_RED);         
                } 
                else
                {
                    $cli->printString(KCli::br()."Unzip complete! ".KCli::br(2));
                    $cli->printString(KCli::br()."Replacing files! ".KCli::br());

                    if(!$updateDb->checkDirUpdates())
                    {
                        $cli->printStringCustom(KCli::br()."Error with the update package missing directories ".KCli::br(),KCli::$B_RED);
                    }          
                    else
                    {
                        if(!$updateDb->replaceSrc())
                        {
                            $cli->printStringCustom(KCli::br()."Error with the update package missing directories ".KCli::br(),KCli::$B_RED);
                        } 
                        // launch SQL && actions
                    }
                }
            }
        }
        
        $cli->printString(KCli::br(2)."X) Removing Temporary Files ... ".KCli::br());
        
        if(!$updateDb->deleteUpdateDir())
        {        
            $cli->printStringCustom(KCli::br(2)."X) Problem removing temporary files ".KCli::br(),KCli::$B_RED);
            exit();
        }
        
        

    }
    else if($response===false)
    {
        $cli->printStringCustom(KCli::br()."Aborting .... see you next time.".KCli::br(),KCli::$B_YELLOW);
        exit;
    }
    else
    {
        $cli->printStringCustom(KCli::br()."Wrong answers aborting.".KCli::br(),KCli::$B_RED);
    }
}


$cli->printString("@==> You are up to date!@".KCli::br());


// check current version

