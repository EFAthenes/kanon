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
class ColComponent extends KComponent
{
    const FULL_LINE="-12";
    function __construct(mixed $size="")
    {        
        $sizeString="".$size;
        if(!empty($sizeString)&&!str_contains($sizeString,"-"))
        {
            $sizeString="-".$sizeString;
        }
        parent::__construct();
        $this->setName("col".$sizeString);
        $this->setClass(); 
    }
    public function changeColSize(string $size="") : void
    {
        $this->setName("col".$size);
    }
}