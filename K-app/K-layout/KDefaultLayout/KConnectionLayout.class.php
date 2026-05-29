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
class KConnectionLayout extends KLayout
{
    public static string $FOOTER="footer";
    
    public function __construct()
    {
        parent::__construct("KConnectionLayout");
    }    
    
    public function initialize() : void
    {
        $this->setTitle(LanguageManager::_("CONNEXION_PAGE_TITLE")." ".ParamManager::getInstance()->site_title);
        $this->addMetaProperty("og:site_name",ParamManager::getInstance()->site_title);
        
        $this->addCSSFileToBuffer(__DIR__."/css/bootstrapKapp.min.css");
        $this->addCSSFileToBuffer(__DIR__."/fontawesome6/css/all.css");
        $this->addCSSFileToBuffer(__DIR__."/css/jquery-ui.min.css");              
        
        $this->addJsFileToBuffer(__DIR__."/js/jquery-3.4.0.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/jquery-ui.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/bootstrap.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/popper.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/pace.js");
        $this->addJsFileToBuffer(__DIR__."/js/jquery.lazy.js");        
       
        $this->addClassToBody("app sidebar-mini rtl");

        // INIT ALL Graphics CONTAINER COMPONENTS
        $this->addLayoutItem(self::$CENTER,new KConnectionComponent());
        $this->setComponentAsMain(self::$CENTER);
    }
    
    public function terminate() : void
    {

    }
}