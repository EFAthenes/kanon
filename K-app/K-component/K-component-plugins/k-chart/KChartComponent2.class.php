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
 */
class KChartComponent2 extends KComponent
{
    private string $classes = "";

    private string $type = "";
    private string $labels = "";
    private string $datasets = "";
    private string $options = "";
    private string $plugins_options = "";
    
    /**
     * 
     * @var array<int,array<int,KChartPieItem>> 
     */
    private array $arrayDatasets = [];
    /**
     * 
     * @var array<int,string> 
     */    
    private array $arrayLabelDataset = [];
    /**
     * 
     * @var array<int,string> 
     */    
    private array $arrayStackDataset = [];
    /**
     * 
     * @var array<int,string> 
     */     
    private array  $arrayLabels = [];
    
    public const string YELLOW = "#ffcd56";
    public const string ORANGE = "#ff9f40";
    public const string ROSE = "#ff6384";
    public const string RED = "#e52c18";
    public const string PURPLE = "#9966ff";
    public const string BLUE = "#36a2eb";
    public const string GREEN = "#4bc0c0";
    
    /**
     * crée un graphique de type pie chart par défaut 
     * $nbDatasets: nombre de jeux de données, par défaut 1
     * $name: nom unique du graphique, généré aléatoirement si non spécifié
     */
    function __construct(?string $name=null, int $nbDatasets=1)
    {
        parent::__construct();
        $this->setId();    

        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/chart.js");

        $this->setTypePie();

        if (!empty($name))
        {
            $this->setName($name);
        }
        else
        {
            $this->setName("KChart_".KRandom::makeRandom());
        }

        $nbDatasets2 = $nbDatasets >= 1 ? $nbDatasets : 1;
        for ($i = 0; $i < $nbDatasets2; $i++)
        {
            $this->arrayDatasets[] = [];
            $this->arrayLabelDataset[] = "Dataset ".$i;
            $this->arrayStackDataset[] = "Stack ".$i;
        }
    }

    /**
     * définit les classes de la balise <canvas> du graphique
     */
    public function setCanvasClass(string $classes): void
    {
        $this->classes = $classes;
    }
    
    /* Type de graphique */

    public function setTypePie() : KChartComponent2
    {
        $this->type = "pie";
        return $this;
    }
    
    public function setTypeDonut() : KChartComponent2
    {
        $this->type = "doughnut";
        return $this;
    }
    
    public function setTypeBar() : KChartComponent2
    {
        $this->type = "bar";
        return $this;
    }

    public function setTypeStackedBarChart() : KChartComponent2
    {
        $this->type = "bar";
        $this->setStackedBar();
        return $this;
    }
    
    public function setTypeHBar() : KChartComponent2
    {
        $this->type = "horizontalBar";
        return $this;
    }
    
    public function setTypeLine() : KChartComponent2
    {
        $this->type = "line";
        return $this;
    }
    
    public function setTypeRadar() : KChartComponent2
    {
        $this->type = "radar";
        return $this;
    }
    
    public function setTypePolar() : KChartComponent2
    {
        $this->type = "polarArea";
        return $this;
    }
    
    /**
     * 
     * @return array<int,string> 
     */
    public function getArrayLabels(): array
    {
        return $this->arrayLabels;
    }

    
    /* Options du graphique */

    /**
     * obtenir toutes les options du graphique y compris celles ajouter dans plugins
     */
    private function getOptions(): string
    {
        $string = "";
        if (!empty($this->options) || !empty($this->plugins_options))
        {
            $string .= "options: {";
            $string .= $this->options;
            if (!empty($this->options) && !empty($this->plugins_options))
            {
                $string .= ",";
            }
            if (!empty($this->plugins_options))
            {
                $string .= "plugins: {".$this->plugins_options."}";
            }
            $string .= "}";
        }
        return $string;
    }

    /**
     * ajouter une option qui n'est pas une option plugin
     */
    private function addOption(string $option) : void
    {
        if (empty($this->options))
        {
            $this->options = $option;
        }
        else
        {
            $this->options = $this->options.",".$option;
        }
    }

    /**
     * ajouter une option plugin
     */
    private function addPluginOption(string $option) : void
    {
        $this->plugins_options = empty($this->plugins_options) ? $option : $this->plugins_options.",".$option;
    }
    
    /**
     * ne pas afficher de légende
     */
    public function setNoLegend() : KChartComponent2
    {
        $this->addPluginOption("legend: { display: false }");
        return $this;
    }
    
    /**
     * afficher la légende à droite du graphique
     */
    public function setLegendPositionRight() : KChartComponent2
    {
        $this->addPluginOption("legend: { position: 'right' }");
        return $this;
    }
    
    /**
     * afficher la légende à gauche du graphique
     */
    public function setLegendPositionLeft() : KChartComponent2
    {
        $this->addPluginOption("legend: { position: 'left' }");
        return $this;
    }

    public function setResponsive(bool $bool) : KChartComponent2
    {
        if ($bool)
        {
            $this->addOption("responsive: true");
        }
        else
        {
            $this->addOption("responsive: false");
        }
        return $this;
    }

