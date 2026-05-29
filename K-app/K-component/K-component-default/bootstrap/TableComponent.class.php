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
class TableComponent extends KComponent
{
    private string $table_id="";
    /**
     * 
     * @var array<int,string>
     */
    private array $arrayColumns=[];
    /**
     * 
     * @var array<int,array<int,mixed>>
     */
    private array $arrayLines=[];
    private bool $activateHTMLEntities=true;
    
    private string $styleTheadTr="";
    private string $styleTheadTh="";
    private string $styleTable="";
    private string $styleTableTr="";
    private string $styleTableTd="";
    
    /**
     * 
     * @param string $table_id
     * @param array<int,string> $arrayColumns
     * @param array<int,array<int,mixed>> $arrayLines
     */
    function __construct(string $table_id,array $arrayColumns,array $arrayLines)
    {        
        parent::__construct();
        $this->setNone();
        $this->table_id=$table_id;
        $this->setIdName($this->table_id);
        $this->arrayColumns=$arrayColumns;
        $this->arrayLines=$arrayLines;
        $this->setClassName("table table-hover table-bordered");
    }
    public function setHtmlEntities(bool $activate) : self
    {
        $this->activateHTMLEntities=$activate;
        return $this;
    }
    
    public function setStyleTheadTr(mixed $style) : void
    {
        $this->styleTheadTr=strval($style);
    }
    public function setStyleTheadTh(mixed $style) : void
    {
        $this->styleTheadTh=strval($style);
    }    
    public function setStyleTable(mixed $style) : void
    {
        $this->styleTable=strval($style);
    }
    public function setStyleTableTr(mixed $style) : void
    {
         $this->styleTableTr=strval($style);
    }  
    public function setStyleTableTd(mixed $style) : void
    {
        $this->styleTableTd=strval($style);
    }    
    
    private function getStyleTable() : string
    {
        if(!empty($this->styleTable))
        {
            return 'style="'.$this->styleTable.'"';
        }
        return "";
    }
    
    private function getStyleTableTr() : string
    {
        if(!empty($this->styleTableTr))
        {
            return 'style="'.$this->styleTableTr.'"';
        }
        return "";
    } 
    private function getStyleTableTd() : string
    {
        if(!empty($this->styleTableTd))
        {
            return 'style="'.$this->styleTableTd.'"';
        }
        return "";
    }     
    
    private function getStyleTheadTr() : string
    {
        if(!empty($this->styleTheadTr))
        {
            return 'style="'.$this->styleTheadTr.'"';
        }
        return "";
    }  
    
    private function getStyleTheadTh() : string
    {
        if(!empty($this->styleTheadTh))
        {
            return 'style="'.$this->styleTheadTh.'"';
        }
        return "";
    }      
    
    
    /**
     * 
     * @param array<int,mixed> $line
     */
    public function addLine(array $line) : void
    {
        $this->arrayColumns[]=$line;
    }
    public function draw() : string
    {
        $html='';
        $html.='     
<table id="'.$this->getName().'" class="'.$this->getClassName().'" '.$this->getStyleTable().' >';
        
        if(count($this->arrayColumns))
        {
            $html.='<thead><tr '.$this->getStyleTheadTr().'>';
            foreach($this->arrayColumns as $column)
            {
                //$string=strval($column);
                KDebugger::getInstance()->dump($column);
                $html.='<th '.$this->getStyleTheadTh().'>'.($this->activateHTMLEntities? FormComponent::inputString($column): $column).' </th>';
            }
            $html.='</tr></thead>';
        }
        
        $html.='<tbody>';
        foreach($this->arrayLines as $line)
        {
            $html.='<tr '.$this->getStyleTableTr().'>';
            foreach ($line as $item)
            {
                if($item instanceof KComponent)
                {
                    $html.='<td '.$this->getStyleTableTd().'>'.$item->draw().'</td>';
                }
                else
                {
                    $html.='<td '.$this->getStyleTableTd().'>'.($this->activateHTMLEntities? FormComponent::inputString($item): $item).'</td>';
                }
            }
            $html.='</tr>';
        }   
        $html.='</tbody></table>';
        
        return $html;
    }
}