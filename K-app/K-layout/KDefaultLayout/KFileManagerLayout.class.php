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
class KFileManagerLayout extends KLayout
{
    public function __construct()
    {
        parent::__construct();
    }

    public function initialize(): void
    {
        
        $this->addCSSFileToBuffer(__DIR__."/css/bootstrap.min.css");
        $this->addCSSFileToBuffer(__DIR__."/fontawesome5/css/all.css");
        $this->addCSSFileToBuffer(__DIR__."/css/jquery-ui.min.css");
        $this->addCSSFileToBuffer(__DIR__."/css/main-vali.css");
        $this->addCSSFileToBuffer(__DIR__."/css/k-own.css");
        $this->addCSSFileToBuffer(__DIR__."/css/dropzone.css");
        
        
        $this->addJsFileToBuffer(__DIR__."/js/jquery-3.4.0.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/popper.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/bootstrap.min.js");
        $this->addJsFileToBuffer(__DIR__."/js/jquery-ui.min.js");
        //$this->addJsFileToBuffer(__DIR__."/js/pace.js");
        $this->addJsFileToBuffer(__DIR__."/js/jquery.lazy.js");
        $this->addJsFileToBuffer(__DIR__."/js/sweetalert.min.js");    
        $this->addJsFileToBuffer(__DIR__."/js/dropzone.js");     
         //parent::initialize();
        $this->addLayoutItem(self::$CENTER,new DivIdComponent(self::$CENTER));
        $this->setComponentAsMain(self::$CENTER);
    }
    
    public function terminate() : void
    {

    }    
}