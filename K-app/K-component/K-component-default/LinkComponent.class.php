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
class LinkComponent extends EmptyComponent
{
    private string $url="";
    private string $text="";
    private bool $blank=false; 
    public function __construct(string $url="",string $text="",bool $blank=false)
    {        
        parent::__construct(); 
        $this->text=$text;
        $this->url=$url;
        $this->blank=$blank;
    }
    
    public function getUrl() : string
    {
        return $this->url;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function getBlank() : bool
    {
        return $this->blank;
    }

    public function setUrl(string $url) : LinkComponent
    {
        $this->url = $url;
        return $this;
    }
    
    public function setKUrl(KURL $url) : LinkComponent
    {
        $this->url = $url->printURLWithoutAmp();
        return $this;
    }    

    public function setText(string $text) : LinkComponent
    {
        $this->text = $text;
        return $this;
    }

    public function addText(string $text) : LinkComponent
    {
        $this->text .= $text;
        return $this;
    }

    public function setBlank(bool $blank) : LinkComponent
    {
        $this->blank = $blank;
        return $this;
    }

        
    public function draw() : string
    {       
        $string="<a ";
        if($this->getClassName()!="")
        {
            $string.=" class=\"".$this->getClassName()."\" ";
        }
        if($this->getIdName()!="")
        {
            $string.=" id=\"".$this->getIdName()."\" ";
        }
        if($this->url!="")
        {
            $string.=' href="'.$this->url.'" ';
        }
        if($this->blank)
        {
            $string.=' target="_blank" ';
        }
        
        $string.=">";
        
        if($this->text!="")
        {
            $string.="".$this->text."";
        }
        return $string.parent::draw()."</a>";
    }
   #[\Override]
    public static function testMe(): ?static
    {
        //string $url="",string $text="",bool $blank=false
       /* @phpstan-ignore-next-line */
        $class=new static(new KURL()->printJustURL(),"the text of the link",false);
        return $class;
    }    
}