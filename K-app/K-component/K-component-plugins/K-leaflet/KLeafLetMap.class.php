<?php

/**
 * Description of KLeafLetMap
 *
 * @author Mulot Louis
 */
class KLeafLetMap extends KComponent
{
    public const string DEFAULT_COLOR="#0000FF";
    public const string DEFAULT_WEIGTH="5";
    public const string DEFAULT_OPACITY="0.20";
    public const int MAX_LAYERS=6;
    public function __construct(
        string $name,
        ?string $height = null,
        ?string $center_x = null,
        ?string $center_y = null,
        ?string $zoom = null,
        ?string $max_zoom = null
    ) {
        parent::__construct();
        $this->setNone();
        $this->addAllLeafLetFiles();
        $this->addJSText($this->createIcon());
        $this->addHtmlComponent('<div id="' . $name . '"></div>');

        if (is_null($height)) {
            $height = '750';
        }
        //27.5, 32.43 //[37.39, 25.26],
        if (is_null($center_x)) {
            $center_x = '37.39';
        }

        if (is_null($center_y)) {
            $center_y = '25.26';
        }

        if (is_null($zoom)) {
            $zoom = '6';
        }

        if (is_null($max_zoom)) {
            $max_zoom = '20';
        }
        

        $js = '      
            $("#' . $name . '").height(' . $height . ');
            initLeafLetMap(' . $name . ',' . $center_x . ',' . $center_y . ',' . $zoom . ',' . $max_zoom . ');';
        
        $this->addJSTextOnDocumentReady($js);
    }

    private function addAllLeafLetFiles() : void
    {
        $layout = KApp::getInstance()->getLayout();
        $layout->addCSSFileToBuffer(__DIR__ . "/css/leaflet.css");
        $layout->addCSSFileToBuffer(__DIR__ . "/css/leaflet.archimage.css");
        $layout->addCSSFileToBuffer(__DIR__ . "/geoman/leaflet-geoman.css");
        $layout->addCSSFileToBuffer(__DIR__ . "/css/MarkerCluster.Default.css");
        $layout->addJsFileToBuffer(__DIR__ . "/js/leaflet.js");
        $layout->addJsFileToBuffer(__DIR__ . "/geoman/leaflet-geoman.min.js");
        $layout->addJsFileToBuffer(__DIR__ . "/js/leaflet.markercluster.js");
        $layout->addJsFileToBuffer(__DIR__ . "/js/mapLeaflet.js");
        $layout->addJsFileToBuffer(__DIR__ . "/js/leaflet.draw.init.js");
    }

  private function createIcon(): string
  {
    $js = '
        const svgBlueIcon = L.divIcon({
            html: `' . KLeafLetMap::getSvgBlueIcon() . '`,
            className: "",
            iconSize: [18, 30],
            iconAnchor: [17, 50],
        });

        const svgYellowIcon = L.divIcon({
            html: `' . KLeafLetMap::getSvgYellowIcon() . '`,
            className: "",
            iconSize: [18, 30],
            iconAnchor: [17, 50],
        });

        const svgOrangeIcon = L.divIcon({
            html: `' . KLeafLetMap::getSvgOrangeIcon() . '`,
            className: "",
            iconSize: [18, 30],
            iconAnchor: [17, 50],
        });

        const svgGreenIcon = L.divIcon({
            html: `' . KLeafLetMap::getSvgGreenIcon() . '`,
            className: "",
            iconSize: [18, 30],
            iconAnchor: [17, 50],
        });
        
        const svgVioletIcon = L.divIcon({
            html: `' . KLeafLetMap::getSvgVioletIcon() . '`,
            className: "",
            iconSize: [18, 30],
            iconAnchor: [17, 50],
        });
        
        const svgPaleBlueIcon = L.divIcon({
            html: `' . KLeafLetMap::getSvgPaleBlueIcon() . '`,
            className: "",
            iconSize: [18, 30],
            iconAnchor: [17, 50],
        });        
';
    return $js;
  }

  public static function getSvgBlueIcon() : string
  {
    return '
            <svg
  width="35"
  height="51"
  viewBox="0 0 384 512"
  version="1.1"
  preserveAspectRatio="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <path
        fill="#7195E2"
        d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
</svg>
            ';
  }
  
