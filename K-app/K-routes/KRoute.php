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
class KRoute
{
    private static ?KRoute $instance=null;
    private ?KRoutesItems $items=null;
    private ?string $itemName=null;
    private bool $is_routed_by_arg=false;
    private bool $is_routed=false;
    private bool $is_controlled=false;
    private ?ArrayList $list_middlewares=null;
    private string $controller_name="";
    public static string $KROUTE="kroute";
    public static ?string $overideKroute=null;
    public static bool $activateManageUsers=false;
    public static bool $activateKCMS=false;
    
    private int $BY_GET=1;
    private int $BY_POST=1;

    private function __construct(KRoutesItems $items)
    {
        $this->items=$items;
        $this->list_middlewares=new ArrayList();
    }

    protected static function getInstance(): KRoute
    {
        if(is_null(self::$instance))
        {
            echo "ITEMS FOR KRoutes == NULL";
            exit();
            //self::$instance=new KRoutes();
        }
        return self::$instance;
    }
       
    public static function getControllerName() : string
    {
        return self::getInstance()->controller_name;
    }
    
    public static function getKrouteIdentifier() : string
    {
        if(!is_null(self::$overideKroute))
        {
            //KDebugger::getInstance()->dump(self::$overideKroute,"getKrouteIdentifier");
            return self::$overideKroute;
        }
        return self::$KROUTE;
    }
    
    public static function overideKrouteIdentifier(string $identifier) : bool
    {
        if(strlen($identifier)>0&&self::isOnlyCharLower($identifier))
        {
            self::$overideKroute=$identifier;
            return true;
        }
        return false;
    }
    
    private static function isOnlyCharLower(string $input) : bool
    {
        // Use a regular expression to match the desired format with lowercase letters
         $pattern = '/^[a-z]+$/';
         //KDebugger::getInstance()->dump(preg_match($pattern, $input),"isOnlyCharLower");
        // Check if the input string matches the pattern
        if(preg_match($pattern, $input))
        {
            return true;
        }
        return false;
    }

    public static function setItems(KRoutesItems $items) : void
    {
        self::$instance=new KRoute($items);
    }

    public static function getItemName(): ?string
    {
        return self::getInstance()->itemName;
    }

    public static function get(string $route,?callable $function=null): KRoute
    {
        $kroutes=self::getInstance();
        if(!$kroutes->is_controlled)
        {
            //echo "=>".$kroutes->is_controlled." // ".$route."\n";
            if($kroutes->isItemPresentByGet($route))
            {
                if(is_callable($function))
                {
                    call_user_func($function);
                }
            }
        }
        //$this->is_routed_by_arg=true;
        return $kroutes;
    }

    public static function post(string $route,?string $function=null): KRoute
    {
        $kroutes=self::getInstance();
        if($kroutes->isItemPresentByPost($route))
        {
            if($function!=null&&is_callable($function))
            {
                $function();
            }
        }
        return $kroutes;
    }

    public static function hasARoute(?string $function=null): bool
    {
        $kroutes=self::getInstance();
        if($function!=null&&is_callable($function))//&&is_function_callable($function))
        {
            $function();
        }
        return $kroutes->getIs_routed_by_arg();

    }

    public static function isRouteOK(?string $function=null): bool
    {
        $kroutes=self::getInstance();
        if($function!=null&&is_callable($function))//&&is_function_callable($function))
        {
            $function();
        }
        return $kroutes->isRouted();
    }

    public static function isControllerOK(?string $function=null): bool
    {
        $kroutes=self::getInstance();
        if($function!=null&&is_callable($function))//&&is_function_callable($function))
        {
            $function();
        }
        return $kroutes->is_controlled;
    }

