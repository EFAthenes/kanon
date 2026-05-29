<?php
declare(strict_types=1);
mb_internal_encoding("UTF-8");
ini_set('memory_limit','8192M');
//ini_set('memory_limit', '4096M');
ini_set('display_errors',"1");
//error_reporting(1);
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/K-utils/includeUtils.php');
KTimer::getInstance()->start();
require_once(__ROOT__.'/K-container/includeContainer.php');
require_once(__ROOT__.'/K-sql/includeSQL.php');
require_once(__ROOT__.'/K-session/includeSession.php');
require_once(__ROOT__.'/K-manager/includeManager.php');


require_once(__ROOT__.'/K-cache/KCache.class.php');
require_once(__ROOT__.'/K-lib/K-geometry/KGeometry.class.php');
$use_cache=true;
if(defined('USE_CACHE'))
{
    $use_cache=USE_CACHE;
}

$require_cache=__ROOT__.'/K-cache/a_require_cache.php';
$file=new KFile($require_cache);
if($use_cache && $file->exists())
{
    require_once $file->getPath();
    //KDebugger::getInstance()->dump("CACHED USE");
}
else
{
    //echo "ko";
    $include=new KIncludeDirectory();   
    
    $include->setPath_name(__ROOT__.'/K-api/');
    $include->includeAllDirectory();    
    
    $include->setPath_name(__ROOT__.'/K-action/');
    $include->includeAllDirectory();
    
    $include->setPath_name(__ROOT__.'/K-component/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-abstract-object/KObject.class.php');
    $include->includeFile();

    $include->setPath_name(__ROOT__.'/K-abstract-object/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-cli/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-manager/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-internalLib/');
    $include->includeAllDirectory();   

    $include->setPath_name(__ROOT__.'/K-mail/KMail.class.php');
    $include->includeFile();

    $include->setPath_name(__ROOT__.'/K-routes/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-controller/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-layout/');
    $include->includeAllDirectory();

    $include->setPath_name(__ROOT__.'/K-app/');
    $include->includeAllDirectory();
    
    $include->setPath_name(__ROOT__.'/K-language/class/');
    $include->includeAllDirectory();
    
    if(!$file->insertStringInFile("<?php \n".$include->getOutput()))
    {
        echo "Problem with cache =>".$require_cache." || info : ".$file->analyse()."  ";
        if($file->getParentKFile()->isReadOnly())
        {
            echo "PATH ".$file->getParentKFile()->getPath()." is ReadOnly";
        }
        exit();
    }
}

//KTimer::getInstance()->stop();
//echo "INCLUDE TIME ==> ".KTimer::getInstance()->toString()."\n";
