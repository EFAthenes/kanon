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
/**
 * Description of HTMLComponent
 *
 * @author Mulot Louis
 */
class KNavMenuComponent extends KComponent
{
    private ?KNavMenuItemComponent $itemBrand=null;
    private ArrayList $listOfItemsLeft;
    private ArrayList $listOfItemsRight;
    private ArrayList $listOfItemsCenter;
    function __construct()
    {
        parent::__construct();
        $this->setNone();
        $this->listOfItemsLeft=new ArrayList();
        $this->listOfItemsRight=new ArrayList();
        $this->listOfItemsCenter=new ArrayList();

        $js=' 
function KNavMenuComponentTestNavigationType()
{
    //alert("1="+$(".navbar-collapse").height()+" // 2="+$(".navbar-header").height());
    if($(".navbar-collapse").height()<= $(".navbar-header").height())
    {
        return true;
    }
    return false;
}

$(function()
{            
    $(".dropdown-submenu > a").submenupicker();  
    
    $(".nav li.dropdown").hover(function() 
    {
        
        if(KNavMenuComponentTestNavigationType())
        {
            $(this).addClass("open");
        }
    }, 
    function() 
    {
        if(KNavMenuComponentTestNavigationType())
        {
            $(this).removeClass("open");
        }       
    });


    $(".nav li.dropdown-submenu").hover(function() 
    {
        if(KNavMenuComponentTestNavigationType())
        {
            $(this).addClass("open");
        }
    }, 
    function() 
    {
        if(KNavMenuComponentTestNavigationType())
        {
            $(this).removeClass("open");
        }  
    }); 
});
';   
        $this->addJSText($js);
    
    }

    function addItemBrand(KNavMenuItemComponent $item) : void 
    {
        $this->itemBrand=$item;
    }    
    function addItemLeft(KNavMenuItemComponent $item) : void 
    {
        $this->listOfItemsLeft->add($item);
    }
    function addItemRight(KNavMenuItemComponent $item) : void 
    {
        $this->listOfItemsRight->add($item);
    } 
    function addItemCenter(KNavMenuItemComponent $item) : void 
    {
        $this->listOfItemsCenter->add($item);
    }     
    public function draw() : string
    {
        $html='
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
';      
        
        if($this->itemBrand!=null)
        {
            $itemText="";
            if($this->itemBrand->getImage()!="")
            {
                $itemText.=' <img id="platform-brand" src="'.$this->itemBrand->getImage().'">';                  
            }
            if($this->itemBrand->getText()!="")
            {
                 $itemText.=$this->itemBrand->getText();     
            }    
            if($this->itemBrand->getLink()!="")
            {
       $itemText='         
        <a class="navbar-brand" href="'.$this->itemBrand->getLink().'"> '.$itemText.' </a>
';                       
            }            
            
            $html.=$itemText;      
        }
        
       $html.='         
    </div>
';
        if($this->listOfItemsLeft->getSize()>0||$this->listOfItemsRight->getSize()>0||$this->listOfItemsCenter->getSize()>0)
        {
            $html.='
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">               
';
            if($this->listOfItemsLeft->getSize()>0)
            {
                $html.='<ul class="nav navbar-nav">';
                for ($i = 0; $i < $this->listOfItemsLeft->getSize(); $i++)
                {
                    $html.=$this->listOfItemsLeft->get($i)->draw();
                }
                $html.='</ul>';                   
            } 
            
            if($this->listOfItemsCenter->getSize()>0)
            {
                for ($i = 0; $i < $this->listOfItemsCenter->getSize(); $i++)
                {
                    $html.=$this->listOfItemsCenter->get($i)->draw();
                }               
            } 
            
            if($this->listOfItemsRight->getSize()>0)
            {
                $html.='<ul class="nav navbar-nav navbar-right">';
                for ($i = 0; $i < $this->listOfItemsRight->getSize(); $i++)
                {
                    $html.=$this->listOfItemsRight->get($i)->draw();
                }
                $html.='</ul>';                   
            } 
            $html.='
    </div><!-- /.navbar-collapse -->          
';                
        }
        
            $html.='
  </div><!-- /.container-fluid -->
</nav>                
';        
        $this->addHTML($html);
        return parent::drawOnlyThisComponent(true);      
    }
}