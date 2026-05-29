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
class KAdminLayoutLeftMenuItem extends KComponent
{
    private string $label="";
    private string $fa_icon="";
    private string $url="";
    
    function __construct(string $label,string $fa_icon="",string $url="")
    {        
        parent::__construct();
        $this->setNone();
        
        $this->label=$label;
        $this->fa_icon=$fa_icon;
        $this->url=$url;
    }
    
    public function checkActiveLink() : string
    {
        $item=KRoute::getItemName();
        $searched_item=strstr($this->url,"kroute=",false);
//        KDebugger::getInstance()->dump("1=>".$searched_item);
        if($searched_item)
        {
            $searched_item=strtok($searched_item,"&");
            //KDebugger::getInstance()->dump("2=>".$searched_item);
            if(strcmp($searched_item,"kroute=".$item)== 0)
            {
                return "active";
            }
        }
        return "";
    }
    
    public function draw(): string
    {
        $html='';
        $list=$this->getListComponent();

        if($list->getSize())
        {
            $active="";
            $htmlInner="";
            /* @var $menuItem KAdminLayoutLeftMenuItem */
            foreach ($list as $menuItem)
            {

                $activeItem = $menuItem->checkActiveLink();
                if($activeItem!="" && $active=="")
                {
                    $active=$activeItem." is-expanded";
                }
                $htmlInner.='
        <li>
            <a class="treeview-item '.$activeItem.'" href="'.FormComponent::inputString($menuItem->getUrl()).'">
                <i class="icon '.FormComponent::inputString($menuItem->getFa_icon()).'"></i>
                '.FormComponent::inputString($menuItem->getLabel()).'
            </a>
        </li>
            ';            
                
            }
            
            $html.='
<li class="treeview '.$active.'">
    <a class="app-menu__item " href="'.FormComponent::inputString($this->url).'" data-toggle="treeview">
        <i class="app-menu__icon '.FormComponent::inputString($this->fa_icon).'"></i>
        <span class="app-menu__label">'. FormComponent::inputString($this->label).'</span>
        <i class="treeview-indicator fa fa-angle-right"></i>  
    </a>
    <ul class="treeview-menu">         
        '.$htmlInner.'
    </ul>
</li>            
';               
        }
        else
        {
            $active = $this->checkActiveLink();
            $html='
<li class="treeview">
    <a class="app-menu__item '.$active.'" href="'.FormComponent::inputString($this->url).'">
        <i class="app-menu__icon '.FormComponent::inputString($this->fa_icon).'"></i>
        <span class="app-menu__label">'.FormComponent::inputString($this->label).'</span>
    </a>
</li>            
';              
        }
        return $html;
    }
    
    public function addMenuItem(KAdminLayoutLeftMenuItem $component): void
    {
        parent::addComponent($component);
    }
    
    public function addComponent(\KComponent $component): KComponent
    {
        $this->addComment("KComponent NOT ADDED => ".$component->getName());
        // CANNOT ADD COMPONENTS
        return $this;
    }
    
    function getLabel() : string
    {
        return $this->label;
    }

    function getFa_icon() : ?string
    {
        return $this->fa_icon;
    }

    function getUrl() : ?string
    {
        return $this->url;
    }

    function setLabel(string $label) : void
    {
        $this->label = $label;
    }

    function setFa_icon(?string $fa_icon) : void
    {
        $this->fa_icon = $fa_icon;
    }

    function setUrl(?string $url) : void
    {
        $this->url = $url;
    }
}