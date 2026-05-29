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
class KNavMenuItemComponent extends KComponent
{
    public static int $TYPE_TITLE=1;
    public static int $TYPE_IMAGE=2;
    public static int  $TYPE_SUBMENU=2;
    private string $text="";
    private string $link= "";
    private string $image= "";
    private string $icon= "";
    private bool $separator=false;
    private string $header="";
    private string $onClick="";
    private ?ArrayList $listOfItems=null;
    
    function __construct(string $text,string $link="",string $image="",string $icon="")
    {
        parent::__construct();
        $this->setName("");
        $this->setNone();
        $this->listOfItems=new ArrayList();
        $this->text=$text;
        $this->link=$link;
        $this->image=$image;
        $this->icon=$icon;
        KTitleNavManager::getInstance()->compareUrl($this);
    }
    function getText() : string
    {
        return $this->text;
    }

    function getIcon() : string
    {
        return $this->icon;
    }
    
    function getImage() : string
    {
        return $this->image;
    }

        function getLink() : string
    {
        return $this->link;
    }

    function addSeparator() : void
    {
        $this->separator=true;
    }
    function addHeader(string $header) : void
    {
        $this->header=$header;
    }  
    function addOnClick(string $onClick) : void
    {
        $this->onClick=$onClick;
    }
    
    function addItem(KNavMenuItemComponent $item) : void
    {
        $this->listOfItems->add($item);
    }   
    public function draw(int $rank=1) : string
    {
        $html='<li>';
        $caret="";
        $dropDown="";
        // class="k-navigation-nav-parent"
        if($this->listOfItems->getSize()>0)
        {
            if($rank>=2)
            {
                $html='<li class="dropdown-submenu">';
                $caret='';
                $dropDown='';                
            }               
            else
            {
                $html='<li class="dropdown">';
                $caret=' <span class="caret"></span>';
                $dropDown='class="dropdown-toggle" data-toggle="dropdown"';
            }

        }
        //$html.="".$rank;
        if($rank>1&&$this->header!="")
        {
           $html='<li class="dropdown-header">Nav header</li>'.$html;
        }
        
        $html.='<a ';
        
        if($this->onClick!="")
        {
            $html.=' onclick="'.$this->onClick.'" ';
        }
        
        if($this->text!="")
        {
            if($this->link!="")
            {
                if($this->icon!="")
                {
                    $html.='href="'.$this->link.'" '.$dropDown.'><i class="'.$this->icon.'"></i>&nbsp;&nbsp;'.$this->text.$caret.'</a>';                     
                }
                else
                {
                    $html.='href="'.$this->link.'" '.$dropDown.'>'.$this->text.$caret.'</a>';                                   
                }
            }
            else
            {
                if($this->icon!="")
                {                
                    $html.='href="javascript:void(0);" '.$dropDown.'><i class="'.$this->icon.'"></i>&nbsp;&nbsp;'.$this->text.$caret.'</a>';                
                }
                else
                {
                    $html.='href="'.$this->link.'" '.$dropDown.'>'.$this->text.$caret.'</a>';                        
                }
            }
        }
        else if($this->image!="")
        {
            if($this->link!="")
            {
                $html.='href="'.$this->link.'" '.$dropDown.'><img src="'.$this->image.'"/>'.$caret.'</a>';                
            }
            else
            {
                $html.='href="javascript:void(0);" '.$dropDown.'><img src="'.$this->image.'"/>'.$caret.'</a>     ';                 
            }
        }
        
        if($this->listOfItems->getSize()>0)
        {
            $newRank=$rank+1;
            $html.='<ul class="dropdown-menu" role="menu">';
            for ($i = 0; $i < $this->listOfItems->getSize(); $i++)
            {
                $html.=$this->listOfItems->get($i)->draw($newRank);
            }
            $html.='</ul>';
        }
        $html.='</li>';
        if($this->separator)
        {
            $html.='<li class="divider"></li>';
        }
        $this->addHTML($html);
        return parent::draw();
    }    
}