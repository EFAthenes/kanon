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
 * Description of KSplide.class
 *
 * @author Louis Mulot
 */
class KSplide extends KComponent
{
    /**
     * 
     * @var array<int,mixed>
     */
    private array $arrayImages=[];
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $arrayInfos=[];
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $arrayThumbs=[];
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $arraySelects=[];   
    private bool $activateThumbs=false;
    private bool $activateInfos=false;
    private bool $activateSelects=false;
    
    private ?float $heightRatio = null;
    private ?int $fixedWidth = null;
    private ?int $perPage = null;
    private ?int $perMove = null;
    private ?string $gap = null;
    private bool $pagination = false;
    private bool $arrows = true;
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $options=[];
    private bool $isHtmlJavascriptGenerated = false;    
    /**
     * 
     * @var array<int,mixed>
     */
    private array $breakPoints=[];
    private bool $use_v4=true;

    /**
     * 
     * @param string $name
     * @param array<int,mixed>|null $arrayImages
     * @param array<int,mixed>|null $arrayThumbs
     * @param array<int,mixed>|null $arrayInfos
     * @param array<int,mixed>|null $arraySelects
     * @param array<int,mixed>|null $options
     * @param bool $generateHtmlJavascript
     */
    public function __construct(string $name,?array $arrayImages,?array $arrayThumbs=null,?array $arrayInfos=null,?array $arraySelects=null,?array $options=null, bool $generateHtmlJavascript = true)
    {        
        parent::__construct();
        $this->setNone();
        $this->includeConfigFiles();

        $this->setName($name."_KSplide_" . KRandom::makeRandom());
        
        if(is_null($arrayImages) || count($arrayImages)==0)
        {
            return ;
        }

        $this->arrayImages=$arrayImages;
        $this->arrayThumbs=$arrayThumbs;    
        $this->arrayInfos=$arrayInfos;
        $this->arraySelects=$arraySelects;
        $this->options=$options;
        
        $this->verifyArrays();
        
        if ($generateHtmlJavascript)
        {
            $this->generateHtmlJavascript();
        }
    }
    
