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
class DetailsComponent extends KComponent
{
    private bool $open=false;
    private ? KComponent $label;
    final public function __construct(string $name,?KComponent $label=null,bool $open=false)
    {        
        parent::__construct();
        $this->setName($name);
        $this->setClass();  
        $this->label=$label;
        $this->open=$open;
    }
    public function setStringLabel(string $string) : self
    {
        $this->label=new HTMLComponent($string);
        return $this;
    }
    public function setOpenOnInit(bool $open) : self
    {
        $this->open=$open;
        return $this;
    }    
    
    public function draw() : string
    {  
        $openString= $this->open ? 'open=""':''; 
        $labelString='';
        if(!is_null($this->label))
        {
            $labelString=$this->label->draw();
        }
        $html='  
<details  '.$openString.'>
    <summary>
   '.$labelString.'
     </summary>        

   '.parent::draw().'
</details> 
';      
        return $html;
    }  
    #[\Override]
    public static function testMe(): ?static
    {
        //string $name,?KComponent $label=null,bool $open=false
        $class=new static("DetailsComponent",new HTMLComponent("<b>Title</b>"),false);
        $class->addComponent(new HTMLComponent("inside"));
        return $class;
    }
}