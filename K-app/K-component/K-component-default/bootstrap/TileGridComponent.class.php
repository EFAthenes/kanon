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
class TileGridComponent extends KComponent
{
    private ?TileComponent $tile=null;
    private ?RowComponent $row=null;
    function __construct()
    {        
        parent::__construct(); 
        $root =new RowComponent();
        $this->addComponent($root);
        $this->tile=new TileComponent();
        $this->tile->addClassName("col-12");
        $this->addRowComponent();
        $root->addComponent($this->tile);
    }
    
    public function addColComponent(KComponent $component,string $size="-12") : void
    {
        if(is_null($this->row))
        {
            $this->addRowComponent();
        }
        $col = new ColComponent($size);
        $col->addComponent($component);
        $this->row->addComponent($col);
    }
    
    public function addRowComponent() : void
    {
        $this->row=new RowComponent();
        $this->tile->addComponent($this->row);
    }
    
}