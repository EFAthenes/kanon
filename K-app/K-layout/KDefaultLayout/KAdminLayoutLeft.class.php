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
class KAdminLayoutLeft extends KComponent
{
    function __construct()
    {        
        parent::__construct();
        $this->setName("KAdminLayoutLeft");
        $this->setId();   
        
        $menu=new KAdminLayoutLeftMenu();

        $first=new KAdminLayoutLeftMenuItem("menu_1", "fa-brands fa-bandcamp","");
        $second=new KAdminLayoutLeftMenuItem("menu_1_1", "fa-brands fa-quora",KRoute::makeURL(RoutesItems::$HOME));
        $first->addMenuItem($second);
        
        $menu->addComponent($first);
 //       $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_2", "fa fa-linode", KRoute::makeURL(RoutesItems::$LOUIS)));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_3", "fa-brands fa-bandcamp",KRoute::makeURL(RoutesItems::$SHOW_ALL_TABLES)));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_4", "fa-brands fa-bandcamp",""));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_5", "fa-brands fa-bandcamp",""));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_6", "fa-brands fa-bandcamp",""));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_7", "fa-brands fa-bandcamp",""));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_8", "fa-brands fa-bandcamp",""));
        $menu->addComponent(new KAdminLayoutLeftMenuItem("menu_9", "fa-brands fa-bandcamp",""));
        
        
        $menu->addComponent(new KAdminLayoutMenuItemSystem());       
        $menu->addComponent(new KAdminLayoutMenuItemCMS());
        $menu->addComponent(new KAdminLayoutMenuItemAllPrivileges());              
        
        $this->addComponent($menu);
    }
}