    /**
     * 
     * @return array<int,mixed>|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    
    public function generateHtmlJavascript(): void
    {
        if (!$this->isHtmlJavascriptGenerated)
        {
            $this->makeHtml();
            $this->makeJavascript();
            $this->isHtmlJavascriptGenerated = true;
        }
    }
    
    private function includeConfigFiles() : void
    {
        $layout = KApp::getInstance()->getLayout();
        
        if($this->use_v4)
        {
            $layout->addJsFileToBuffer(__DIR__ . "/v4/js/splide.min.js");
            $layout->addCssFileToBuffer(__DIR__ . "/v4/css/splide.min.css");
            //$layout->addCssFileToBuffer(__DIR__ . "/css/splide-core.min.css");
            //$layout->addCssFileToBuffer(__DIR__ . "/css/themes/archivage-theme-splide.css");            
        }
        else
        {
            $layout->addJsFileToBuffer(__DIR__ . "/js/splide.min.js");
            $layout->addCssFileToBuffer(__DIR__ . "/css/splide.min.css");
            //$layout->addCssFileToBuffer(__DIR__ . "/css/splide-core.min.css");
            $layout->addCssFileToBuffer(__DIR__ . "/css/themes/archivage-theme-splide.css");
        }
    }
    
    private function verifyArrays() : void
    {
        if(is_array($this->arrayThumbs)&&count($this->arrayImages)==count($this->arrayThumbs))
        {
            $this->activateThumbs=true;
        }
        if(is_array($this->arrayInfos)&&count($this->arrayImages)==count($this->arrayInfos))
        {
            $this->activateInfos=true;
        } 
        if(is_array($this->arraySelects)&&count($this->arrayImages)==count($this->arraySelects))
        {
            $this->activateSelects=true;
        }  
    }

    private function imagesHtml() : string
    {  
        $html = '';
        $i = 0;
        foreach($this->arrayImages as $value)
        {   
            if($value instanceof KComponent)
            {
                $html .= '<li class="splide__slide">'.$value->draw().'</li>';
            }
            else
            {
                $html .= '<li class="splide__slide"><img src="' . $value. '" /></li>';                   
            }
            $i ++;
        }       
        return $html;
    }
    
    private function infosHtml() : string
    {
        $html = '';
        for($j=0; $j<count($this->arrayInfos) ;$j++)
        {         
            $info=$this->arrayInfos[$j];
            $html.='
<div id="data-info_'.$this->getName().'_'.$j.'">'.$info.'</div>';  
        }
        return '<div style="display:none">'.$html.'</div>';
    }
    
    private function thumbsHtml() : string
    {
        $html = '';
        $i = 0;
        for($j=0; $j<count($this->arrayThumbs) ;$j++)
        {   
            $url_image=$this->arrayThumbs[$j];            
            $html .= '<li class="splide__slide"><img src="' . $url_image. '" /></li>
';                   
            $i += 1;
        }       
        return $html;        
    }

    private function makeSelect() : string
    {
        $array=[];
        for ($i = 0; $i < count($this->arraySelects); $i++)
        {
            //$html .= '  <option value="' . ($i) . '"> ' .$this->arraySelects[$i] . '</option>';
            $array[]=[($i),$this->arraySelects[$i]];
        }
        
        $kselect= new KSelectComponent("kselect_".$this->getName(), $array,null,false,"Se rendre à une inscription : ",false,12,12); //e.params.data.text
        $kselect->addJsOnChange('goTo_'.$this->getName().'((e.params.data.id));');
        //primarySlider_'.$this->getName().'.go(e.params.data.id);
        return $kselect->draw();
    }

    public function setPagination(bool $pagination) : bool
    {
        $this->pagination = $pagination;
        return true;
    }

    public function setGap(string $gap) : bool
    {
        if ($gap != "")
        {
            $this->gap = $gap;
            return true;
        }
        return false;
    }

    public function setHeightRatio(?float $ratio) : bool
    {
        if (($ratio > 0 && $ratio <= 1) || !$ratio)
        {
            $this->heightRatio = $ratio;
            return true;
        }
        return false;
    }

    public function setFixedWidth(?int $width) : bool
    {
        if (($width > 1 && $width <= 100) || !$width)
        {
            $this->fixedWidth = $width;
            return true;
        }
        return false;
    }

    public function setPerPage(?int $perPage) : bool
    {
        if ($perPage >= 1 || !$perPage)
        {
            $this->perPage = $perPage;
            return true;
        }
        return false;
    }

    public function setPerMove(?int $perMove) : bool
    {
        if ($perMove >= 1 || !$perMove)
        {
            $this->perMove = $perMove;
            return true;
        }
        return false;
    }
    
    
    public function addBreakPoint(int $size,int $perPage,int $perMove) : bool
    {
        if($size>0&&$perPage>0&&$perMove>0)
        {
            $this->breakPoints[]=[$size,$perPage,$perMove];
            return true;
        }
        return false;
    }

    private function makeJavascript() : void
    {
        $js2='
var primarySlider_'.$this->getName().'=null;            
 ';
        $js='
Promise.all(Array.from(document.getElementsByClassName("")).filter(img => !img.complete).map(img => new Promise(resolve => { img.onload = img.onerror = resolve; }))).then(() => 
{
    primarySlider_'.$this->getName().' = new Splide("#'.$this->getName().'", {
            type       : "loop",         
        ';
        if(count($this->breakPoints))
        {
            $js .= '
            breakpoints: {';
            foreach ($this->breakPoints as $breakPoint)
            {
                $js .= '
                '.$breakPoint[0].': {
                        perPage: '.$breakPoint[1].',
                        perMove : '.$breakPoint[2].',
                },
                ';                
            }
            $js .= '
            },';
        }

        $js .= $this->pagination ? 'pagination : true,' : 'pagination : false,';
        
        $js .= $this->arrows ? 'arrows : true,' : 'arrows : false,';
        
        $js .= '
            cover      : true,
            rewind     : true,
            lazyLoad   : "nearby",
            ';
        
        if ($this->gap)
        {
            $js .= 'gap : "'.$this->gap.'",';
        }

        if ($this->perPage)
        {
            $js .= 'perPage : '.$this->perPage.',';
        }

        if ($this->perMove)
        {
            $js .= 'perMove : '.$this->perMove.',';
        }

        if ($this->heightRatio)
        {
            $js .= 'heightRatio : '.$this->heightRatio.',';
        }

        if ($this->fixedWidth != null)
        {
            $js .= 'fixedWidth : "'.$this->fixedWidth.'%",';
        }

        $js .= '});';

        if($this->activateThumbs)
        {
            $js2.='
var secondarySlider_'.$this->getName().'=null;            
 ';            
            
            $js.='            
    secondarySlider_'.$this->getName().' = new Splide("#'.$this->getName().'-2", {
	rewind      : true,
	fixedWidth  : 100,
	fixedHeight : 64,
	isNavigation: true,
	gap         : 2,
	focus       : "center",
	pagination  : false,
	cover       : true,
        lazyLoad   : "nearby",
	breakpoints : {
		"600": {
			fixedWidth  : 66,
			fixedHeight : 40,
		}
	}
    }).mount();
    primarySlider_'.$this->getName().'.sync( secondarySlider_'.$this->getName().' ).mount();
';
        }
        else
        {
            $js.='primarySlider_'.$this->getName().'.mount();';
        }
        
        if($this->activateInfos)
        {
$js.=' 
function updateInfo_'.$this->getName().'(nb)
{
    //console.log("info=>"+nb);
    $("#info-item").html($("#data-info_'.$this->getName().'_"+nb).html());
}    
primarySlider_'.$this->getName().'.on( "move", function() 
{
    console.log("=>"+primarySlider_'.$this->getName().'.index);
    updateInfo_'.$this->getName().'(primarySlider_'.$this->getName().'.index);
    $("#kselect_'.$this->getName().'").val(primarySlider_'.$this->getName().'.index); 
    $("#kselect_'.$this->getName().'").trigger("change");   
});
updateInfo_'.$this->getName().'(0);
';
        }
        
        if($this->activateSelects)
        {
$js2.=' 
function goTo_'.$this->getName().'(nb)
{
    //console.log(nb);
    primarySlider_'.$this->getName().'.go(nb);
}    
';
        }        
        
        $js.='
    $("#'.$this->getName().'_loader").hide();
});';
        
        $this->addJSText($js2);
        $this->addJSTextOnDocumentReady($js);
    }    
    
    
    private function makeHTML() : void
    {
        $html ='  
<div id="'.$this->getName().'_loader" class="loading_ajax" style="display:block;">

        &nbsp;
        <i class="fa fa-spinner fa-spin"></i>

</div>   
<div id="'.$this->getName().'" class="splide">
    <div class="splide__track">
        <ul class="splide__list">
            '.$this->imagesHtml().'
        </ul>
    </div>
</div>
';
        if($this->activateThumbs)
        {
            $html.='    
<div id="'.$this->getName().'-2" class="splide">
    <div class="splide__track">
        <ul class="splide__list">
            '.$this->thumbsHtml().'
        </ul>
    </div>
</div> 
 ';      
        }
        
        if($this->activateInfos)
        {
            $html.='
<div class="col-12 mb-3">
    <p id="info-item"> 
    </p> 
</div>
'.$this->infosHtml();
        }
        if($this->activateSelects)
        {         
            $html.=$this->makeSelect();
        }        
//    $html.='
//</div>  
//';
        $this->addHTML($html);   
    }
}