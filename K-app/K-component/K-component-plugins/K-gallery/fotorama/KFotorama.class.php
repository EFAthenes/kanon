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
 * Description of KFotorama
 *
 * @author Mulot Louis
 */
class KFotorama extends KComponent
{
    private ?DivClassComponent $div=null;
    final function __construct(string $className="")
    {
        parent::__construct();
        $this->setName($className);
        
        $layout=KApp::getInstance()->getLayout();      
        $layout->addCSSFileToBuffer(__DIR__."/css/fotorama.css");
        $layout->addJsFileToBuffer(__DIR__."/js/fotorama.js"); 
        
        $this->setNone();
        //$this->setName($className);
        $this->div = new DivClassComponent($this->getName()."_fotorama");
        parent::addComponent($this->div);       
        $this->scriptDivFotorama();
        $this->cssDivFotorama();
    }   
    
    public function addComponent(\KComponent $component): \KComponent
    {
        $this->div->addComponent($component);
        return $this;
    }
    
    public function addImage(string $url_image,string $label,string $url_image_big="",string $link="") : void
    {
        $caption="";
        if(!empty($link))
        {
            $caption="<a target='_blank' href='".$link."'>'".htmlentities($label)."'</a>";
        }
        else
        {
            $caption=htmlentities($label);
        }
        
        //$tag_img='<img src="'.$url_image.'" data-full="'.$url_image_big.'" data-caption="'.convertDoubleQuotes($caption).'" />';
        $tag_img='<a href="'.$url_image.'" data-full="'.$url_image_big.'" data-caption="'.convertDoubleQuotes($caption).'"></a>';
        
        //'<img src="'.$url_image.'" data-full="'.$url_image_big.'" data-caption="<a target=\'_blank\' href=\''.$publication->makeLinkToCatalogue().'\'>'.$klink->printLabel().'</a>" />'));  
        $this->addComponent(new HTMLComponent($tag_img));  
    }
    
    private function scriptDivFotorama():void
    {
        $script='   
<script>      
$(document).ready(function()
{
    $(".'.$this->div->getName().'").fotorama(
    {
        loop:"true",
        width:"100%",
        height:"300",
        ratio:"4/3",
        maxwidth:"100%",
        nav:"thumbs",
        allowfullscreen:"true",
        //allowfullscreen: "native",
        transition:"slide",
        arrows:"true", 
        click:"false", 
        swipe:"true", 
        trackpad:"true"
    });
});
</script>
';
        parent::addComponent(new HTMLComponent($script));
    }    
    
    private function cssDivFotorama():void
    {
        if(!empty(StyleManager::getInstance()->main_colour))
        {
        $css='
.fotorama__thumb-border {
    border-color:'.StyleManager::getInstance()->main_colour.';
}            
';
         parent::addCssText($css);
        }
    } 
    
    public function getDivComponent() : ?DivClassComponent
    {
        return $this->div;
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        $class=new static("kfoto");
        $class->addImage("https://archimage.efa.gr/image_request_iiif/795459/full/max/0/default.jpg","image 1");
        $class->addImage("https://archimage.efa.gr/image_request_iiif/500618/full/max/0/default.jpg","image 2");
        $class->getDivComponent()->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
        return $class;
    }
}