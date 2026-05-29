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
 * Description of KChartComponent
 *
 * @author Mulot Louis
 */
class KChartComponent extends KComponent
{
    private string $options="";
    private string $type="";
    /**
     * 
     * @var array<int,KChartPieItem>
     */
    private array $arrayItems=[];
    
    private string $dataString="";
    private string $backgroundColorString="";
    private string $hoverBackgroundColorString="";
    private string $labelString="";
    
    function __construct()
    {
        parent::__construct();       
        $this->setId();
        
        $this->setName("KChart_".KRandom::makeRandom());     
        $layout=KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/chart.js"); 
        $this->setTypePie();
    }
    
    public function setTypePie() : KChartComponent
    {
        $this->type="pie";
        return $this;
    }
    
    public function setTypeDonut() : KChartComponent
    {
        $this->type="doughnut";
        return $this;
    }
    
    public function setTypeBar() : KChartComponent
    {
        $this->type="bar";
        return $this;
    }
    
    public function setTypeHBar() : KChartComponent
    {
        $this->type="horizontalBar";
        return $this;
    }
    
    public function setTypeLine() : KChartComponent
    {
        $this->type="line";
        return $this;
    }
    
    public function setTypeRadar() : KChartComponent
    {
        $this->type="radar";
        return $this;
    }
    
    public function setTypePolar() : KChartComponent
    {
        $this->type="polarArea";
        return $this;
    }
    
    function draw(): string
    {
        $html='<canvas id="'. $this->getName().'"></canvas>';
        $html.=parent::draw();
        return $html.$this->makeTheScript();
    }
    
    public function setOptions(string $options) : void
    {
        $this->options=$options;
    }
    
    public function setNoLegend() : KChartComponent
    {
        $this->options="legend: { display: false }";
        return $this;
    }
    
    public function setLegendPositionRight() : KChartComponent
    {
        $this->options="legend: { position: 'right' }";
        return $this;
    }
    
    public function setLegendPositionLeft() : KChartComponent
    {
        $this->options="legend: { position: 'left' }";
        return $this;
    }
    
    public function addItem(string $label,mixed $data,?string $color=null,?string $colorHover=null) :void
    {
        $item=new KChartPieItem($label,$data,$color,$colorHover);
        $this->addKChartPieItem($item);
        
    }
    public function addKChartPieItem(KChartPieItem $item) : void
    {
        $this->arrayItems[]=$item;
    }
    
    /*
    private $dataString="";
    private $backgroundColorString="";
    private $hoverBackgroundColorString="";
    private $labelString="";
     */
    
    private function prepareData() : bool
    {
        if(!count($this->arrayItems))
        {
            return false;
        }
        $count=0;
        /* @var $item KChartPieItem */
        foreach ($this->arrayItems as $item)
        {
            if($count)
            {
                $this->dataString.=",";
                $this->backgroundColorString.=",";
                $this->hoverBackgroundColorString.=",";
                $this->labelString.=",";
            }
            
            $this->labelString.='"'.$item->label.'"';
            $this->dataString.=$item->data;
            
            if(empty($item->color))
            {
                // create color
                $item->color='"rgb('.mt_rand(0, 255).','.mt_rand(0, 255).','.mt_rand(0, 255).',1)"';
                $this->backgroundColorString.=$item->color;
            }
            else
            {               
                $item->color='"'.$item->color.'"';
                $this->backgroundColorString.=$item->color;
            }
            
            if(empty($item->colorHover))
            {
                $this->hoverBackgroundColorString.=$item->color;
            }
            else
            {            
                $item->colorHover='"'.$item->colorHover.'"';
                $this->hoverBackgroundColorString.=$item->colorHover;
            }
            
            $count++;
        }
        return true;
    }
    
    private function makeTheScript() : string
    { 
        if(!$this->prepareData())
        {
            return "Error : No data for the chart!";
        }
        
        $html='
let ctx_'. $this->getName().' = document.getElementById("'. $this->getName().'").getContext("2d");
let chart_'. $this->getName().' = new Chart(ctx_'. $this->getName().', {
    // The type of chart we want to create
    type: "'.$this->type.'", 

    // The data for our dataset
    data : {
        datasets: [{
            data: ['.$this->dataString.'],
            backgroundColor: ['.$this->backgroundColorString.'],
            hoverBackgroundColor: ['.$this->hoverBackgroundColorString.']    
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: ['.$this->labelString.']
    },

    // Configuration options go here
    options: {'.$this->options.'}
});            
';
        return "<script>".$html."</script>";
    }
}