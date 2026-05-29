<?php
declare(strict_types=1);
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
class KCMS_Show_Elements extends ShowTableContent
{
    public function init() : void
    {
        parent::init();

        if(!class_exists('Kapp_Layout'))
        {
            return;
        }
        
        $this->setUrl_modify_table(RoutesItems::$KCMS_EDIT_ELEMENT);
        $this->setUrl_back_button(RoutesItems::$KCMS_HOME);                
        $this->setShowEditTableButton(false);
        $this->setShow_tablename_title(false);
        $this->setButtonBar(false);
        if($this->checkTableName())
        {
            $type=KCMS_Type_Manager::getInstance()->getItemByTableName($this->getTablename());
            
            //KDebugger::_($type->id,"id");
            
            if($type->id==KCMS_Type_Manager::LAYOUT)
            {
                //$type->label
                $this->setAdminTitle("Modifications des Layouts ", $type->icon);
                $row= new RowColsComponent();
                //----
                $button1=new ButtonComponent("Ajouter des Items",ButtonComponent::$TYPE_INFO);
                $button1->setActionRouteItem(RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => Kapp_Layout_Item::$TABLE_NAME ,self::$GET_PARAM_NEW_ITEM=>"1"]);
                $row->addColComponent($button1,"-md-auto");
                //----
                //----
                $button2=new ButtonComponent("Trier les items",ButtonComponent::$TYPE_SECONDARY);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button2,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");
                //----
                $button3=new ButtonComponent("Éditer les propriétés",ButtonComponent::$TYPE_WARNING);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button3,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");       
                
                
                $this->addColComponent("Options",
                        $row);
                
                
                $columnsDef=[];
                $columnsDef[]=["name","Label"];
                $columnsDef[]=["description","Description"];
                $columnsDef[]=["id","Options"];
                
                $this->setColumnsDef($columnsDef);


            } 
            else if($type->id==KCMS_Type_Manager::LAYOUT_ITEM)
            {
                //$type->label
                $this->setAdminTitle("Modifications des Items Layouts ", $type->icon);
                
                $row= new RowColsComponent();
                
                //----
                $button1=new ButtonComponent("Ajouter des Items",ButtonComponent::$TYPE_INFO);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button1,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");
                //----
                //----
                $button2=new ButtonComponent("Trier les items",ButtonComponent::$TYPE_SECONDARY);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button2,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");
                //----
                $button3=new ButtonComponent("Éditer les propriétés",ButtonComponent::$TYPE_WARNING);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button3,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");                
                
                
                $this->addColComponent("Options",$row);
                
                
                $columnsDef=[];
                $columnsDef[]=["label","Label"];
                $columnsDef[]=["id_attribute","CSS : Id "];
                $columnsDef[]=["class_attribute","CSS : Class"];
                $columnsDef[]=["id","Options"];
                
                $this->setColumnsDef($columnsDef);
                
            }
            else if($type->id==KCMS_Type_Manager::LAYOUT_ITEM)
            {
                //$type->label
                $this->setAdminTitle("Modifications des Items Layouts ", $type->icon);
                
                $row= new RowColsComponent();
                
                //----
                $button1=new ButtonComponent("Ajouter des Items",ButtonComponent::$TYPE_INFO);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button1,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");
                //----
                //----
                $button2=new ButtonComponent("Trier les items",ButtonComponent::$TYPE_SECONDARY);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button2,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");
                //----
                $button3=new ButtonComponent("Éditer les propriétés",ButtonComponent::$TYPE_WARNING);
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button3,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");                
                
                
                $this->addColComponent("Options",$row);
                
                
                $columnsDef=[];
                $columnsDef[]=["label","Label"];
                $columnsDef[]=["id_attribute","CSS : Id "];
                $columnsDef[]=["class_attribute","CSS : Class"];
                $columnsDef[]=["id","Options"];
                
                $this->setColumnsDef($columnsDef);
                
            }            
            else if($type->id!=KCMS_Type_Manager::UNKNOWN_ITEM)
            {
                $this->setAdminTitle("Modifier entité : ".$type->label, $type->icon);
                

                $row= new RowColsComponent();
                $button=new ButtonComponent("Edition");
                $button->setActionKURL(
                        KRoute::makeKURL(
                            RoutesItems::$KCMS_EDIT_ELEMENT,
                            [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]));
                
                $button2=new ButtonComponent("Affecter Items",ButtonComponent::$TYPE_INFO);

                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    $button2,
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");
                $row->addColComponent(new LinkDataTableComponent(
                    KRoute::makeURL(
                        RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),
                    "",
                    LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")),"-md-auto");                
                
                $button3=new ButtonDataTableComponent("Test",ButtonComponent::$TYPE_WARNING);
                $button3->setActionRouteItem(RoutesItems::$KCMS_EDIT_ELEMENT,
                        [self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]);
                $button3->activateURLTag();
                $button3->activateLabelTag();
                
                $row->addColComponent(
                    $button3,"-md-auto");
                
                $this->addColComponent("Options",
                        $row);
                
                
                $columnsDef=[];
                $columnsDef[]=["name","Label"];
                $columnsDef[]=["id","Options"];
                
                $this->setColumnsDef($columnsDef);


            }
        }
    }
}