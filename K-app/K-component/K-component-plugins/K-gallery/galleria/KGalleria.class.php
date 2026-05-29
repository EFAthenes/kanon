<?php
/**
 * Description of KGalleria
 *
 * @author Mulot Louis
 */
class KGalleria extends KComponent
{
    private ?DivIdComponent $div=null;
    function __construct(string $className)
    {
        parent::__construct();
        
        $layout=KApp::getInstance()->getLayout();      
        $layout->addCSSFileToBuffer(__DIR__."/css/galleria.miniml.min.css");
        $layout->addJsFileToBuffer(__DIR__."/js/galleria.min.js"); 
        
        $this->setNone();
        $this->setName($className);
        $this->div = new DivIdComponent($this->getName()."_galleria");
        parent::addComponent($this->div);       
        $this->scriptDivFotorama();
        
        $this->setCssText('
           /* Demo styles */
            html,body{background:#222;margin:0;}
            body{border-top:4px solid #000;}
            .content{color:#777;font:12px/1.4 "helvetica neue",arial,sans-serif;width:300px;margin:20px auto;}
            h1{font-size:12px;font-weight:normal;color:#ddd;margin:0;}
            p{margin:0 0 20px}
            a {color:#22BCB9;text-decoration:none;}
            .galleria-info-description a {color: #bbb;}
            .cred{margin-top:20px;font-size:11px;}

            /* This rule is read by Galleria to define the gallery height: */
            #galleria{height:320px}            

');
    }   
    
    public function addComponent(\KComponent $component): \KComponent
    {
        $this->div->addComponent($component);
        return $this;
    }
    
    public function addImage(string $url_image,string $label,string $url_image_big="",string $link="") : void
    {
//        $caption="";
//        if(!empty($link))
//        {
//            $caption="<a target=\'_blank\' href=\''.$link.'\'>'".htmlentities($label)."'</a>";
//        }
//        else
//        {
//            $caption=htmlentities($label);
//        }
        
        $tag_img='
<a href="'.$url_image.'"><img src="'.$url_image.'" data-title="'.convertDoubleQuotes($label).'" /></a>';
        
        //'<img src="'.$url_image.'" data-full="'.$url_image_big.'" data-caption="<a target=\'_blank\' href=\''.$publication->makeLinkToCatalogue().'\'>'.$klink->printLabel().'</a>" />'));  
        $this->addComponent(new HTMLComponent($tag_img));  
    }
    
    private function scriptDivFotorama():void
    {
        $script='   
<script>      
$(document).ready(function()
{
    Galleria.configure({
//    //	transition: "fade",
//    //	imageCrop: true
//        responsive: true,
//        fullscreenDoubleTap:true,
//        debug:true,
    });
    // Initialize Galleria
    Galleria.run("#'.$this->div->getName().'");
});
</script>
';
        parent::addComponent(new HTMLComponent($script));
    }    
    
//    #[\Override]
//    public static function testMe() : ?static
//    { 
//        $class=new static("kgalleria");
//        $class->addImage("https://archimage.efa.gr/image_request_iiif/795459/full/max/0/default.jpg","image 1");
//        $class->addImage("https://archimage.efa.gr/image_request_iiif/500618/full/max/0/default.jpg","image 2");
//        $class->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
//        return $class;
//    }    
    
}