    public static function getSvgPaleBlueIcon() : string
  {
    return '
            <svg
  width="35"
  height="51"
  viewBox="0 0 384 512"
  version="1.1"
  preserveAspectRatio="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <path
        fill="#2ac1db"
        d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
</svg>
            ';
  }
  
    public static function getSvgVioletIcon() : string
  {
    return '
            <svg
  width="35"
  height="51"
  viewBox="0 0 384 512"
  version="1.1"
  preserveAspectRatio="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <path
        fill="#6f23ba"
        d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
</svg>
            ';
  }  

  public static function getSvgGreenIcon() : string
  {
    return '
            <svg
  width="35"
  height="51"
  viewBox="0 0 384 512"
  version="1.1"
  preserveAspectRatio="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <path
        fill="#6FD066"
        d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
</svg>
            ';
  }

  public static function getSvgOrangeIcon() : string
  {
    return '
            <svg
  width="35"
  height="51"
  viewBox="0 0 384 512"
  version="1.1"
  preserveAspectRatio="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <path
        fill="#FF8000"
        d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
</svg>
            ';
  }

  public static function getSvgYellowIcon() : string
  {
      
    return '
            <svg
  width="35"
  height="51"
  viewBox="0 0 384 512"
  version="1.1"
  preserveAspectRatio="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <path
        fill="#FFDC4F"
        d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
</svg>
            ';
  }
  

    /**
     * 
     * @return array<int,string>
     */
    protected function makeColors() : array
    {   
        $colors=[];
        for($i=0;$i<6;$i++)
        {
            $value=ParamManager::getInstance()->get("LEAFLET_SECTEUR_LVL_".$i);
            if(!empty($value))
            {
                $colors[]=$value;
            }
            else
            {
                $colors[]=self::DEFAULT_COLOR;
            }
        }
        return $colors;
    }
    /**
     * 
     * @return array<int,string>
     */
    protected function makeWeights() : array
    {
        $weigths=[];
        for($i=0;$i<6;$i++)
        {
            $value=ParamManager::getInstance()->get("LEAFLET_SECTEUR_LVL_".$i."_W");
            if(!empty($value))
            {
                $weigths[]=$value;
            }
            else
            {
                $weigths[]=self::DEFAULT_WEIGTH;
            }
        }
        return $weigths;      
    }
    /**
     * 
     * @return array<int,string>
     */
    protected function makeOpacity() : array
    {
        $opacities=[];
        for($i=0;$i<self::MAX_LAYERS;$i++)
        {
            $value=ParamManager::getInstance()->get("LEAFLET_SECTEUR_LVL_".$i."_O");
            if(!empty($value))
            {
                $opacities[]=$value;
            }
            else
            {
                $opacities[]=self::DEFAULT_OPACITY;
            }
        }
        return $opacities;      
    }  
    
    public function initCustomStyles() : void
    {
        $colors=$this->makeColors();
        $weights=$this->makeWeights();
        $opacities=$this->makeOpacity();
        $js='';
        for($i=0;$i<self::MAX_LAYERS;$i++)
        {
           $js.='
myStyle.set('.$i.', {"color": "'.$colors[$i].'","weight":'.floatval($weights[$i]).',"opacity":"'.$opacities[$i].'"});               
'; 
        }  
        $this->addJSTextOnDocumentReady($js);     
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        /* @phpstan-ignore-next-line */
        $class=new static("kleaflet","400");
        
        $js='
        let site_1738_center = L.geoJSON({"type":"Point","coordinates":[22.501151996666877,38.481384494292655]},
        { 
            pointToLayer: function (feature, latlng) 
            {
            let marker = L.marker(latlng, 
                { 
                    icon: svgBlueIcon,
                    zIndexOffset: 4231
                });
                let popup = 
                    L.popup({
                        offset: [0,-10],
                        })
                    .setContent(                       
                        "<input type=\"hidden\" name=\"autocomplete_field\" value=\"[Delphes (Lieu)]\" />" +
                        "<h4 style=\"text-align: center;\">Title</h4>" +
                        "<div class=\"content-leaflet-popup\">content </div> "
                    );
                marker.bindPopup(popup);   
                 return marker;
        }}).addTo(mymap);
       
';
        $class->addJSTextOnDocumentReady($js);
        return $class;
    }    
  
}
