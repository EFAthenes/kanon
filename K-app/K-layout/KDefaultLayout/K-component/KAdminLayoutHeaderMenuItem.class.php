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
class KAdminLayoutHeaderMenuItem extends KComponent
{
    private string $label="";
    private string $fa_icon="";
    private string $url="";
    private bool $isSubItem=false;
    private ?KComponent $component=null;
    
    function __construct(string $label,string $fa_icon="",string $url="")
    {        
        parent::__construct();
        $this->setNone();
        
        $this->label=$label;
        $this->fa_icon=$fa_icon;
        $this->url=$url;
    }
    
    public function replaceLabelByComponent(KComponent $component) : void
    {
        $this->component=$component;
    }
    
    public function checkActiveLink() : string
    {
        $item=KRoute::getItemName();
        
        if(string_contains("kroute=".$item, $this->url))
        {
            return "active";
        }
        return "";
    }
    
    public function draw(): string
    {
        $html='';
        $list=$this->getListComponent();

        if($list->getSize())
        {
            //$active="";
            $htmlInner="";
            /* @var $menuItem KAdminLayoutLeftMenuItem */
            foreach ($list as $menuItem)
            {
                $htmlInner.=$menuItem->draw();               
            }
            
            if(!$this->isSubItem)
            {
               if(!is_null($this->component))
               {
            $html.='
<li class="dropdown">
    <a class="app-nav__item" href="'.FormComponent::inputString($this->url).'" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
        '.$this->component->draw().'
    </a>  
     <ul class="dropdown-menu settings-menu dropdown-menu-left">       
        '.$htmlInner.'
    </ul>
</li>            
            ';      
               }
               else
               {
            $html.='
<li class="dropdown">
    <a class="app-nav__item" href="'.FormComponent::inputString($this->url).'" data-bs-toggle="dropdown" aria-label="Open Profile Menu">
    <i class="'.FormComponent::inputString($this->fa_icon).'"></i>
    '. FormComponent::inputString($this->label).'
    </a>  

     <ul class="dropdown-menu settings-menu dropdown-menu-left">       
        '.$htmlInner.'
    </ul>
</li>            
            ';                    
               }
            
           }      
           else
           {              
               if(!is_null($this->component))
               {
            $html.='
<li class="dropdown">
    '.$this->component->draw().'
</li>            
            ';                    
               }
               else
               {               
               
            $html.='
<li class="dropdown">
    <a class="dropdown-item" href="'.FormComponent::inputString($this->url).'" >
    <i class="'.FormComponent::inputString($this->fa_icon).'"></i>
    '. FormComponent::inputString($this->label).'
    </a>  
     <ul class="dropdown-menu-inner">       
        '.$htmlInner.'
    </ul>
</li>            
            ';    
               }
           }
        }
        else if(!$this->isSubItem)
        {
            
            $html='
<li class="dropdown">
    <a class="app-nav__item" href="'.FormComponent::inputString($this->url).'" >
    <i class="'.FormComponent::inputString($this->fa_icon).'"></i>
    '. FormComponent::inputString($this->label).'
    </a>  
</li>
';              
        }
        else
        {
               if(!is_null($this->component))
               {
            $html.='
<li class="dropdown">
    '.$this->component->draw().'
</li>            
            ';                    
               }
               else
               {             
            $html='
<li class="dropdown">
    <a class="dropdown-item" href="'.FormComponent::inputString($this->url).'" >
    <i class="'.FormComponent::inputString($this->fa_icon).'"></i>
    '. FormComponent::inputString($this->label).'
    </a>  
</li>
';             
            
               } 
        }
        return $html;
    }
    
    public function addMenuItem(KAdminLayoutHeaderMenuItem $component): void
    {
        $component->setIsSubItem(true);
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
    
    function getIsSubItem() : bool
    {
        return $this->isSubItem;
    }

    function setIsSubItem(bool $isSubItem) : void
    {
        $this->isSubItem = $isSubItem;
    }
}