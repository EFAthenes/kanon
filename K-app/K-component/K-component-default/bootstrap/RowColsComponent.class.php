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
class RowColsComponent extends KComponent
{
    private ?ColComponent $col=null;
    function __construct(?KComponent $componentInside=null)//,string $size="")
    {        
        parent::__construct();
        $this->setName("row");
        $this->setClass(); 
        if(!is_null($componentInside))
        {
            $this->addComponent($componentInside);//,$size);
        }
    }
    public function addColComponent(\KComponent $component,mixed $size="") : void
    {
        $the_size="".$size;
        $this->col=new ColComponent($the_size);
        $this->col->addComponent($component);
        parent::addComponent($this->col);
    }
    public function addComponent(\KComponent $component): KComponent
    {
        if(is_null($this->col))
        {
            $this->col=new ColComponent(ColComponent::FULL_LINE);
        }
        $this->col->addComponent($component);
        return $this;
    }
    public function addHtmlColComponent(string $HTML,mixed $size="") : void
    {
        $the_size="".$size;
        $this->col=new ColComponent($the_size);
        $this->col->addHtmlComponent($HTML);
        parent::addComponent($this->col);
    }    
}