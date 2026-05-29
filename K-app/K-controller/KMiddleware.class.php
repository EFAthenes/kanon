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
abstract class KMiddleware
{ 
    /* @var $list_middlewares ArrayList */
    private ?ArrayList $list_middlewares=null;
    function __construct() 
    {
        
    }
    function __destruct() 
    {
        
    }   
    
    abstract public function handle() : bool;
    abstract public function terminate() : bool;
    public function addMiddleware(KMiddleware $middleware) : void
    {
        if(is_null($this->list_middlewares))
        {
            $this->list_middlewares=new ArrayList();
        }
        $this->list_middlewares->add($middleware);
    }
    public function handleAll() : bool
    {
        /* @var $middleware KMiddleware */
        if(!is_null($this->list_middlewares))
        {
            foreach($this->list_middlewares as $middleware)
            {
                if(!$middleware->handleAll())
                {
                    return false;
                }
            }
        }
        return $this->handle();
    }
    public function terminateAll() : bool
    {
        /* @var $middleware KMiddleware */
        if(!is_null($this->list_middlewares))
        {
            foreach($this->list_middlewares as $middleware)
            {
                if(!$middleware->terminateAll())
                {
                    return false;
                }
            }
        }
        return $this->terminate();
    }
}