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
class SectionComponent extends KComponent
{
    private string $id_name="";
    public function __construct(string $id_name="",string $class_name="")
    {        
        parent::__construct();     
        $this->addClassName($class_name);
        $this->id_name=$id_name;
        $this->setNone();
    }
    public function draw() : string
    {       
        $string='<section class="'.$this->getClassName().'"';

        if($this->id_name!="")
        {
            $string.=" id=\"".$this->id_name."\" ";
        }
        
        return $string.">".parent::draw()."</section>";
    }
}