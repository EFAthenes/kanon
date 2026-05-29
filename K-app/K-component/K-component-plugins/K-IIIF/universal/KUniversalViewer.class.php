<?php
/**
 * Description of KUniversalViewer
 *
 * @author Mulot Louis
 */
class KUniversalViewer extends DivIdComponent
{
    /**
     * 
     * @param string $name
     * @param string $url
     */
    function __construct(string $name,string $url)//, int $canvasIndex = 2,bool $initOnDocument=true)
    {
        parent::__construct(self::makeName($name));
        $this->setClassName("uv");
        
        $js='

    const data = {
        manifest: "'.$url.'",
        embedded: true // needed for codesandbox frame
    };

    var uv = UV.init("'.$this->getName().'", data);
';
        $this->addJS($js);
        
//        $css='';
//        
//        
        
//        $jsS='
//<link
//      rel="stylesheet"
//      href="https://cdn.jsdelivr.net/npm/universalviewer@4.0.0/dist/uv.css"
//    />            
//<script
//      type="application/javascript"
//      src="https://cdn.jsdelivr.net/npm/universalviewer@4.0.0/dist/umd/UV.js"></script>   
//';
        
        $this->setCssText('#"'.$this->getName().'" {
        width: 924px;
        height: 668px;
        
      }');
        
//        $this->addHTML($jsS);
//      
             
//        if($initOnDocument)
//        {
//            //$layout=KApp::getInstance()->getLayout();
//            //$layout->addJsFileToBuffer(self::getJSPath());//,true);
//            $this->addJSTextOnDocumentReady($js);
//            //$this->addCssText(self::makeCssForName($this->getName()));
//        }
//        else
//        {
//            $this->addJSTextOnDocumentReady($js);
//            //$this->addHTML('<script>'.$js.'</script>');
//        } 
    }
        
    public static function getJSPath() : string
    {
        return __DIR__."/js/universalviewer.min.js";
    } 
    public static function getCSSPath() : string
    {
        return __DIR__."/css/uv.css";
    } 
    
    public static function makeCssForName(string $name) : string
    {
        $css="#".self::makeName($name)." {width: 1200px; height: 800px; position: relative;}";
        return $css;
    }  
    
    public static function makeName(string $name) : string
    {
        return "uv_".$name;
    }
    
    public function draw(): string
    {
        return '<div id="'.$this->getName().'" class="uv"> </div>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    '.$this->getJS().'
});
</script>
         ';
    }
}