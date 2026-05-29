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
 * Description of KLeafLetMapDrawSecteurs
 *
 * @author Maxime Tueux
 */
class KLeafLetMapDrawSecteurs extends KLeafLetMap
{
    /**
     * 
     * @param string $name
     * @param string|null $name_input
     * @param array<mixed,mixed>|null $geoJSON
     * @param string|null $centroid
     * @param string|null $height
     * @param string|null $center_x
     * @param string|null $center_y
     * @param string|null $zoom
     * @param string|null $max_zoom
     * @param string|null $geo_color
     * @param string|null $geo_weight
     * @param string|null $geo_opacity
     * @param bool $singleMarker
     */
    public function __construct(
        string $name,
        ?string $name_input=null,
        ?array $geoJSON =null,
        ?string $centroid = null,
        ?string $height = null,
        ?string $center_x = null,
        ?string $center_y = null,
        ?string $zoom = null,
        ?string $max_zoom = null,
        ?string $geo_color = null,
        ?string $geo_weight = null,
        ?string $geo_opacity = null,
        bool $singleMarker = false
    ) {
        parent::__construct($name, $height, $center_x, $center_y, $zoom, $max_zoom);

        $edit_string = "false";
        if (!is_null($name_input)) {
            $edit_string = "true";
            $this->addHtmlComponent('<input id="' . $name_input . '" class="form-control" type="hidden" value="TODO" name="' . $name_input . '" type="text">');
        }

        $js = "";
        if (empty($centroid)) {
            $centroid = 'null';
        }

        if (is_null($geo_color)) {
            $geo_color = "#1d4877";
            if (!empty(ParamManager::getInstance()->get("LEAFLET_GEO_COLOR"))) {
                $geo_color = ParamManager::getInstance()->get("LEAFLET_GEO_COLOR");
            }
        }
        if (is_null($geo_weight)) {
            $geo_weight = "5";
            if (!empty(ParamManager::getInstance()->get("LEAFLET_GEO_WEIGHT"))) {
                $geo_weight = ParamManager::getInstance()->get("LEAFLET_GEO_WEIGHT");
            }
        }
        if (is_null($geo_opacity)) {
            $geo_opacity = "0.20";
            if (!empty(ParamManager::getInstance()->get("LEAFLET_GEO_OPACITY"))) {
                $geo_opacity = ParamManager::getInstance()->get("LEAFLET_GEO_OPACITY");
            }
        }

        // TODO: limit to 5 levels
        $colors = $this->makeColors();  
        $weights = $this->makeWeights();
        $opacity = $this->makeOpacity();
        
        $js2='';
        $json="";
        if(is_array($geoJSON)&&count($geoJSON)&& is_array($geoJSON[0]))
        {
            $json=$geoJSON[0][0];
            for ($i = 1; $i < count($geoJSON); $i++) 
            {
                $js2 .= "loadJSONSecteur('" . $geoJSON[$i][0] . "', 'null','" . $colors[($geoJSON[$i][1])%4] . "','" . $weights[($geoJSON[$i][1])%4] . "','" . $opacity[($geoJSON[$i][1])%4] . "');";
            }            
        }

        $js = "initMapDrawForm('" .$json. "', '" . $centroid . "','" . $name_input . "'," . $edit_string . ",'" . $colors[0] . "','" . $geo_weight . "','" . $geo_opacity . "', " . $singleMarker . ");";
        $this->addJSTextOnDocumentReady($js.$js2);
        $this->initCustomStyles();
    }
    
    public function addWmsBaseMap(string $layerName,string $urlWms,string $url_sig) : void
    {
        $js="addBaseMapWmsSite('".addslashes($urlWms)."','".addslashes($layerName)."','".addslashes($url_sig)."');";
        $this->addJSTextOnDocumentReady($js);
    }
    #[\Override]
    public static function testMe() : ?static
    { 
        return null;
//        $class=new static("kleafletMapDraw","kleafletInput",null,"");
//        $class->addStyleCode("background-color:#FFFFFF;height:400px;width:400px;");
//        return $class;
    }      
    
}