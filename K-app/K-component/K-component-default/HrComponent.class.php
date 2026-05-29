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
class HrComponent extends HTMLComponent
{
    public function __construct(mixed $size=1,string $color="#000000",string $bkcolor="")
    {
        $color_string="";
        if(isHexaColor($color))
        {
            $color_string.='color:'.$color.';background-color:'.$bkcolor;
            if(isHexaColor($bkcolor))
            {
                $color_string.='background-color:'.$bkcolor;
            }
        }
        $int_size=intval($size);
        $the_size=1;
        if($int_size>0)
        {
            $the_size=$int_size;
        }
        parent::__construct('<hr style="height:'.$the_size.'px;'.$color_string.'">');
    }

}