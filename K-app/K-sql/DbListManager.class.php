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
class DbListManager extends HashMapSingleton
{
    private static bool $cache=true;
    public function init(): void
    {
        
    }
    public static function setCache(bool $cache) : void
    {
        self::$cache=$cache;
    }
    public static function getCache() : bool
    {
        return self::$cache;
    }    
    public static function getDb(string $tableClassName,bool $reloadCache=false) : DbList
    {

        $the_instance=self::getInstance(false,false);//$sessionMemory);
        $dblist = $the_instance->get($tableClassName);
        if (is_null($dblist)) 
        {
            $dblist = new DbList($tableClassName);
            if(!$reloadCache)
            {
                $dblist->setCache(self::$cache);
            }
            else
            {
                $dblist->setCache(false);
            }
            $the_instance->put($tableClassName, $dblist);
        }
        else
        {
            $dblist->reset();
        }
        return $dblist;        
    }
}