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
class SessionComponent extends KComponent
{
    function __construct(string $name="session_component")
    {
        parent::__construct();
        $this->setName($name);
        $this->setClass();
    }

    public function draw() : string
    {
        $this->addHTML("<p> SESSION :: Name :".ParamManager::getInstance()->site_session_name."</p>");    
        $this->addHTML("<p> SESSION :: Memory".SessionMemory::getInstance()->toString()."</p>");

        if(isset($_SESSION['message']))
        {            
            $this->addHTML(print_r($_SESSION['message'],true));
        }

        return parent::draw();
    }
}