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
class TitleComponent extends KComponent
{
    private int $heigth=3;
    private string $title="";
    private ?string $icon=null;
    private string $underLineString="";
    function __construct(string $title,bool $underLine=true,?int $height=3,?string $icon=null)
    {        
        parent::__construct();
        $this->setNone(); 
        
        $this->title=$title;
        $this->icon=$icon;
        
        if(!is_null($height)&&$height>0&&$height<7)
        {
            $this->heigth=$height;
        }
        if($underLine)
        {
            $this->underLineString="line-head";
        }
    }
    
    public function draw(): string
    {
        $string_icon='';
        if(!is_null($this->icon))
        {
           $string_icon='<i class="'.kPurify($this->icon).'"></i> ';
        }
        $html = '<h'.$this->heigth.' class="mb-3 '.$this->underLineString.' '.$this->getClassName().'" >'.$string_icon.FormComponent::inputString($this->title).'</h'.$this->heigth.'>';
        //$this->addHTML($html);
        return $html;
    }
}