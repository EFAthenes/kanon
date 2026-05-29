<?php
/**
 * Description of UiFileManager
 *
 * @author Mulot Louis
 */
class UiFileManager extends KComponent
{
    function __construct(KURL $kurl,string $name)
    {
        parent::__construct();
        $this->setNone(); 
        
        $layout=KApp::getInstance()->getLayout();      
        $layout->addJsFileToBuffer(__DIR__."/js/iframeResizer.js");         

        $html='
<iframe id="the_iframe_'.$name.'" width="100%;" scrolling="no" src="'.$kurl->printURLWithoutAmp().'" style="border: 1px solid #ebebeb;" > </iframe> 
<script>
iFrameResize({ log: false }, "#the_iframe_'.$name.'");
</script>
';
        $component=new HTMLComponent($html);  
        $this->addComponent($component);
    }
}