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
class OAI_PMH_Set
{
    public string $setName="";
    public string $setSpec="";
    public string $setDescription="";
    /**
     * 
     * @var array<int,string>
     */
    public array $metaDataPrefixes = [];
    public ?string $className=null;
    
    /**
     * 
     * @param string $setName
     * @param string $setSpec
     * @param string $setDescription
     * @param array<int,string> $metaDataPrefixes
     * @param string|null $className
     */
    public function __construct(string $setName,string $setSpec,string $setDescription="", array $metaDataPrefixes = [],?string $className=null)
    {
        $this->setName=$setName;
        $this->setSpec=$setSpec;
        $this->setDescription=$setDescription;
        $this->metaDataPrefixes = $metaDataPrefixes;
        $this->className = $className;
    }

    public function hasMetadata(mixed $metadata): bool
    {
        return in_array("".strval($metadata), $this->metaDataPrefixes);
    }

    public function toString() : string
    {
        return "setName : ".$this->setName." | setSpec : ".$this->setSpec." | setDescription : ".$this->setDescription." | metaDataPrefixes : ".print_r($this->metaDataPrefixes,true)."".($this->className ?? "no");
    }    
}