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
 * Description of MonacoEditorComponent
 *
 * @author Mulot Louis
 */
class MonacoEditorComponent extends KComponent
{
    private string $version="0.52";
    final public function __construct(string $label)
    {      
        parent::__construct();
        $this->setName($label);
        $this->addHTML('<div id="container" style="width: 800px; height: 600px; border: 1px solid grey"></div>');
        
        
        
//        $layout=KApp::getInstance()->getLayout();
//        $layout->addJsFileToBuffer(self::getJSPath(),true);
//        $this->addJSTextOnDocumentReady($js);
        
        $js=__DIR__."/".$this->version."/loader.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js,true);
                
        
        //$this->addJSFile($js);
        $js2=__DIR__."/".$this->version."/editor/editor.main.js";
        KApp::getInstance()->getLayout()->addJsFileToBuffer($js2,true);
        //$this->addJSFile(__DIR__.$this->version."/min/vs/editor/editor.main.js");
        
        $css=__DIR__."/".$this->version."/editor/editor.main.css";
        KApp::getInstance()->getLayout()->addCSSFileToBuffer($css);
        
        $this->addHTML('
<script>
    var editor = monaco.editor.create(document.getElementById("container"), {
        value: [\'function x() {\', \'\tconsole.log("Hello world!");\', \'}\'].join(\'\n\'),
        language: "javascript"
    });
</script>
');                                
                
    }
}