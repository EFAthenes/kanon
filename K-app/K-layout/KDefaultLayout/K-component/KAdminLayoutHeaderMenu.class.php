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
class KAdminLayoutHeaderMenu extends KComponent
{
    protected string $title="";
    protected string $url="";
    protected ?ArrayList $listItems=null;
    
    function __construct(string $title="",string $url="")
    {        
        parent::__construct();
        $this->setNone(); 
        $this->title=$title;
        $this->url=$url;
        $this->listItems=new ArrayList();
    }
    
    public function addItem(KAdminLayoutHeaderMenuItem $item) : void
    {
        $this->listItems->add($item);
        $this->addComponent($item);
    }
    
    public function draw() : string
    {
        $html='
    <header class="app-header">
        <a class="app-header__logo" href="'.$this->url.'">'.$this->title.'</a>
        <!-- Sidebar toggle button-->
        <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"><i style="padding-top:15px;" class="fas fa-bars"></i></a>
        <!-- Navbar Right Menu-->
        <ul class="app-nav">
';
      
        foreach ($this->listItems as $item)
        {
            $html.=$item->draw();
        }
        
        
        $html.='
        
        </ul>
    </header>
    '; //.parent::draw().'
//';
        return $html;
        //return '<header class="app-header">'.parent::draw().'</main>';
    }    
    
}
