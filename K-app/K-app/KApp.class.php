<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 * 
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 * 
 */
declare(strict_types=1);
class KApp
{
    private static string $SESSION_NAME="";
//    private static $HOME_PAGE="";
//    private static $CONNECTION_PAGE="";  
    private static ?self $instance=null;
//    private string $kapp_folder="";
    private string $app_folder="";
    private string $app_directory_name="";
    private string $version_number="";
    
    /* @var $layout KLayout */
    private ?KLayout $layout=null;
    
    public static string $FOLDER_VIEW="view";
    public static string $FOLDER_TEMPLATE="template";
    public static string $FOLDER_WWW="www";
    public static string $FOLDER_UTILS="utils";
    public static string $FOLDER_TMP="tmp";
    public static string $FOLDER_CRON="cron";
    public static string $FOLDER_CONFIG="config";
    //public static $FOLDER_CACHE="cache";

    public static bool $initSession =true;

    public static function getInstance() : KApp
    {
        if(is_null(self::$instance))
        {
            self::$instance=new KApp("",true);
        }
        return self::$instance;
    }
    private static function setInstance(KApp $instance) : void
    {
        self::$instance=$instance;
    }
    
    public function getAppFolder() : string
    {
        //return ParamManager::getInstance()->app_folder;
        return $this->app_folder;
    }
    public function getAppDirectoryName() : string
    {
        return $this->app_directory_name;
    } 
    
    public function getAppConfigFolder() : string
    {
        //return ParamManager::getInstance()->app_folder;
        return $this->app_folder.KFile::separator().self::$FOLDER_CONFIG;
    }
    
    public function getAppVersion(): string
    {
        if(empty($this->version_number))
        {          
            $updateK=new KUpdateApp($this->app_directory_name);
            if($updateK->retrieveVersion())
            {
                $this->version_number=$updateK->getVersion();
            }
        }
        return $this->version_number;
    }
    
    public function includeKAppFile(string $path) : bool
    {
        $file=new KFile(__ROOT__.$path);
        if($file->exists())
        {
            require_once $file->getPath();
            return true;
        }
        return false;
    }
    
    
    public function __construct(string $appDirectory, bool $initSession=true,bool $initLayout=false,bool $useCache=true)
    {   
        $root_directory=new KFile(__ROOT__);
        $root_1=$root_directory->getParentKFile();
        $this->app_folder=$root_1->getPath().KFile::separator().$appDirectory; 
        ParamManager::getInstance()->app_folder=$this->app_folder;
        $this->app_directory_name=$appDirectory;       
        $include=new KIncludeDirectory(ParamManager::getInstance()->app_folder);
        $exceptDirectoryArray=[ self::$FOLDER_VIEW,
                                self::$FOLDER_TEMPLATE,
                                self::$FOLDER_WWW,
                                self::$FOLDER_UTILS,
                                self::$FOLDER_TMP,
                                self::$FOLDER_CRON,
                                KCache::$FOLDER_CACHE];

        $appFolder=new KFile(ParamManager::getInstance()->app_folder);
        if(!$appFolder->exists())
        {
            echo "App path doesn't exist or not accessible =>".$appFolder->getPath();
            exit;
        }
        
        //$use_cache=false;
        $require_cache=$this->makeKAppCachePath();
        $file=new KFile($require_cache);
        
        if($useCache && $file->exists())
        {           
            try
            {
                require_once $file->getPath();
            }
            catch (Exception $ex) 
            {
                 echo "Unable to load require configuration file.";
                 exit;
            }
            
        } 
        else
        {
            $dir_cache=$file->getParentKFile();
            if(!$dir_cache->exists())
            {
                $dir_cache->mkdir();
            }      
            $include->includeAllSubDirectories($exceptDirectoryArray);
            if(!$file->insertStringInFile("<?php \n".$include->getOutput()))
            {
                echo "Problem with cache =>".$require_cache." || info : ".$file->analyse()."  " ;
                exit();
            }   
        }
        
        self::$SESSION_NAME=ParamManager::getInstance()->site_session_name;        

        KRoute::setItems(new RoutesItems());       
        
        if($initSession)
        {
            $this->initSession();
        }
        
        if($initLayout)
        {
            $this->setLayout(new HTML5Layout());
        }
        
        self::setInstance($this);
    }
    public function makeKAppCachePath() : string
    {
        return ParamManager::getInstance()->app_folder.KFile::separator().KCache::$FOLDER_CACHE.KFile::separator().'a_require_cache.php';
    }
    public function __destruct()
    {
        
    }
    public function initSession() : void
    {
//        echo session_id();
//        exit;
//        
        
        if (session_status() !== PHP_SESSION_ACTIVE) 
        {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: text/html; charset=UTF-8");        
            ini_set('session.gc_maxlifetime',30*60);
            ini_set('session.gc_probability',1);
            ini_set('session.gc_divisor',1);
            session_name(self::$SESSION_NAME);
            session_start();
            //echo "SATRAT SESSION";
        }
    }
    
    function getLayout() : KLayout
    {
        if(is_null($this->layout))
        {
            $this->layout=new DefaultLayout();
        }
        return $this->layout;
    }

    function setLayout(KLayout $layout) : void
    {
        $this->layout=$layout;
        $this->layout->initializeLayout();
        $this->layout->initialize();
    }
    
    function drawLayout() : bool
    {
        if(is_null($this->layout))
        {
            echo "NO LAYOUT";
            return false;
        }
        else
        {
            //var_dump($this->layout);
            //echo $this->layout->getTitle();
            $this->layout->terminate();
            $this->layout->drawAll();
            return true;
        }
    }
    
    public function getCacheFolder() : string
    {
        return ParamManager::getInstance()->app_folder.KFile::separator().KCache::$FOLDER_CACHE;
    }
    
    public function getTemplateFolder() : string
    {
        return ParamManager::getInstance()->app_folder.KFile::separator().self::$FOLDER_TEMPLATE;
    }
    
    public function getRootKFile() : KFile
    {
        return new KFile(__ROOT__);
    }
}
