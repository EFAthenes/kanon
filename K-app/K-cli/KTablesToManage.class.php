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
class KTablesToManage
{
    public string $name="";
    /**
     * 
     * @var array<int,string>
     */
    public array $fields=[];
    /**
     * 
     * @param string $name
     * @param array<int,string> $fields
     */
    function __construct(string $name, array $fields)
    {
        $this->name=$name;
        $this->fields=$fields;
    }
    function getName() : string
    {
        return $this->name;
    }

    /**
     * 
     * @return array<int,string>
     */
    function getFields() : array
    {
        return $this->fields;
    }

    function setName(string $name) : void
    {
        $this->name=$name;
    }

    /**
     * 
     * @param array<int,string> $fields
     * @return void
     */
    function setFields(array $fields) : void
    {
        $this->fields=$fields;
    }
}