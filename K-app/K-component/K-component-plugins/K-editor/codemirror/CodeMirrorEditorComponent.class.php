<?php
declare(strict_types=1);
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
/**
 * Description of CodeMirrorEditorComponent
 *
 * Based on Codemirror
 * @author Mulot Louis
 */
class CodeMirrorEditorComponent extends EmptyComponent
{
    private string $version = "5.65";
    private string $content = "";
    /**
     * 
     * @var array<int,string>
     */
    private array $modesAvailable=[];
    private string $mode="";
    private string $var_js_name='';
    private int $height_px=300;
    
    public const string CSS="css";
    public const string PHP="php";
    public const string JAVASCRIPT="javascript";
    public const string HTML="htmlmixed";
    public const string XML="xml";

    final public function __construct(string $div_id,?string $content="",?string $mode="")
    {
        parent::__construct();
        $this->initModes();
        
        $this->content=htmlentities(strval($content));
        $this->setMode($mode);  

        $this->setIdAndName($div_id);

        $this->var_js_name="editor_".$this->getIdName();

        $this->setNone();   
        $this->addHTML('<textarea id="'.$this->getIdName().'">'.$this->content.'</textarea>');

        //JS MAIN
        $js = __DIR__."/".$this->version."/js/codemirror.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js);
        
        // Extension
        $js2 = __DIR__."/".$this->version."/js/dialog.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/show-hint.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);           
        $js2 = __DIR__."/".$this->version."/js/css-hint.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/html-hint.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);        
        $js2 = __DIR__."/".$this->version."/js/javascript-hint.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);   
        $js2 = __DIR__."/".$this->version."/js/mark-selection.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);           
        $js2 = __DIR__."/".$this->version."/js/match-highlighter.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);        
        $js2 = __DIR__."/".$this->version."/js/search.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);   
        $js2 = __DIR__."/".$this->version."/js/searchcursor.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);          
        $js2 = __DIR__."/".$this->version."/js/xml-hint.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);  
        $js2 = __DIR__."/".$this->version."/js/matchbrackets.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);           
        $js2 = __DIR__."/".$this->version."/js/xml-fold.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);          
        $js2 = __DIR__."/".$this->version."/js/matchtags.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);  
        $js2 = __DIR__."/".$this->version."/js/closetag.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2); 
        
        // Modes
        $js2 = __DIR__."/".$this->version."/js/javascript.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/css.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/css.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/htmlmixed.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/clike.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/php.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/xml.min.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);
        $js2 = __DIR__."/".$this->version."/js/markdown.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2);        
     
        //CSS
        $css = __DIR__."/".$this->version."/css/codemirror.min.css";
        KApp::getInstance()->getLayout()->addCSSFileToBuffer($css);
        $css = __DIR__."/".$this->version."/css/dialog.css";
        KApp::getInstance()->getLayout()->addCSSFileToBuffer($css);
        $css = __DIR__."/".$this->version."/css/hint.css";
        KApp::getInstance()->getLayout()->addCSSFileToBuffer($css);
    }
    
    private function initModes() : void
    {
        $this->mode=self::HTML;
        $this->modesAvailable[]=self::CSS;
        $this->modesAvailable[]=self::HTML;
        $this->modesAvailable[]=self::JAVASCRIPT;
        $this->modesAvailable[]=self::PHP;
        $this->modesAvailable[]=self::XML;
    }
    
    public function setMode(mixed $mode) : bool
    {
        if(!empty($mode))
        {
            if(in_array($mode,$this->modesAvailable))
            {
                $this->mode=$mode;
                return true;
            }
        }
        return false;
    }

    public function getVarJsName() : string
    {
        return $this->var_js_name; 
    }
    
    private function makeJS() : string
    {
        $js='
<script>
    const '.$this->var_js_name.' = CodeMirror.fromTextArea(document.getElementById("'.$this->getIdName().'"), {
      lineNumbers: true,
      extraKeys: {"Ctrl-Space": "autocomplete"},
      matchTags: {bothTags: true},
      autoCloseTags: true,
      matchBrackets: true,
      mode: "'.$this->mode.'",
    });
    '.$this->var_js_name.'.setSize(null,'.$this->height_px.');
</script>
';  
        return $js;
    }
    
    public function setHeight(int $pixels) : void
    {
        $this->height_px=$pixels;
    }
    
    #[\Override]
    public function draw(): string
    {
        return parent::draw().$this->makeJS();
    }
    
        #[\Override]
    public static function testMe(): ?static
    {
        $code='
<style>
.custom-popup {
  width: auto !important;
  max-height: none !important;
  overflow: visible !important;
}
</style>

<div class="custom-popup">
    <h5>{{nom}}</h5>
    <strong>Nombre de documents associés :</strong> {{document_count}}<br>
    <strong>Niveau :</strong> {{niveau}}<br>
    <strong>Lien :</strong> <a href="{{content_link}}" target="_blank">cliquez ici</a>
    <br>
</div>
<?php
$a=[1,2,3,4,5];
foreach ($a as $v) 
{
    echo $v."<br />";
}
?>
';    
            
        $class=new static("CodeMirrorEditorComponent",$code,self::PHP);
       // return $class;
        return null;
    }  
}