    public static function redirectURL(string $url): void
    {
        $url="Location: ".$url;
        header($url);
        exit();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return void
     */
    public static function redirectRoute(string $route,?array $getArray=null) : void
    {
        $kroutes=self::getInstance();
        if($kroutes->isItemPresent($route))
        {
            $url="Location: ".ParamManager::getInstance()->site_root."?".self::getKrouteIdentifier()."=".$route;
            if(isArrayAssoc($getArray))
            {
                foreach($getArray as $key=> $value)
                {
                    $url.="&".urlencode("".$key)."=".urlencode("".$value);
                }
            }
            if(!headers_sent())
            {
                header($url);
                exit();
            }
            else
            {
                echo "Headers Sent => ".$url;
            }
        }
    }
    
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return bool
     */
    public function redirectRouteIfControlled(string $route,?array $getArray=null): bool
    {
        if(!$this->is_routed)
        {
            return false;
        }

        if($this->is_controlled)
        {
            return false;
        }    
        
        self::redirectRoute($route,$getArray);
        return true;
    }
    

    public static function forceDownloadFile(string $path,bool $deleteFile=false,bool $closeSession=true): void
    {
        self::forceDownloadKFile(new KFile($path),$deleteFile,$closeSession);
    }

    public static function forceDownloadKFile(KFile $file,bool $deleteFile=false,bool $closeSession=true): void
    {
        if($file->exists()&&$file->isFile()&&!headers_sent())
        {
            if($closeSession)
            {
                session_write_close();
            }
            header("Content-disposition: attachment; filename=\"".$file->getName()."\"");
            header("Content-Type: application/force-download");
            header("Content-Transfer-Encoding: application/".$file->getExtension()."\n");
            header("Content-Length: ".$file->getSize());
            header("Pragma: no-cache");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
            header("Expires: 0");
            readfile($file->getPath());
            if($deleteFile)
            {
                $file->delete();
            }
            exit();
        }
    }

    public static function notFound() : void
    {
        if(!headers_sent())
        {
            header('HTTP/1.1 404 Not Found',true,404);
            echo "PAGE NOT FOUND // KROUTE NOT FOUND";
            exit();
        }
    }

    public function controller(KController $controller): bool
    {
        if(!$this->is_routed)
        {
            return false;
        }

        if($this->is_controlled)
        {
            return false;
        }

//        if($controller instanceof KController)
//        {
//            echo "Controller.execute =>".get_class($controller)."\n";
            $controller->init();
            if($controller->execute())
            {
                $component=$controller->drawComponents();
                KApp::getInstance()->getLayout()->addLayoutItemToMain($component);
                $this->is_controlled=true;
                $this->controller_name=get_class($controller);
            }
            $controller->after();
//        }

        $this->middlewareTerminate();
        return true;
    }

    public function controllerString(string $controllerString): bool
    {
        try
        {
            /* @var $controller KController */
            $controller=new $controllerString();
            if($controller instanceof KController)
            {
                return $this->controller($controller);
            }
        }
        catch(Exception $exception)
        {
            
        }
        return false;
    }

    public function controlAndDraw(KController $controller) : void
    {
        if($this->controller($controller))
        {
            KApp::getInstance()->drawLayout();
            exit();
        }
    }

    public function controlStringAndDraw(string $controllerString) : void
    {
        if($this->controllerString($controllerString))
        {
            KApp::getInstance()->drawLayout();
            exit();
        }
    }

    public function controllerMethod(KController $controller,string $methodName): bool
    {
        if(!$this->is_routed)
        {
            return false;
        }

//        if($controller instanceof KController)
//        {
            if(method_exists($controller,$methodName)&&$controller->$methodName())
            {
                $controller->drawComponents();
                $this->is_controlled=true;
            }
//        }

        $this->middlewareTerminate();
        return true;
    }

    private function middlewareTerminate() : bool
    {
        /* @var $middleware KMiddleware */
        if(!is_null($this->list_middlewares))
        {
            foreach($this->list_middlewares as $middleware)
            {
                if(!$middleware->terminateAll())
                {
                    return false;
                }
            }
        }
        return true;
    }

    public function middleware(?KMiddleware $middleware=null,?string $function=null,bool $launchFunctionBoolean=true): KRoute
    {
        if(!$this->is_routed)
        {
            return $this;
        }

        if($this->is_controlled)
        {
            return $this;
        }

        if(is_null($middleware))
        {
            return $this;
        }

        /* @var $middleware KMiddleware */
        $this->list_middlewares->add($middleware);
        $this->is_routed=$middleware->handleAll();
        //KDebugger::getInstance()->dump(SessionMemory::getInstance()->getUser());
        //echo "Routed == >".$this->is_routed." // <br />";
        if($this->is_routed&&$launchFunctionBoolean)
        {
            if($function!=null&&is_callable($function))
            {
                $function();
            }
        }
        return $this;
    }
    
    private function verifyItem(int $type,string $itemName) : bool
    {
        if($this->isItemPresent($itemName))
        {     
            $var=null;
            if($type==$this->BY_GET)
            {
                $var=$_GET;
            }
            else if($type==$this->BY_POST)
            {
                $var=$_POST;
            }
            
            if(isset($var)
                &&
                    ((isset($var[self::$KROUTE])&&$var[self::$KROUTE]==$itemName)
                    ||
                    (!is_null(self::$overideKroute)&&isset($var[self::$overideKroute])&&$var[self::$overideKroute]==$itemName)
                    ))
            {
                return true;
            }
        }
        return false;   
    }

    private function isItemPresentByGet(string $itemName) : bool
    {
        if($this->verifyItem($this->BY_GET,$itemName))
        {
            $this->itemName=$itemName;
            $this->is_routed_by_arg=true;
            $this->is_routed=true;
        }
        else
        {
            $this->is_routed=false;
        }
        return $this->is_routed;
    }

    private function isItemPresentByPost(string $itemName) : bool
    {
        if($this->verifyItem($this->BY_POST,$itemName))
        {
            $this->itemName=$itemName;
            $this->is_routed_by_arg=true;
            $this->is_routed=true;
        }
        else
        {
            $this->is_routed=false;
        }
        return $this->is_routed;
    }

    private function isItemPresent(string $itemName): bool
    {
        if(!is_null($this->items))
        {
            return $this->items->isItemPresent($itemName);
        }
        return false;
    }

    public function isRouted(): bool
    {
        return $this->is_routed;
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeFullURL(string $route,?array $getArray=null): string
    {
        $kurl=self::makeKURL($route,$getArray);
        $newKUrl=new KURL(ParamManager::getInstance()->site_root);
        $newKUrl->replaceArrays($kurl->getArgListNames(),$kurl->getArgListValues());       
        return $newKUrl->printURL();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */    
    public static function makeFullURLWithHost(string $route,?array $getArray=null): string
    {
        $kurl=self::makeKURL($route,$getArray);
        $newKUrl=new KURL(ParamManager::getInstance()->site_root);
        $newKUrl->replaceArrays($kurl->getArgListNames(),$kurl->getArgListValues());       
        return $newKUrl->printURLWithHost();
    }    
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeActionFullURL(string $route,?array $getArray=null): string
    {
        $kurl=self::makeActionKURL($route,$getArray);
        $newKUrl=new KURL(ParamManager::getInstance()->site_action);
        $newKUrl->replaceArrays($kurl->getArgListNames(),$kurl->getArgListValues());
        return $newKUrl->printURL();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeActionFullURLWithHost(string $route,?array $getArray=null): string
    {
        $kurl=self::makeActionKURL($route,$getArray);
        $newKUrl=new KURL(ParamManager::getInstance()->site_action);
        $newKUrl->replaceArrays($kurl->getArgListNames(),$kurl->getArgListValues());      
        return $newKUrl->printURLWithHost();
    }    
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeFullURLNoAmp(string $route,?array $getArray=null): string
    {
        $kurl=self::makeKURL($route,$getArray);
        return $kurl->printURLWithHostWithoutAmp();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeActionFullURLNoAmp(string $route,?array $getArray=null): string
    {
        $kurl=self::makeActionKURL($route,$getArray);
        //$newKUrl=new KURL();
        //$newKUrl->replaceArrays($kurl->getArgListNames(),$kurl->getArgListValues());
        return $kurl->printURLWithoutAmp();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeURL(string $route,?array $getArray=null): string
    {
        return self::makeKURL($route,$getArray)->printURL();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeURLNoAmp(string $route,?array $getArray=null): string
    {
        return self::makeKURL($route,$getArray)->printURLWithoutAmp();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeActionURLNoAmp(string $route,?array $getArray=null): string
    {
        return self::makeActionKURL($route,$getArray)->printURLWithoutAmp();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return KURL
     */
    public static function makeKURL(string $route,?array $getArray=null): KURL
    {
        $kurl=new KURL();
        $kroutes=self::getInstance();
        if($kroutes->isItemPresent($route))
        {
            $kurl=new KURL(ParamManager::getInstance()->site_root);
            $kurl->removeAllArgs();
            $kurl->add(self::getKrouteIdentifier(),$route);
            if(!is_null($getArray)&&isArrayAssoc($getArray))
            {
                foreach($getArray as $key=> $value)
                {
                    if(is_array($value))
                    {
                        foreach ($value as $val)
                        {
                            $kurl->add("".$key."[]","".$val);
                        }
                    }
                    else
                    {
                        $kurl->add("".$key,"".$value);
                    }
                }
            }
            return $kurl;
        }      
        return $kurl;
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return string
     */
    public static function makeActionURL(string $route,?array $getArray=null): string
    {
        return self::makeActionKURL($route,$getArray)->printURL();
    }
    /**
     * 
     * @param string $route
     * @param array<string,mixed>|null $getArray
     * @return KURL
     */
    public static function makeActionKURL(string $route,?array $getArray=null): KURL
    {
        $kurl=new KURL();
        $kroutes=self::getInstance();
        if($kroutes->isItemPresent($route))
        {
            $kurl=new KURL(ParamManager::getInstance()->site_action);
            $kurl->removeAllArgs();
            $kurl->add(self::getKrouteIdentifier(),$route);
            if(!is_null($getArray)&&isArrayAssoc($getArray))
            {
                foreach($getArray as $key=> $value)
                {
                    if(is_array($value))
                    {
                        foreach ($value as $val)
                        {
                            $kurl->add("".$key."[]","".$val);
                        }
                    }
                    else
                    {
                        $kurl->add("".$key,"".$value);
                    }
                }
            }
            return $kurl;
        }  
        return $kurl;
    }

    public function getIs_routed_by_arg() : bool
    {
        return $this->is_routed_by_arg;
    }

    public function setIs_routed_by_arg(bool $is_routed_by_arg) : void
    {
        $this->is_routed_by_arg=$is_routed_by_arg;
    }

    public function getIs_routed() : bool
    {
        return $this->is_routed;
    }

    public function setIs_routed(bool $is_routed) :void
    {
        $this->is_routed=$is_routed;
    }

    public static function forceRoute(?callable $function=null): KRoute
    {
        $kroutes=self::getInstance();
        $kroutes->setIs_routed(true);
        if(is_callable($function))
        {
            call_user_func($function);
        }
        return $kroutes;
    }

    public static function launchDefaultRoute(
            ?KLayout $homeLayout=null,
            ?KController $homePageController=null,
            ?KLayout $connectionLayout=null,
            ?KController $connectionController=null,
            ?KMiddleware $accessMiddleware=null): void
    {
        
        $sql=Sql::getInstance();
        if(is_null($sql))
        {
            echo "SQL connection failed verify database//username//password!";
            exit;
        }

        if(is_null($homeLayout))
        {
            $homeLayout=new KAdminLayout();
        }
        if(is_null($homePageController))
        {
            $homePageController=new KShowHomePage();
        }
        if(is_null($connectionLayout))
        {
            $connectionLayout=new KConnectionLayout();
        }
        if(is_null($connectionController))
        {
            $connectionController=new KConnectionController();
        }
        if(is_null($accessMiddleware))
        {
             $accessMiddleware=new UserIsGroupOne();
        }

        KRoute::get(RoutesItems::$CSS_KACHE,function ()
        {
            KApp::getInstance()->setLayout(new CSSLayout());
        })->controller(new KPrintBufferCSS());

        KRoute::get(RoutesItems::$JS_KACHE,function ()
        {
            KApp::getInstance()->setLayout(new JSLayout());
        })->controller(new KPrintBufferJS());

        KRoute::get(RoutesItems::$FONT_KACHE,function ()
        {
            KApp::getInstance()->setLayout(new FontLayout());
        })->controller(new KPrintDefaultFont());

        KRoute::get(RoutesItems::$HOME,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller($homePageController);

        KRoute::get(RoutesItems::$CONNECTION,function () use ($connectionLayout)
        {
            KApp::getInstance()->setLayout($connectionLayout);
        })->middleware(new UserIsNotConnected())->controller($connectionController);

        KRoute::get(RoutesItems::$CONNECTION,function () use ($connectionLayout)
        {
            KApp::getInstance()->setLayout($connectionLayout);
        })->middleware($accessMiddleware)->controller((new KRedirectController())->setRedirection(ParamManager::getInstance()->site_root));

        KRoute::get(RoutesItems::$DISCONNECTION)->middleware(new UserDisconnection())->controller(new KDisconnectionController());

        KRoute::get(RoutesItems::$FORGOT_PASSWORD,function ()
        {
            KApp::getInstance()->setLayout(new KForgotPasswordLayout());
        })->middleware(new UserIsNotConnected())->controller(new KForgotPasswordController());

        KRoute::get(RoutesItems::$EDIT_KAPP_USER,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware(new UserIsConnected())->controller(new EditKapp_Users());
        
        KRoute::get(RoutesItems::$SHOW_ALL_TABLES,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ShowAllTables());

        KRoute::get(RoutesItems::$SHOW_TABLE_CONTENT,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ShowTableContent());

        KRoute::get(RoutesItems::$SHOW_EDIT_TABLE_ELEMENT,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ShowEditTableElement());

        KRoute::get(RoutesItems::$SHOW_TABLE_EDIT_OPTIONS,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ShowEditTableOptions());

        KRoute::get(RoutesItems::$MODIFY_STRUCTURE_TABLE_ADD,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ModifyStructureTablesAddColumn());

        KRoute::get(RoutesItems::$MODIFY_STRUCTURE_TABLE_REMOVE,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ModifyStructureTablesRemoveColumn());

        KRoute::get(RoutesItems::$MODIFY_STRUCTURE_TABLE_UPLOAD_XLS,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ModifyStructureByUploadingXLS());

        KRoute::get(RoutesItems::$MODIFY_STRUCTURE_TABLE_RENAME,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ModifyStructureTablesRenameColumn());

        KRoute::get(RoutesItems::$MODIFY_TABLE_RENAME,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new RenameTable());

        KRoute::get(RoutesItems::$MODIFY_TABLE_DELETE,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new DeleteTable());

        KRoute::get(RoutesItems::$MODIFY_TABLE_COPY,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new CopyTable());

        KRoute::get(RoutesItems::$MODIFY_TABLE_EMPTY,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new EmptyTable());

        KRoute::get(RoutesItems::$MODIFY_TABLE_IMPORT_RECORDS,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new ImportRecordsByXLS());

        KRoute::get(RoutesItems::$MODIFY_TABLE_UPDATE_RECORDS_BY_ID,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new UpdateRecordsByXLS());

        KRoute::get(RoutesItems::$MODIFY_TABLE_DELETE_RECORDS_BY_ID,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new DeleteRecordsByXLS());

        KRoute::get(RoutesItems::$CREATE_TABLE_IN_DB,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new CreateTable());


        KRoute::get(RoutesItems::$KCHANGE_LANGUAGE_ITEM,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new KChangeLanguageItems());

        KRoute::get(RoutesItems::$PHP_INFO,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new KPhpInfoController());
        
        KRoute::get(RoutesItems::$PARAM_INFO,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new KParamManagerViewer());        
        
        KRoute::get(RoutesItems::$SYSTEM_INFO,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new KSystemInfoController()); 
        
        KRoute::get(RoutesItems::$LOGS_INFO,function () use ($homeLayout)
        {
            KApp::getInstance()->setLayout($homeLayout);
        })->middleware($accessMiddleware)->controller(new KLogsInfoController());  
               
        //KDebugger::getInstance()->dump(self::$activateManageUsers,"activateManageUsers");   
        if(self::$activateManageUsers)
        {
            KRoute::get(RoutesItems::$KSHOW_USERS,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KShowAllUsers()); 
            
            KRoute::get(RoutesItems::$KLIST_CONNECTIONS,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KShowAllUsersConnections()); 
            
            KRoute::get(RoutesItems::$KEDIT_USERS,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KEditTableUsers());    
            
            KRoute::get(RoutesItems::$KSHOW_GROUPS,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KShowAllGroups()); 
            
            KRoute::get(RoutesItems::$KEDIT_GROUPS,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KEditTableGroups());              
                  
            KRoute::get(RoutesItems::$KTEST_KCOMPONENT,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KtestKComponent());   
                      
      
        }
        
        if(self::$activateKCMS)
        {
            // CMS
            KRoute::get(RoutesItems::$KCMS_HOME,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KCMS_Home());   
            
            KRoute::get(RoutesItems::$KCMS_SHOW_ELEMENTS,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KCMS_Show_Elements());  
            
            KRoute::get(RoutesItems::$KCMS_EDIT_ELEMENT,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KCMS_Edit_Element());              
            
            // TEMPLATE
            KRoute::get(RoutesItems::$KTEMPLATE_PAGE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KPageTemplate()); 
            
            KRoute::get(RoutesItems::$KCREATE_TEMPLATE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KCreateTemplate());   

            KRoute::get(RoutesItems::$KCOPY_TEMPLATE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KCopyTemplate());     

            KRoute::get(RoutesItems::$KDEL_TEMPLATE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KDeleteTemplate());     

            KRoute::get(RoutesItems::$KRENAME_TEMPLATE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KRenameTemplate());     

            KRoute::get(RoutesItems::$KACTIVATE_TEMPLATE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KActivateTemplate());     

            KRoute::get(RoutesItems::$KMODIFY_TEMPLATE,function () use ($homeLayout)
            {
                KApp::getInstance()->setLayout($homeLayout);
            })->middleware($accessMiddleware)->controller(new KModifyTemplate());  
            
           
            
        }
        
        
    }
    
    public static function activateManageUsers() : void
    {
        self::$activateManageUsers=true;
    }
    public static function activateKCMS() : void
    {
        self::$activateKCMS=true;
    }
    
    public static function launchDefaultActionRoute(?KMiddleware $accessMiddleware=null): void
    {
        
        if(is_null($accessMiddleware))
        {
             $accessMiddleware=new UserIsGroupOne();
        }        
        
        KRoute::get(RoutesItems::$SHOW_EDIT_TABLE_ELEMENT_AJAX,function ()
        {
            KApp::getInstance()->setLayout(new JsonLayout());
        })->middleware($accessMiddleware)->controller(new ShowEditTableElementAjax());

        KRoute::get(RoutesItems::$EXPORT_TABLE_CONTENT_JSON,function ()
        {
            KApp::getInstance()->setLayout(new JsonLayout());
        })->middleware($accessMiddleware)->controller(new ExportTableContentJSON());

        KRoute::get(RoutesItems::$EXPORT_TABLE_CONTENT_XLS,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
        })->middleware($accessMiddleware)->controller(new ExportTableContentXLS());

        KRoute::get(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_STRUCTURE,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
        })->middleware($accessMiddleware)->controller(new ExportTableContentStructureXLS());

//        KRoute::get(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_STRUCTURE, function ()
//        {
//            KApp::getInstance()->setLayout(new TextPlainLayout());
//        })->middleware(new IsUserAdmin())->controller(new ExportTableContentStructureXLS()); 
//        
//        KRoute::get(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_STRUCTURE, function ()
//        {
//            KApp::getInstance()->setLayout(new TextPlainLayout());
//        })->middleware(new IsUserAdmin())->controller(new ExportTableContentStructureXLS()); 

        KRoute::get(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_COLS,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
        })->middleware($accessMiddleware)->controller(new ExportTableContentCol());

        KRoute::get(RoutesItems::$UPLOAD_FILES_ACTION,function ()
        {
            KApp::getInstance()->setLayout(new JsonLayout());
        })->middleware($accessMiddleware)->controller(new KUploadFilesToDirectory());

        KRoute::get(RoutesItems::$IMAGE_LOADER,function ()
        {
            KApp::getInstance()->setLayout(new SvgLayout());
        })->controller(new KLoaderImageSVG());

        KRoute::get(RoutesItems::$KCHANGE_LANGUAGE,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
        })->controller(new KChangeLanguageController());
        
        KRoute::get(RoutesItems::$LOGS_DOWNLOAD,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
        })->middleware($accessMiddleware)->controller(new KLogsInfoDownload());     
        
        KRoute::get(RoutesItems::$LOGS_CLEAN,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout);
        })->middleware($accessMiddleware)->controller(new KLogsInfoClean());   
        

        if(self::$activateManageUsers)
        {
            KRoute::get(RoutesItems::$KMANAGE_USERS,function ()
            {
                KApp::getInstance()->setLayout(new TextPlainLayout());
            })->middleware($accessMiddleware)->controller(new KManageUsersController()); 
            
            KRoute::get(RoutesItems::$KMANAGE_GROUPS,function ()
            {
                KApp::getInstance()->setLayout(new TextPlainLayout());
            })->middleware($accessMiddleware)->controller(new KManageGroupsController());  
        }
        
        if(self::$activateKCMS)
        {        
            KRoute::get(RoutesItems::$KEDIT_FILE_TEMPLATE,function () 
            {
                KApp::getInstance()->setLayout(new TextPlainLayout());
            })->middleware($accessMiddleware)->controller(new KEditFileTemplate()); 
            
            KRoute::get(RoutesItems::$KSAVE_FILE_TEMPLATE,function () 
            {
                KApp::getInstance()->setLayout(new TextPlainLayout());
            })->middleware($accessMiddleware)->controller(new KSaveFileTemplate());             
            
        }
    }

    public static function endDefaultRoute(?string $routeHome=null,?string $routeNotConnected=null): void
    {
        if(is_null($routeHome))
        {
            $routeHome=RoutesItems::$HOME;
        }
        if(is_null($routeNotConnected))
        {
            $routeNotConnected=RoutesItems::$CONNECTION;
        }


        if(!KRoute::hasARoute())
        {
            //echo "NOT ROUTED<br />";
            KRoute::redirectRoute($routeHome);
        }
        else if(!KRoute::isControllerOK())
        {
            //echo "NOT CONTROLLED<br />";
            KRoute::redirectRoute($routeNotConnected);
        }
        KApp::getInstance()->drawLayout();
    }

    public static function endDefaultActionRoute(): void
    {
        if(!KRoute::hasARoute())
        {
            KRoute::redirectRoute(RoutesItems::$HOME);
        }
        else if(!KRoute::isControllerOK())
        {
            KApp::getInstance()->setLayout(new HTML5Layout());
            KRoute::notFound();
        }
        KApp::getInstance()->drawLayout();
    }

    public static function stayActionConnected(): void
    {
        KRoute::get(RoutesItems::$STILL_CONNECTED,function ()
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
        })->middleware(new UserIsConnected())->controller(new KRemainConnectedController());
    }
    
    public static function debug() : void
    {
        $kroutes=self::getInstance();
        echo "DEBUG MODE Kroute <br />";
        echo "################# <br />";
        echo "is_routed =>".convertBoolToString($kroutes->is_routed)."<br />";
        echo "RouteOK =>".convertBoolToString($kroutes->isRouteOK())."<br />";        
        echo "is_controlled =>".convertBoolToString($kroutes->is_controlled)."<br />";
        echo "ControllerOK =>".convertBoolToString($kroutes->isControllerOK())."<br />";
        exit;
    }

}