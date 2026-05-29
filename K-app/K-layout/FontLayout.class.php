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
class FontLayout extends KLayout
{
    public function __construct()
    {
        parent::__construct("FontLayout");
        $this->setPrintOnlyComponents(true);
    }    
    //
    public function initialize() : void
    {
        header("Content-type: application/font-woff2;q=1.0,application/font-woff;q=0.9,*/*;q=0.8;");   
        $this->addLayoutItem(self::$CENTER,new EmptyComponent());
        $this->setComponentAsMain(self::$CENTER);
    }
    
    public function terminate() : void
    {

    }
}