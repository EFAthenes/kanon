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
 * Description of KLightSlider
 *
 * @author Sara Lasnami
 * @author louis.mulot
 */
class KLightSlider extends KComponent
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
    
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $options=[];


    /**
     * 
     * @param string $name
     * @param array<int,mixed>|null $arrayImages
     * @param array<int,mixed>|null $arrayThumbs
     * @param array<int,mixed>|null $arrayInfos
     * @param array<int,mixed>|null $arraySelects
     * @param array<int,mixed>|null $options
     */
    public function __construct(string $name,?array $arrayImages,?array $arrayThumbs=null,?array $arrayInfos=null,?array $arraySelects=null,?array $options=null)
    {        
        parent::__construct();
        $this->setNone();
        $this->includeConfigFiles();

        $this->setName($name."_KLightSlider_" . KRandom::makeRandom());
        
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
        
        $this->makeHtml();      
        $this->makeJavascript();
    }
    
    private function includeConfigFiles() : void
    {
        $layout = KApp::getInstance()->getLayout();
        // NEEDED IF NOT INCLUDED IN LAYOUT 
        //$layout->addJsFileToBuffer(__DIR__ . "/js/bootstrap.min.js");
        //$layout->addCssFileToBuffer(__DIR__ . "/css/bootstrap.min.css");

        $layout->addJsFileToBuffer(__DIR__ . "/js/lightslider.min.js");
        $layout->addCssFileToBuffer(__DIR__ . "/css/lightslider.min.css");
        $layout->addCssFileToBuffer(__DIR__ . "/css/style.css");
    }
    
    /**
     * 
     * @return array<int,mixed>|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    
    private function makeJavascript() : void
    {
        $function_select='';
        $call_function_updateSelect='';
        $function_updateInfo='';
        $call_function_updateInfo='';
        
        
        
        if($this->activateSelects)
        {
            $function_select='
$("#slider-num_'.$this->getName().'").on("change", () => {
    const sliderNum = $("#slider-num_'.$this->getName().'").val();
    lightSlider_'.$this->getName().'.goToSlide(parseInt(sliderNum))
});     
function updateSlider_'.$this->getName().'()
{
    const selected = $(".slider-item.lslide.active");
    $("#slider-num_'.$this->getName().'").val(selected.attr("data-num"));
}   
 ';        
            $call_function_updateSelect='updateSlider_'.$this->getName().'();';
        }
        
        if($this->activateInfos)
        {
            $function_updateInfo='
function updateInfo_'.$this->getName().'()
{
    const selected = $(".slider-item.lslide.active");
    $("#info-item").html(selected.attr("data-info"));
}                
 ';
            $call_function_updateInfo='updateInfo_'.$this->getName().'();';
        }
        
        $js='   
const lightSlider_'.$this->getName().' = $("#'.$this->getName().'").lightSlider({
    gallery: '.convertBoolToString($this->activateThumbs).',
    item: 1,
    //loop: true,
    slideMargin: 0,
   /* thumbItem: 50, */
    onAfterSlide: function()
    {
       '.$call_function_updateSelect.'
       '.$call_function_updateInfo.'
    },
});
'.$function_select.'
'.$function_updateInfo.'
'.$call_function_updateInfo.'
    ';
        
        /*
    $(".slider-num").on("change", () => {
        const sliderNum = $(".slider-num").val();
        lightSlider.goToSlide(parseInt(sliderNum))
    });
    

    function updateInfo_'.$this->getName().'()
    {
        const selected = $(".slider-item.lslide.active");
        $("#info-item").html(selected.attr("data-info"));
        $(".slider-num").val(selected.attr("data-num"));
    }
    updateInfo'.$this->getName().'();

';
         * 
         */
        $this->addJSTextOnDocumentReady($js);
    }
    
    private function verifyArrays() : void
    {
        if(count($this->arrayImages)==count($this->arrayThumbs))
        {
            $this->activateThumbs=true;
        }
        if(count($this->arrayImages)==count($this->arrayInfos))
        {
            $this->activateInfos=true;
        } 
        if(count($this->arrayImages)==count($this->arraySelects))
        {
            $this->activateSelects=true;
        }  
    }

    private function imagesHtml() : string
    {  
        $html = '';
        $i = 0;
        for($j=0; $j<count($this->arrayImages) ;$j++)
        {   
            $url_image=$this->arrayImages[$j];
            
            $string_thumbs="";
            if($this->activateThumbs)
            {
                $thumb=$this->arrayThumbs[$j];
                $string_thumbs=' data-thumb="'.$thumb.'" ';
            }
            
            $string_infos="";
            if($this->activateInfos)
            {
                $info=$this->arrayInfos[$j];
                $string_infos=' data-info="'.$info.'" ';
            }            
            
            $html .= '<li data-num="'.$i.'" '.$string_thumbs.' '.$string_infos.' class="slider-item"><img src="' . $url_image. '" loading="lazy"/></li>';                   
            
//            $html .= '<li data-num="'.$i.'" data-thumb="' . $item['thumb'] . '" data-info="'.$item['info']. '" class="slider-item">
//                    <img src="' . $item['url'] . '" alt="' . $item['info'] . '" loading="lazy"/>
//                </li>';            
            
            $i += 1;
        }       
        return $html;
    }

    private function makeSelect() : string
    {
        $html = '';
        for ($i = 0; $i < count($this->arraySelects); $i++)
        {
            $html .= '  <option value="' . ($i) . '"> ' .$this->arraySelects[$i] . '</option>';
        }
        return $html;
    }

    private function makeHTML() : void
    {
        $html ='
<div class=" light-slider-component row" >

    <div class="col-12">
        <ul id="'.$this->getName().'">
            '.$this->imagesHtml().'
        </ul>
    </div>
';
        if($this->activateInfos)
        {
            $html.='
    <blockquote class="col-12 mb-3">
        <p id="info-item"> 
        </p> 
    </blockquote>
';

        }
        if($this->activateSelects)
        {
            $html.='
    <div class="col-12">
        <div class="form-group">
            <label for="">Se rendre à l\'inscription suivante :</label>

            <select id="slider-num_'.$this->getName().'" >
                '.$this->makeSelect().'
            </select>
        </div>
    </div>
';

        }        
    $html.='
</div>  
';
        $this->addHTML($html);
    }
}