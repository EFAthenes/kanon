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
class BreadCrumbsComponent extends KComponent
{
    private ?ArrayList $listItem=null;
    final public function __construct(string $name)
    {        
        parent::__construct();
        $this->setName($name);
        $this->setId();   
        $this->listItem=new ArrayList();
    }
    
    public function addItemString(string $item,string $link) : self
    {
        $this->addItemComponentString(new HTMLComponent($item),$link);
        return $this;
    }
       
    public function addItemKUrl(string $item,KURL $kurl) : self
    {
        $this->addItemComponentString(new HTMLComponent($item), $kurl->printURLWithoutAmp());
        return $this;
    }
    
    public function addItemComponentString(KComponent $item,string $link) : self
    {
        $this->listItem->add(new LinkComponent($link, $item->draw()));
        return $this;
    } 
    
    public function draw() : string
    {     
        /*
        $string="<a ";
        if($this->getClassName()!="")
        {
            $string.=" class=\"".$this->getClassName()."\" ";
        }
        if($this->getIdName()!="")
        {
            $string.=" id=\"".$this->getIdName()."\" ";
        }
        if($this->url!="")
        {
            $string.=' href="'.$this->url.'" ';
        }
        if($this->blank)
        {
            $string.=' target="_blank" ';
        }
        
        $string.=">";
        
        if($this->text!="")
        {
            $string.="".$this->text."";
        }
         * 
         */
        
        $string='
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
';
        $url=new KURL();
        /* @var $item LinkComponent */
        foreach($this->listItem as $item)
        {
            $active="";
            if($url->printURLWithoutAmp()==$item->getUrl())
            {
                $active=" active";
            }
            $string.='<li class="breadcrumb-item '.$active.'">'.$item->draw().'</li>';
        }
        
        $string.='
  </ol>
</nav>            
';
        return $string.parent::draw();
    }
    /*
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Library</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data</li>
  </ol>
</nav>
     */
    #[\Override]
    public static function testMe(): ?static
    {
        //string $name,?KComponent $label=null,bool $open=false
        $class=new static("BreadCrumbsComponent");
        $class->addItemString("1","#");
        $class->addItemString("2","#");
        $class->addItemString("3","#");
        $class->addItemString("4","#");
        return $class;
    }
}