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
class SqlLimit
{
    private string $limit="";
    private string $offset="";
    
    function __construct(mixed $limit,mixed $offset=null)
    {
        $this->setLimit($limit);
        $this->setOffset($offset);
    }
    function __destruct()
    {
        
    } 
    function getLimit() : string
    {
        return $this->limit;
    }
    function getLimitInt() : int
    {
        if(!empty($this->limit))
        {
            $value=intval($this->limit);
            if(isInteger($value))
            {
                return $value;
            }
        }
        return 0;
    }    

    function getOffset() : string
    {
        return $this->offset;
    }
    function getOffsetInt() : int
    {
        if(!empty($this->offset))
        {
            $value=intval($this->offset);
            if(isInteger($value))
            {
                return $value;
            }
        }
        return 0;
    }    

    function setLimit(mixed $limit) : bool
    {
        if(isInteger($limit))
        {
            $limitInt= intval($limit);
            if($limitInt>0)
            {
                $this->limit=$limitInt."";  
                return true;
            }
        }
        return false;
    }
    function setOffset(mixed $offset) : bool
    {
        if(isInteger($offset))
        {
            $offsetInt= intval($offset);
            if($offsetInt>0)
            {
                $this->offset="".$offsetInt;     
                return true;
            }
        }
        return false;
    }
    
    public function toString(string $delimitor="<br />") : string
    {
        return "limit=>".$this->limit."//offset=>".$this->offset;
    }
}