    /**
     * définir le titre du graphique
     */
    public function setTitle(string $title): void
    {
        $this->addPluginOption('title: {display: true, text: "'.$title.'"}');
    }

    /**
     * pour obtenir un diagramme de type bar, avec des barres empilées
     */
    private function setStackedBar(): void
    {
        $this->addOption("
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        }");
    }

    /* Data, labels and colors*/

    /**
     * ajoute une donnée $data au jeu de données numéro $indexDataset
     */
    public function addItem(mixed $label,mixed $data, ?string $color=null, ?string $colorHover=null, int $indexDataset=0): void
    {
        $item = new KChartPieItem(strval($label),$data, $color, $colorHover);
        $this->addKChartItem($item, $indexDataset);
    }

    private function addKChartItem(KChartPieItem $item, int $indexDataSet) : void
    {
        $this->arrayDatasets[$indexDataSet][] = $item;
    }

    /**
     * définit le label du jeu de données
     */
    public function setLabelDataset(int $indexDataset, string $label): void
    {
        $this->arrayLabelDataset[$indexDataset] = $label;
    }
    
    /**
     * définit le label du jeu de données 0
     */    
    public function setDefaultLabelDataset(string $label): void
    {
        $this->arrayLabelDataset[0] = $label;
    }    

    /**
     * pour un graphique de type stacked bar, les jeux de données avec le même label de stack seront empilés
     */
    public function setStackDataset(int $indexDataset, string $label): void
    {
        $this->arrayStackDataset[$indexDataset] = $label;
    }

    /**
     * ajouter un label au graphique
     */
    
    public function addLabel(string $label): void
    {
        $this->arrayLabels[] = $label;
    }

    /* Draw */
    
    function draw(): string
    {
        $html = '<canvas class="'.$this->classes.'" id="'. $this->getName().'"></canvas>';
        $html .= parent::draw();
        return $html.$this->makeTheScript();
    }

    private function makeTheScript() : string
    {
        //$this->prepareLabels();
        $this->prepareData();
        $html = '
        let ctx_'.$this->getName().' = document.getElementById("'.$this->getName().'").getContext("2d");
        let chart_'.$this->getName().' = new Chart(ctx_'.$this->getName().', {
            type: "'.$this->type.'", 
            data : {
                datasets: '.$this->datasets.',
                labels: '.$this->labels.'
            },
            '.$this->getOptions().'
        });            
        ';
        return "<script>".$html."</script>";
    }
    /*
    private function prepareLabels() : bool
    {
        $this->labels = "[";
        $count = 0;
        foreach($this->arrayLabels as $label)
        {
            if($count)
            {
                $this->labels .=",";
            }
            $this->labels .= '"'.$label.'"';
            $count ++;
        }
        $this->labels .= "]";
        return true;
    }
    */
    private function prepareData() : bool
    {
        $tempLabels=[];
        $countDatasets = 0;
        $countItemInDataset = 0;
        $this->datasets = '[';
        foreach ($this->arrayDatasets as $arrayDataset)
        {
            if($countDatasets)
            {
                $this->datasets .= ',';
            }
            $this->datasets .= '{';

            $countItemInDataset = 0;
            $data = "";
            $backgroundColor = "";
            $hoverBackgroundColor = "";
            $label = $this->arrayLabelDataset[$countDatasets];
            $stack = ($this->type == "bar") ? ', stack:"'.$this->arrayStackDataset[$countDatasets].'"' : '';
            foreach($arrayDataset as $item)
            {
                if($countItemInDataset)
                {
                    $data .=",";
                    $backgroundColor .= ",";
                    $hoverBackgroundColor .= ",";
                }
                $tempLabels[]= '"'.$item->label.'"';
                $data .= $item->data;
                if(empty($item->color))
                {
                    $item->color = '"rgb('.mt_rand(0, 255).','.mt_rand(0, 255).','.mt_rand(0, 255).',1)"';
                    $backgroundColor .= $item->color;
                }
                else
                {               
                    $item->color = '"'.$item->color.'"';
                    $backgroundColor .= $item->color;
                }
                if(empty($item->colorHover))
                {
                    $hoverBackgroundColor .= $item->color;
                }
                else
                {            
                    $item->colorHover = '"'.$item->colorHover.'"';
                    $hoverBackgroundColor .= $item->colorHover;
                }
                $countItemInDataset ++;
            }
            $this->datasets .= 'label:"'.$label.'", data:['.$data.'],  backgroundColor: ['.$backgroundColor.'], hoverBackgroundColor: ['.$hoverBackgroundColor.'] '.$stack.'}';

            $countDatasets ++;
        }
        $this->datasets .= ']';
        if($this->type == "bar")
        {
            $tempLabels=array_unique($tempLabels);
        }
 
        foreach($tempLabels as $label)
        {
            if(!empty($this->labels))
            {
                $this->labels.=",";
            }
            $this->labels.=$label;  
        }
        $this->labels="[".$this->labels."]"; 
        return true;
    }
}