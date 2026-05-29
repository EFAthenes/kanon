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
class KAdminLayoutHeader extends KComponent
{    
    function __construct()
    {        
        parent::__construct();
        $this->setName("AdminLayoutHeader");
        $this->setId(); 
        
        $menu= new KAdminLayoutHeaderMenu(ParamManager::getInstance()->app_name, KRoute::makeURL(RoutesItems::$HOME));
        
        
        $menuItem1=new KAdminLayoutHeaderMenuItem("", "fa fa-cogs");
        $menuSubItem1=new KAdminLayoutHeaderMenuItem("Utilisateur", "fa fa-user", KRoute::makeURL(RoutesItems::$EDIT_KAPP_USER));
        $menuSubItem2=new KAdminLayoutHeaderMenuItem("Se déconnecter", "fa fa-sign-out",KRoute::makeURL(RoutesItems::$DISCONNECTION));
        $menuItem1->addMenuItem($menuSubItem1);
        $menuItem1->addMenuItem($menuSubItem2);
        $menu->addItem($menuItem1);
        
        $menuItem2=new KAdminLayoutHeaderMenuItem("", "fa fa-home",KRoute::makeURL(RoutesItems::$HOME));
        $menu->addItem($menuItem2);
        
//        $menuItem=new KAdminLayoutHeaderMenuItem("test", "fa-brands fa-superpowers");
//        $menuSubItem=new KAdminLayoutHeaderMenuItem("test2", "fa-brands fa-superpowers",KRoute::makeURL(RoutesItems::$SHOW_ALL_TABLES));
//        $menuSubSubItem1=new KAdminLayoutHeaderMenuItem("test2.1", "fa-brands fa-superpowers");
//        $menuSubSubItem2=new KAdminLayoutHeaderMenuItem("test2.2", "fa-brands fa-superpowers");
//        $menuSubSubSubItem2=new KAdminLayoutHeaderMenuItem("test2.2.1", "fa-brands fa-superpowers");
//        $menuSubItem3=new KAdminLayoutHeaderMenuItem("test3", "fa-brands fa-twitter");
//        $menuSubSubItem2->addMenuItem($menuSubSubSubItem2);
//        $menuSubItem->addMenuItem($menuSubSubItem1);
//        $menuSubItem->addMenuItem($menuSubSubItem2);
//        $menuItem->addMenuItem($menuSubItem);
//        $menuItem->addMenuItem($menuSubItem3);        
//        $menu->addItem($menuItem);
        
        
        $menu->addItem(new KAdminLayoutHeaderLanguageSwitcher());
        
                
        $this->addComponent($menu);
        
    }  
    
}