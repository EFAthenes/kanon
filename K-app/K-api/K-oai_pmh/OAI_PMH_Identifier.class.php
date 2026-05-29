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
/**
 * Description of OAI_PMH_Identifier
 *
 * -> Container for Identifier types with his array of metadata implementation
 * 
 */
class OAI_PMH_Identifier
{
    public string $identifier="";
    /**
     * 
     * @var array<int,string>
     */
    public array $metadatas=[];
    
    /**
     * 
     * @param string $identifier
     * @param array<int,string> $metadatas
     */
    public function __construct(string $identifier,array $metadatas)
    {
        $this->identifier=$identifier;
        $this->metadatas=$metadatas;
    }
    public function toString() : string
    {
        return "identifier : ".$this->identifier." | metadata : ".implode(", ", $this->metadatas);
    }
}