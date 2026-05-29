<?php
/**
 * Description of KLeafLetMapDraw
 *
 * @author Mulot Louis
 * // Update with https://www.geoman.io/demo
 */
class KLeafLetMapDraw extends KLeafLetMap
{
    public function __construct(
            string $name,
            ?string $name_input,
            string $json,
            ?string $centroid=null,
            ?string $height=null,
            ?string $center_x=null,
            ?string $center_y=null,
            ?string $zoom=null,
            ?string $max_zoom=null,
            ?string $geo_color=null,
            ?string $geo_weight=null,
            ?string $geo_opacity=null,
            bool $singleMarker = false,
            ?string $defaultBounds=null,
            )
    {
        parent::__construct($name,$height,$center_x,$center_y,$zoom,$max_zoom);
              
        $edit_string="false";
        if(!is_null($name_input))
        {
            $edit_string="true";
            $this->addHtmlComponent('<input id="'.$name_input.'" class="form-control" type="hidden" value="'.FormComponent::inputString("".$json).'" name="'.$name_input.'" type="text">');
        }
        
        $js="";
        if(empty($centroid)) 
        {   
            $centroid='null';
        }

        
        if(is_null($geo_color))
        {
            $geo_color="#6B82AC";
            if(!empty(ParamManager::getInstance()->get("LEAFLET_GEO_COLOR")))
            {
                $geo_color=ParamManager::getInstance()->get("LEAFLET_GEO_COLOR");
            }
        }
         if(is_null($geo_weight))
        {
            $geo_weight="5";
            if(!empty(ParamManager::getInstance()->get("LEAFLET_GEO_WEIGHT")))
            {
                $geo_weight=ParamManager::getInstance()->get("LEAFLET_GEO_WEIGHT");
            }            
        }
        if(is_null($geo_opacity))
        {
            $geo_opacity="0.20";
            if(!empty(ParamManager::getInstance()->get("LEAFLET_GEO_OPACITY")))
            {
                $geo_opacity=ParamManager::getInstance()->get("LEAFLET_GEO_OPACITY");
            }
        }

        //$this->addHtmlComponent($json);
        $js = "initMapDrawForm('" . $json . "', '" . $centroid . "','".$name_input."',".$edit_string.",'".$geo_color."','".$geo_weight."','".$geo_opacity."', ". $singleMarker ." );";
        if(!empty($defaultBounds))
        {
            $js.='
const bounds = L.polygon('.$defaultBounds.').getBounds();
mymap.fitBounds(bounds);               
';  
        }
        $this->addJSTextOnDocumentReady($js);
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        return null;
//        $class=new static("kleafletMapDraw","kleafletInput","");
//        return $class;
    }   
}