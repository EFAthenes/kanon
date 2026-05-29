<?php
define('USE_CACHE',false);
require_once('./K-app/include.php');
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

$manageDb= new KManageDb($folderApp);
if (count($argv) <2)
{
    $cli->possibleCommands();
}
else
{
     
    $include=new KIncludeDirectory($folderApp."/config/");
    $include->includeAllDirectory();
    
    ParamManager::getInstance()->app_folder= __DIR__.KFile::separator().KManageDb::getAppFolderFromRoot();

    if($argv[1]=="create_project")
    {
        $directory=null;
        if (count($argv)>=3)
        {
            $directory=$argv[2];
        }
        $manageDb->createProject($cli,$directory);
    }
    else if($argv[1]=="set_project")
    {
        $directory=null;
        if (count($argv)>=3)
        {
            $directory=$argv[2];
        }
        $manageDb->setProject($cli,$directory);
    }
    else if($argv[1]=="make_db")
    {
        $db_name=null;
        if (count($argv)>=3)
        {
            $db_name=$argv[2];
        }
        $manageDb->makeDB($cli,$db_name);
    }
    else if($argv[1]=="check")
    {
        $manageDb->checkDb($cli);
    }
    else if($argv[1]=="build")
    {
        $manageDb->buildFiles($cli);
    }  
    else if($argv[1]=="add_columns")
    {
        $manageDb->addColumnToDb($cli);
    }  
    else if($argv[1]=="export_data_json")
    {
        $include=new KIncludeDirectory($folderApp.KFile::separator()."db".KFile::separator());
        $include->includeAllDirectory();
        
        $directory=null;
        $table=null;
        if (count($argv)>=3)
        {
            $directory=$argv[2];
        }
        if (count($argv)>=4)
        {
            $table=$argv[3];
        }
        $manageDb->exportDaTaToJSON($cli,$directory,$table);
    }
    else if($argv[1]=="export_model_json")
    {
        $include=new KIncludeDirectory($folderApp.KFile::separator()."db".KFile::separator());
        $include->includeAllDirectory();
        $directory=null;
        $table=null;
        if (count($argv)>=3)
        {
            $directory=$argv[2];
        }
        if (count($argv)>=4)
        {
            $table=$argv[3];
        }        
        $manageDb->exportModelToJSON($cli,$directory,$table);
    }  
    else if($argv[1]=="create_model_db")
    {       
        $include=new KIncludeDirectory($folderApp.KFile::separator()."db".KFile::separator());
        $include->includeAllDirectory();
        $database=null;
        if (count($argv)>=3)
        {
            $database=$argv[2];
        }        
        $manageDb->createModelInDb($cli,$database);
    }
    else if($argv[1]=="import_data_json")
    {       
        $include=new KIncludeDirectory($folderApp.KFile::separator()."db".KFile::separator());
        $include->includeAllDirectory();
        $path=null;
        if (count($argv)>=3)
        {
            $path=$argv[2];
        }        
        $manageDb->importDataFromJSON($cli,$path);
    } 
    else if($argv[1]=="import_model_json")
    {
        $include=new KIncludeDirectory($folderApp.KFile::separator()."db".KFile::separator());
        $include->includeAllDirectory();
        $path=null;
        if (count($argv)>=3)
        {
            $path=$argv[2];
        }
        $manageDb->importModelFromJSON($cli,$path);
    }
    else if($argv[1]=="initialize_kapp_tables")
    {
        $parentFolder=$folder->getParentKFile();
        
        $folderAppModel=new KFile($parentFolder->getPath().KFile::separator()."K-app".KFile::separator()."K-cli".KFile::separator()."Models");

        if($folderAppModel->exists()&&$folderAppModel->isDirectory())
        {
            $manageDb->importModelFromJSON($cli,$folderAppModel->getPath());           
        }
        else
        {
            $cli->printStringCustom(KCli::br()."MODEL PATH NOT FOUND => ".$folderAppModel->getPath().KCli::br(3),KCli::$F_RED); 
        }
    }    
    else if($argv[1]=="make_indexes_klink")
    {
        $manageDb->makeIndexesKlink($cli);
    }
    else if($argv[1]=="clean_cache")
    {
        if($manageDb->cleanCache($cli))
        {
            $cli->printStringCustom(KCli::br()."Cache directory is clean".KCli::br(3),KCli::$F_BLUE);
        }
        else
        {
            $cli->printStringCustom(KCli::br()."Cache directory not found => ".KCli::br(3),KCli::$F_RED); 
        }
    }
    else if($argv[1]=="test_code")
    {
        $start=1;
        if (count($argv)>2)
        {
            $start= intval($argv[2]);
            if($start<1)
            {
                $start=1;
            }
        }   
        $manageDb->testCode($cli,$start);
    }    
    else
    {
        $cli->printStringCustom(KCli::br()."Command not FOUND => ".$argv[1].KCli::br(3),KCli::$F_RED); 
        $cli->possibleCommands();    
    }
    
}
$manageDb->removeCaches();