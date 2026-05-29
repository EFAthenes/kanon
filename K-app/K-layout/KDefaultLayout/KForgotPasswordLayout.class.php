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
class KForgotPasswordLayout extends KAdminLayout
{
    public static string $FOOTER="footer";
    
    public function __construct()
    {
        parent::__construct("KForgotPasswordLayout");
    }    
    
    public function initialize() : void
    {
        $this->setTitle(LanguageManager::_("CONNEXION_FORGOT_PAGE_TITLE").ParamManager::getInstance()->site_title);
        $this->addMetaProperty("og:site_name","KApp");
        
        $this->includeCssFiles();
        $this->includeJsFiles();
           
        $this->addClassToBody("app sidebar-mini rtl");

        // INIT ALL Graphics CONTAINER COMPONENTS
        $this->addLayoutItem(self::$CENTER,new EmptyComponent());
        $this->setComponentAsMain(self::$CENTER);
    }
    
    public function terminate() : void
    {
//        if(ParamManager::getInstance()->debug)
//        {
//            $footer=new DivClassComponent(self::$FOOTER);
//            $appfooter=new DivClassComponent("app-footer");
//            $comp= new PostGetComponent("debug");
//            $appfooter->addComponent($comp);
//
//            $comp= new SessionComponent("debug");
//            $appfooter->addComponent($comp);
//
//            KTimer::getInstance()->stop();
//            $comp2= new HTMLComponent(KTimer::getInstance()->toString()."<br />");
//            $appfooter->addComponent($comp2);
//
//            $footer->addComponent($appfooter);
//            $this->addComponent(self::$CENTER,$footer);
//        }
    }

}