<?php
/**
 * Description of KLightGallery
 *
 * @author Mulot Louis
 */
class KLightGallery extends KComponent
{
    private ?DivIdComponent $div=null;
    private string $arrayItems='';
    function __construct(string $className)
    {
        parent::__construct();
        
        $layout=KApp::getInstance()->getLayout();      
        $layout->addCSSFileToBuffer(__DIR__."/css/lightgallery-bundle.min.css");
        $layout->addJsFileToBuffer(__DIR__."/js/lightgallery.min.js"); 
        
        $this->setNone();
        $this->setName($className);
        $this->div = new DivIdComponent($this->getName()."_lightgallery");
        $this->div->setClassName("inline-gallery-container");
        parent::addComponent($this->div);      
        $this->setCssText('
.inline-gallery-container {
    width: 100%;

    // set 60% height
    height: 400px;
 /*  padding-bottom: 100%; */
} 

.lg-icon {
  font-family: lg !important;
  speak: never;
  font-style: normal;
  font-weight: 400;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

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
        
        if(empty($url_image_big))
        {
            $url_image_big=$url_image;
        }
            
        if(!empty($this->arrayItems))
        {
            $this->arrayItems.=',';
        }
        $this->arrayItems.='
        {
            src: "'.$url_image_big.'",
            thumb: "'.$url_image.'",
            subHtml: `<div class="lightGallery-captions">
                <h4>Caption 1</h4>
                <p>'.$label.'</p>
            </div>`,
        }';
        
    }
    
    public function draw(): string
    {
        return parent::draw().$this->makeScript();
    }
    
    private function makeScript() : string
    {
        $script='   
<script>      
$(document).ready(function()
{
    const lgContainer = document.getElementById("'.$this->div->getName().'");
    const inlineGallery = lightGallery(lgContainer, {
        container: lgContainer,
        dynamic: true,
        // Turn off hash plugin in case if you are using it
        // as we don\'t want to change the url on slide change
        hash: false,
        // Do not allow users to close the gallery
        closable: false,
        // Add maximize icon to enlarge the gallery
        showMaximizeIcon: true,
        // Append caption inside the slide item
        // to apply some animation for the captions (Optional)
        appendSubHtmlTo: ".lg-item",
        // Delay slide transition to complete captions animations
        // before navigating to different slides (Optional)
        // You can find caption animation demo on the captions demo page
        slideDelay: 400,
        dynamicEl: [
            '.$this->arrayItems.'
        ],
        plugins: [lgZoom, lgThumbnail],
    });

    // Since we are using dynamic mode, we need to programmatically open lightGallery
    inlineGallery.openGallery();
});
</script>
';
        return $script;
    }    
    
    public function getDivComponent() : ?DivIdComponent
    {
        return $this->div;
    }
    
//        #[\Override]
//    public static function testMe() : ?static
//    { 
//        $class=new static("KLightGallery");
//        $class->addImage("https://archimage.efa.gr/image_request_iiif/795459/full/max/0/default.jpg","image 1");
//        $class->addImage("https://archimage.efa.gr/image_request_iiif/500618/full/max/0/default.jpg","image 2");
//        $class->getDivComponent()->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
//        return $class;
//    }    
    
}