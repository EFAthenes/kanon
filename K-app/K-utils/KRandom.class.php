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
class KRandom
{

    private function __construct()
    {
        
    }

//    private function __destruct()
//    {
//        
//    }
    
    public static function makeRandomUniquId() : string
    {
        srand(intval((float)microtime()*1000000));
        return rand()."". uniqid("",false);
    }    

    public static function makeRandom() : int
    {
        srand(intval((float)microtime()*1000000));
        $random_number=rand();
        return $random_number;
    }

    public static function makeRandomString(mixed $length=50,string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') : string
    {
        $pieces=[];
        $max=mb_strlen($keyspace,'8bit')-1;
        for($i=0; $i<$length; ++$i)
        {
            $pieces []=$keyspace[random_int(0,$max)];
        }
        return implode('',$pieces);
    }
    
}
