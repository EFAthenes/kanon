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
class WidgetComponent extends KComponent
{
    public static int $TYPE_SUCCESS=1;
    public static int $TYPE_INFO=4;
    public static int $TYPE_WARNING=2;
    public static int $TYPE_ERROR=3;
    private int $widget_type=1;
    private ?string $fa_icon=null;
    private ?string $widget_title=null;
    private ?string $entire_widget_url=null;
    private ?string $widget_text=null;
    private bool $full_colored=false;
    private string $specific_color1="";
    private string $specific_color2="";
    function __construct(?string $title=null,string $icon="fa fa-thumbs-o-up",int $type=1,?string $text=null)
    {        
        parent::__construct();
        $this->setNone(); 
        
        $this->fa_icon=$icon;
        $this->setWidgetType($type);     
        $this->widget_title=$title;
        $this->widget_text=$text;
    }
    
    public function setWidgetTitle(string $widgetTitle) : WidgetComponent
    {
        $this->widget_title=$widgetTitle;
        return $this;
    }

    public function setWidgetTitleLink(string $url) : void
    {
        $this->widget_title = '<a href="' . $url . '">' . $this->widget_title . '</a>';
    }
    
    public function setWidgetTitleKURL(KURL $kurl)  : void
    {
        $this->setWidgetTitleLink($kurl->printURLWithHostWithoutAmp());
    }  
    
    public function setEntireWidgetLink(string $url) : void
    {
        $this->entire_widget_url = $url;
    }
    public function setEntireWidgetKURL(KURL $kurl) : void
    {
        $this->entire_widget_url = $kurl->printURLWithHostWithoutAmp();
    }   
    
    public function setWidgetText(string $widgetText) : WidgetComponent
    {
        $this->widget_text=$widgetText;
        return $this;
    }    

    public function setWidgetType(int $widgetType) : WidgetComponent
    {
        $this->widget_type=$widgetType;
        return $this;
    }

    public function setFullColored(bool $fullColored) : WidgetComponent
    {
        $this->full_colored=$fullColored;
        return $this;
    }   
    
    public function setSpecificColor(string $color1,?string $color2=null) : void
    {
        $this->specific_color1=$color1;
        if(!empty($color2))
        {
            $this->specific_color2=$color2;
        }
    }
    
    public function getSpecific_color2(): string
    {
        return $this->specific_color2;
    }

    public function setSpecific_color2(string $specific_color2): void
    {
        $this->specific_color2 = $specific_color2;
    }
    
    public function draw() : string
    {       
        $html = '';
        $colored_string="coloured-icon";
        if($this->full_colored)
        {
            $colored_string="";
        }
        $spec_style="";
        if($this->specific_color1!="")
        {
            $spec_style=' style="color:'.$this->specific_color1.'"';
        }
        
        if(!is_null($this->entire_widget_url))
        {
            $html.= '<a href="' . $this->entire_widget_url . '">';
        }         
        
        $html.= '
<div class="widget-small '.$this->drawType().' '.$colored_string.'" '.$spec_style.' >
    <i class="icon '.$this->fa_icon.' fa-3x"></i>
    <div class="info">
';
        
        if(!empty($this->widget_title))
        {
            $html.= '<h4>'.$this->widget_title.'</h4>';
        }        
        if(!empty($this->widget_text))
        {
            $html.= '<p>'.$this->widget_text.'</p>';
        }         

        $html.=parent::draw(). '
    </div>
</div>
';      
        if(!is_null($this->entire_widget_url))
        {
            $html.= '</a>';
        }     
        
        return $html;
    }
    
    private function drawType() :string
    {
        switch ($this->widget_type)
        {
            case 1 : 
                return "primary";
            case 2 : 
                return "warning";
            case 3 : 
                return "danger";
            case 4 : 
                return "info";                
        }
        return "primary";
    }
    
    
}