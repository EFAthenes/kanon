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
use Tracy\Debugger;
class KDebugger
{
    private static $is_init=false;
    private static ?self $debugger=null;
    private function __construct()
    {
        
    }
    
    private static function launchConstructor() : void
    {
        if(is_null(self::$debugger))
        {
            self::$debugger= new KDebugger();
        }        
    }
    
    public static function enable() : void
    {
        self::$is_init=false;
        self::launchConstructor();
        self::$debugger->init();
    }
    
//    public static function enableAjax() : void
//    {
//        self::launchConstructor();
////        self::$debugger->init();      
//        require_once __ROOT__.'/K-composer/vendor/tracy/tracy/src/tracy.php';  
//        Debugger::enable();
//    }
    
    public static function getInstance() : KDebugger
    {
        self::launchConstructor();
        return  self::$debugger;
    }
    
    public static function _(mixed $var,string $label) : void
    {
         self::$debugger->dump($var,$label);
    }    
    private static function makeLogPath(string $default="") : ?string
    {
        if(!empty($default))
        {
            $dirDefault=new KFile($default);
            if($dirDefault->exists()&&$dirDefault->isDirectory())
            {
                return $dirDefault->getPath();
            }
        }
        
        $dirRoot=new KFile(__ROOT__);
        $kdir=$dirRoot->getParentKFile();
        $dir=new KFile($kdir->getPath().KFile::separator()."K-log".KFile::separator());
        if($dir->exists()&&$dir->isDirectory())
        {
            return $dir->getPath();
        }
        return null;
    }
    
    public static function getLogPath() : string
    {
        return self::makeLogPath()."";
    }
    
    public function init() : void
    {   
        self::$is_init=false;
        if(!self::$is_init)
        {
            require_once __ROOT__.'/K-composer/vendor/tracy/tracy/src/tracy.php';       
            //require_once __ROOT__ . '/K-composer/vendor/autoload.php';
            
            $dir_path=self::makeLogPath(ParamManager::getInstance()->log_directory);
            
            if(ParamManager::getInstance()->debug)
            {
                
                //Debugger::setSessionStorage(new Tracy\NativeSession);
                Debugger::enable(Debugger::DEVELOPMENT,$dir_path,null);
                Debugger::$showBar = true;
                Debugger::$strictMode = true;
                Debugger::$showLocation = false;
                Debugger::$maxDepth=15;
                Debugger::$scream=true;
                Debugger::$maxLength = 2500;           
            }
            else
            {
                //Debugger::setSessionStorage(new Tracy\NativeSession);
                Debugger::enable(Debugger::DETECT,$dir_path,null);
                Debugger::$showBar = true;
                Debugger::$strictMode = true;
                Debugger::$showLocation = false;
                Debugger::$maxDepth=15;
                Debugger::$scream=false;
                Debugger::$maxLength = 2500;            
            }
            self::$is_init=true;  
        }
    }
    
    public function dump($var,string $label=null)
    {
        if(self::$is_init)
        {
            Debugger::barDump($var,$label);
        }
    }
    
    public function log(string $msg)
    {
        if(self::$is_init)
        {
            Debugger::log($msg);
        }
        
    }
    
    public function disableBar() : void
    { 
        if(class_exists("Debugger"))
        {
            Debugger::$showBar = false;
        }          
    }
    
    public function dispatch()
    {
        Debugger::dispatch();
    }
    
    public function fireLog(string $msg)
    {
        Debugger::fireLog($msg);
    }
}