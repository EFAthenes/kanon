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
class RowColComponent extends KComponent
{
    private ?ColComponent $col=null;
    function __construct(?KComponent $componentInside=null,string $size="-12")
    {        
        parent::__construct();
        $this->setName("row");
        $this->setClass(); 
        $this->col=new ColComponent($size);
        parent::addComponent($this->col);
        if(!is_null($componentInside))
        {
            $this->addComponent($componentInside);
        }
    }
    function addComponent(\KComponent $component): KComponent
    {
        $this->col->addComponent($component);
        return $this;
    }
    function addHtmlComponent(string $html): KComponent
    {
        $this->col->addComponent(new HTMLComponent($html));
        return $this;
    }    
}