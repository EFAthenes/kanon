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
class KAdminLayout extends KLayout
{
    public static string $HEADER="header";
    public static string $LEFT="left";
    public static string $RIGHT="right";
    public static string $FOOTER="footer";
    public static string $CENTER="central";
    
    private ?KComponent $headerComponent=null;
    private ?KComponent $leftComponent=null;
    private ?KComponent $centerComponent =null;
    private ?KComponent $footerComponent=null;
    
    public static bool $USE_BOOTSTRAP_5=true;
    
    private bool $initComponent=true;
    private bool $initCssFiles=true;
    private bool $initJsFiles=true;
    
    public function __construct(string $title="")
    {
        if($title=="")
        {
            $title="KAdminLayout";
        }       
        parent::__construct($title);
    }  
    
    function getHeaderComponent() : KComponent
    {
        return $this->headerComponent;
    }

    function getLeftComponent() : KComponent
    {
        return $this->leftComponent;
    }

    function getCenterComponent() : KComponent
    {
        return $this->centerComponent;
    }

    function setHeaderComponent(KComponent $headerComponent) : void
    {
        $this->headerComponent = $headerComponent;
        $this->addLayoutItem(self::$HEADER,$this->headerComponent);
    }

    function setLeftComponent(KComponent $leftComponent) : void
    {
        $this->leftComponent = $leftComponent;
        $this->addLayoutItem(self::$LEFT,$this->leftComponent);
    }

    function setCenterComponent(KComponent $centerComponent) : void
    {
        $this->centerComponent = $centerComponent;
        $this->addLayoutItem(self::$CENTER,$this->centerComponent);
    }
    
    function setFooterComponent(KComponent $footerComponent) : void
    {
        $this->footerComponent = $footerComponent;
        $this->addLayoutItem(self::$FOOTER,$this->footerComponent);
    }
    
    public function setInitComponent(bool $initialisation) : void
    {
        $this->initComponent=$initialisation;
    }
    public function setInitCssFiles(bool $initialisation) : void
    {
        $this->initCssFiles=$initialisation;
    }     
    public function setInitJsFiles(bool $initialisation) : void
    {
        $this->initJsFiles=$initialisation;
    }  
    
    public function getKAdminLayoutDir() : string
    {
        return __DIR__;
    }
    
    public function includeCssFiles(): void
    {
        if(self::$USE_BOOTSTRAP_5)
        {
            $this->addCSSFileToBuffer(__DIR__."/bootstrap/bootstrap5/css/bootstrap.min.css");
        }
        else
        {
            $this->addCSSFileToBuffer(__DIR__."/css/bootstrap.min.css");
        }
//        $this->addCSSFileToBuffer(__DIR__."/fontawesome6/css/all.css");
//        $this->addCSSFileToBuffer(__DIR__."/css/jquery-ui.min.css");
//        $this->addCSSFileToBuffer(__DIR__."/css/main-vali.css");
//        $this->addCSSFileToBuffer(__DIR__."/css/k-own.css");     
        
        $this->addCSSFileToBuffer(__DIR__."/css/bootstrapKapp.min.css");  
        $this->addCSSFileToBuffer(__DIR__."/fontawesome7/css/all.css");
        $this->addCSSFileToBuffer(__DIR__."/css/jquery-ui.min.css");
        $this->addCSSFileToBuffer(__DIR__."/css/adminlayout.css");
        $this->addCSSFileToBuffer(__DIR__."/css/sweetalert-bootstrap4.css");
    }
    
    public function includeJsFiles(): void
    {
       
//        $this->addJsFileToBuffer(__DIR__."/js/jquery.min.js");
//        $this->addJsFileToBuffer(__DIR__."/js/jquery-migrate-4.0.2.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/jquery-3.7.1.min.js");
        
        $this->addJsFileToBuffer(__DIR__."/bootstrap/bootstrap5/js/bootstrap.bundle.js");
        
//        else
//        {
//            $this->addJsFileToBuffer(__DIR__."/js/popper.min.js");
//            $this->addJsFileToBuffer(__DIR__."/js/bootstrap.min.js");
//        }
        $this->addJsFileToBuffer(__DIR__."/js/jquery-ui.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/pace.js");
        $this->addJsFileToBuffer(__DIR__."/js/jquery.lazy.js");
        $this->addJsFileToBuffer(__DIR__."/js/sweetalert2.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/sweetalert.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/vali.js");
        $this->addJsFileToBuffer(__DIR__."/js/efa_utils.js");
    }
          
    public function initialize() : void
    {
        //$this->setTitle("Admin Layout");
        $this->addMetaProperty("og:site_name", ParamManager::getInstance()->site_title);
        //$this->addMetaProperty("louis", ParamManager::getInstance()->site_title);
        
        if($this->initCssFiles)
        {
            $this->includeCssFiles();
        }
        
        if($this->initJsFiles)
        {
            $this->includeJsFiles();
        }
        
             
        $this->addClassToBody("app sidebar-mini sidenav-toggled");    
          
        if($this->initComponent)
        {
            // INIT ALL Graphics CONTAINER COMPONENTS
            if(is_null($this->headerComponent))
            {
                $this->addLayoutItem(self::$HEADER,new KAdminLayoutHeader());
            }

            if(is_null($this->centerComponent))
            {
                $this->addLayoutItem(self::$CENTER,new KAdminLayoutCenter());
            }

            if(is_null($this->leftComponent))
            {
                $this->addLayoutItem(self::$LEFT,new KAdminLayoutLeft());
            }

            $this->setComponentAsMain(self::$CENTER);
        
            if(StyleManager::getInstance()->isActivated())
            { 
                $css=' 
.pace .pace-progress
{
    background: '.StyleManager::getInstance()->colour_3.' ;
}

.app-header
{
    background-color: '.StyleManager::getInstance()->main_colour.';
}

.app-header__logo
{
    background-color: '.StyleManager::getInstance()->colour_2.';
}

.app-sidebar__toggle:focus, .app-sidebar__toggle:hover
{
    background-color: '.StyleManager::getInstance()->main_colour.';
}

.app-sidebar__toggle:hover
{
    background-color: '.StyleManager::getInstance()->colour_3.';
}

.app-sidebar__toggle ::selection
{
    background-color: '.StyleManager::getInstance()->main_colour.';
}

';
                $this->addCSSText($css);  
            }
        }
    }
    
    public function terminate() : void
    {
//        $comp= new PostGetComponent("debug");
//        $this->addComponent(AdminLayout::$FOOTER,$comp);
//        
//        KTimer::getInstance()->stop();
//        $comp2= new HTMLComponent(KTimer::getInstance()->toString()."<br />");
//        $this->addComponent(AdminLayout::$FOOTER,$comp2);    
        HistoryPage::getInstance()->update();
        $this->addLayoutItemToMain(new KConnectionRemainConnectedComponent()); 
    }
}