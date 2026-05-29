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
class CacheSqlParameter
{
    /**
     * 
     * @var array<int,string>
     */
    private array $lastModifications=[];

    function __construct()
    {

    }
    public function addLastDbModification(DbList $dbList):void
    {
        $this->addLastModification($dbList->getLastModifiedDateForTablename());
    }    
    public function addLastModification( string $lastModification):void
    {
        $this->lastModifications[]=$lastModification;
    }
    public function makeCacheKey(string $key) : string
    {
        $new_key="";
        foreach($this->lastModifications as $tempKey)
        {
            if(!empty($new_key))
            {
                $new_key.="_";
            }
            $new_key.=$tempKey;
        }
        return $key."_".$new_key;
    }
}