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
 * Description of KOpenSeaDragon
 *
 * @author Mulot Louis
 */
class KOpenSeaDragon extends KComponent
{
    /**
     * 
     * @param string $name
     * @param array<int,string> $json
     * @param float $visibilityRatio
     * @param bool $initOnDocument
     */
    final function __construct(string $name,array $json, float $visibilityRatio = 0.4,bool $initOnDocument=true)
    {
        parent::__construct();       
        $this->setId();
        $this->setName("openseadragon_".$name);

        if($visibilityRatio <= 0 || $visibilityRatio > 1)
        {
            $visibilityRatio = 0.4;
        }

        $this->addStyleCode("width: 100%; height: 800px; background-color:black");      
        $json_tile_string= implode(",", $json);
        
        $js='
        viewer_'.$this->getName().' = OpenSeadragon({
            id: "'.$this->getName().'",
            prefixUrl: "./img/",

            visibilityRatio:    ' .$visibilityRatio. ',
            constrainDuringPan: true,
            minZoomLevel:       0,
            maxZoomLevel: 10,
            defaultZoomLevel:   0,
            timeout: 90000,

            showFlipControl: true,
            showRotationControl: true,
            showNavigator: true,
            sequenceMode: true,
            //maxTilesPerFrame:0.1,
            ImageLoaderLimit:1,
            tileSize: 1024,
            tileSources: [' . $json_tile_string . ']
        });
';
        
        $css='';
        if(!empty(StyleManager::getInstance()->main_colour))
        {
            $css='
.displayregion
{
    border-color : '.StyleManager::getInstance()->main_colour.' !important;
}
';
        }        
        
        
        if($initOnDocument)
        {
            $layout=KApp::getInstance()->getLayout();
            $layout->addJsFileToBuffer(self::getJSPath(),true);
            $this->addJSTextOnDocumentReady($js);
            $this->addCssText($css);
        }
        else
        {
            $this->addHTML('<script>'.$js.'</script><style>'.$css.'</style>');
        }
        

        //viewer_'.$this->getName().'.navigator.element.style.border = "20px solid rgb(153, 0, 0);";

        /*
         * Options (pas exhaustif) de OpenSeaDragon :
         * 
         * sequenceMode: bool --> défini si plusieurs images à faire défiler
         * 
         * degrees: int --> degré de rotation initiale
         * showRotationControl: bool --> défini si les boutons de rotations sont visibles
         * rotationIncrement: int --> degré de rotation des boutons de rotation
         * gestureSettingsTouch: {pinchRotate: bool} --> ???
         * 
         * visibilityRatio: float entre 0 et 1 --> empeche l'image de (trop) dépasser du cadre 
         * constrainDuringPan: bool --> si true, empeche l'effet rebond de l'image quand elle revient au centre car hors limite
         * 
         * showNavigator: bool --> défini si le navigateur est visible ou non
         * navigatorPosition: css (voir exemples) --> pour placer le navigateur dans le cadre
         * navigatorAutoFade: bool --> determine si le navigateur disparait ou non lorsque la souris n'est plus sur l'image
         * 
         * defaultZoomLevel: int --> Niveau de zoom de base et pour quand on appuie sur le bouton home
         * 
         */        
    }
        
    public static function getJSPath() : string
    {
        return __DIR__."/js/openseadragon.6.0.2.min.js";
        //return __DIR__."/js/openseadragon.5.0.0.min.js";
        //return __DIR__."/js/openseadragon.min.js";
    }    
    
    #[\Override]
    public static function testMe() : ?static
    { 
        //$var = new 
        $json = '{"@context":"http:\/\/iiif.io\/api\/image\/3\/context.json","id":"https:\/\/archimage.efa.gr\/image_request_iiif\/659","type":"ImageService3","protocol":"http:\/\/iiif.io\/api\/image","profile":"level2","width":3071,"height":3105,"seeAlso":[{"id":"\/action.php?r=document_export_xml_ead_public&id=659","type":"Dataset","format":"text\/xml","profile":"https:\/\/archimage.efa.gr\/profiles\/ead"},{"id":"\/action.php?r=document_export_dc_public&id=659","type":"Dataset","format":"text\/xml","profile":"https:\/\/archimage.efa.gr\/profiles\/dc"}],"partOf":[{"id":"\/?r=fiche_publique&id=659","type":"Text"}],"tiles":[{"scaleFactors":[1,2,4],"width":256},{"scaleFactors":[8,16,32],"width":512},{"scaleFactors":[64,128],"width":1024}],"extraFeatures":["canonicalLinkHeader","profileLinkHeader"]}';
        $class=new static("OpenSea",[$json],0.4,true);
        $class->setStyleCode("width:400px;height:400px;");
        return $class;
    }     
    
}