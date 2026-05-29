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
class UserDisconnection extends KMiddleware
{
    function __construct() 
    {
        
    }
    function __destruct() 
    {
        
    }    
    public function handle() : bool
    {
        $user=SessionMemory::getInstance()->getUser();
        if(Kapp_Users::isUserConnected()) 
        {
            KApp::getInstance()->setLayout(new TextPlainLayout());
            return true;
        }
        return false;
    }
    public function terminate() : bool
    {
        if(!Kapp_Users::isUserConnected()) 
        {
            KRoute::redirectRoute(RoutesItems::$CONNECTION);
            return true;
        }
        return false;
    }
}