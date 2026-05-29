<?php
declare(strict_types=1);
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
abstract class Ki18Component extends KComponent
{
    /**
     * 
     * @var array<string,mixed>|null
     */
    private ?array $data=null;
        
    function __construct()
    {
        parent::__construct();     
        $this->data=[];
    }
    
    protected function addKi18Item(string $key) : void
    {
        $this->data[$key]=Ki18::_($key);
    }
    
    protected function addItem(string $key,mixed $value) : void
    {
        $this->data[$key]=$value;
    }

    /**
     * 
     * @return array<string,mixed>
     */
    protected function getData() : array
    {
        return $this->data;
    }    
}