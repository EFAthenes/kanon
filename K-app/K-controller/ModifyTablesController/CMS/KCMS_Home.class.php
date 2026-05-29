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
class KCMS_Home extends KController
{
    /**
     * 
     * @var array<int,array<int,KComponent>>
     */
    private array $gridComp=[];
    public function execute(): bool
    {
        //$this->addString("KCMS_Home");
        KApp::getInstance()->getLayout()->setTitle(LanguageManager::_("KCMS_MANAGE_TITLE"));
        $title=new KTitleLayoutAdmin(LanguageManager::_("KCMS_MANAGE_TITLE"),"fa-solid fa-pencil");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        //<i class="fa-solid fa-pencil"></i>
        
        
        if(!class_exists('Kapp_Layout'))
        {
            $this->addComponent(
                    new KAlertComponent("Error","Table for CMS not initialized", KAlertComponent::$TYPE_ERROR)
            );
            return true;
        }
        
        $label_action="Modifier";
        

        /* @var $type KCMS_Type_Element */
        foreach(KCMS_Type_Manager::getInstance(true)as $type)
        {
            //KDebugger::_($type, $label_action);
            $widget=new WidgetComponent($type->label,$type->icon, WidgetComponent::$TYPE_SUCCESS);
            $widget->addComponent(new LinkKURLComponent(
                KRoute::makeKURL(RoutesItems::$KCMS_SHOW_ELEMENTS,
                        [AbstractTableController::$GET_PARAM_TABLENAME => $type->table]), $label_action));
            $this->addTypeElement($widget, $type->level);     
        }
           

        $this->drawGrid();
        return true;
    }
    
    
    private function addTypeElement(KComponent $comp,int $line) : void
    {
        if(!array_key_exists($line, $this->gridComp))
        {
            $this->gridComp[$line]=[];
        }
        $this->gridComp[$line][]=$comp;
    }
    
    private function drawGrid() : void
    {
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("KCMS_MANAGE_LABEL")),"-12");
        
        foreach ($this->gridComp as $comps)
        {
            $itemsNb=count($comps);
            foreach ($comps as $comp)
            {
                $size=intval(12/$itemsNb);
                $grid->addColComponent($comp,"-".$size);      
            }
            $grid->addColComponent(new HrComponent(),"-12");      
        }
        
        $this->addComponent($grid);
    }
    
}