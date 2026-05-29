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
 * Description of KMiradorViewer
 *
 * @author Mulot Louis
 */
class KMiradorViewer extends DivIdComponent
{
    /**
     * 
     * @param string $name
     * @param string $url
     * @param int $canvasIndex
     * @param bool $initOnDocument
     */
    private static bool $debug=false;
    final function __construct(string $name,string $url, int $canvasIndex = 2,bool $initOnDocument=true)
    {
        parent::__construct(self::makeName($name)); 
        
        $this->addClassName("justify-content-center");

        $url=str_replace("&amp;","&", $url);
        
        $js='
var mirador = Mirador.viewer({
  "id": "'.$this->getName().'",
  "manifests": {
    "'.$url.'": {
      "provider": "Archimage"
    }
  },
  "language": "fr",
  "window": {
    allowClose: false,
    allowWindowSideBar:true,
    sideBarPanel: "info",
    defaultView: "single",
    sideBarOpen: true,
    panels: { // Configure which panels are visible in WindowSideBarButtons
      info: true,
      attribution: false,
      canvas: false,
      annotations: false,
      search: false,
      layers: false,
    },
  },
  "windows": [
    {
        "imageToolsEnabled": true,
        "imageToolsOpen": true,
        "allowFullScreen": false,
        "loadedManifest": "'.$url.'",
        "canvasIndex": '.$canvasIndex.'
    }
  ],
  "workspaceControlPanel": {
     enabled: false
   } 
});
';
   //https://github.com/ProjectMirador/mirador/blob/main/src/config/settings.js
        if($initOnDocument)
        {
            $layout=KApp::getInstance()->getLayout();
            $layout->addJsFileToBuffer(self::getJSPath(),true);
            $this->addJSTextOnDocumentReady($js);
            $this->addCssText(self::makeCssForName($name));
        }
        else
        {
            $this->addHTML('<script>'.$js.'</script>');
        }   
    }
        
    public static function getJSPath() : string
    {
        if(self::$debug)
        {
            return __DIR__."/js/mirador.js";
        }
        return __DIR__."/js/mirador.min.js";
    }    
    
    public static function makeCssForName(string $name) : string
    {
        $css="#".self::makeName($name)." {width: 100%; height: 800px; position: relative;}";
        return $css;
    }  
    
    public static function makeName(string $name) : string
    {
        return "mirador_".$name;
    }

    
//    #[\Override]
//    public static function testMe() : ?static
//    { 
//        //string $name,string $url, int $canvasIndex = 2,bool $initOnDocument=true
//        $class=new static("Mirador","https://archimage.efa.gr/action.php?r=iiif_json_manifest&id=659",2,true);
//        $class->setStyleCode("width:600px;height:400px;");
//        return $class;
//    } 
}