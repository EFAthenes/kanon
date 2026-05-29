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
 * Description of LinkDataTableComponent
 *
 * @author Louis Mulot
 */
class LinkDataTableComponent extends KComponent
{
    private ?string $link="";
    private ?string $title="";
    private bool $blank=false;
    
    function __construct(?string $link,mixed $content=null,?string $title="",bool $blank=false)
    {        
        parent::__construct();
        $this->setNone();
        $this->link=$link;
        $this->title=$title;
        $this->blank=$blank;
        if($content instanceof KComponent)
        {
            $this->addComponent($content);
        }
        else
        {
            $this->addHTML("".strval($content));
        }
    }
    
    function getBlank() : bool
    {
        return $this->blank;
    }

    function setBlank(bool $blank) : void
    {
        $this->blank = $blank;
    }
    
    function getLink() : ?string
    {
        return $this->link;
    }

    function setLink(?string $link) : void
    {
        $this->link="".$link;
    }
    
    function addToLink(mixed $link) : void
    {
        if(is_null($this->link))
        {
            $this->link="";
        }
        $this->link.="".$link;
    }  
    
    function getTitle(): ?string
    {
        return $this->title;
    }
    
    function setTitle(string $title) : void
    {
        $this->title=$title;
    }

    function draw() : string
    {
        $html="";
        if(is_null($this->link))
        {
            $html.='<a href="'.parent::draw().'"'; 
        }
        else
        {
            $html.='<a href="'.$this->link.'"'; 
        }
        if($this->title!="")
        {
            $html.=' title="'.FormComponent::inputString($this->title).'"'; 
        }
        if($this->blank)
        {
            $html.=' target="_blank"'; 
        }
        $html.='>';
        $html.=parent::draw();
        $html.='</a>';
        return $html;
    }
    
}