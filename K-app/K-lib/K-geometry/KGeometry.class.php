<?php
require_once 'geoPHP/geoPHP.inc';
class KGeometry
{
    private mixed $geometry = null;
    private mixed $WKB;
    private mixed $EWKT;
    private mixed $EWKB;
    private mixed $srid;
    
    /**
     * 
     * @var array<int,string>
     */
    private static array $WKT_KEYWORDS=['MULTIPOLYGON','POLYGON','POINT'];

    public static function WKTToEWKB(string $WKT) :?string
    {
        //KDebugger::getInstance()->dump($WKT,'KGeometry WKT');
        $wkt_reader = new WKT();
        $geometry = $wkt_reader->read($WKT);

        //KDebugger::getInstance()->dump($geometry,'KGeometry geometry');
        $ewkb_writer = new EWKB();
        $EWKB = $ewkb_writer->write($geometry);

        //KDebugger::getInstance()->dump($EWKB);

        return $EWKB;
    }

    public static function GeoJsonToWKT(?string $geoJson) : ?string
    {
        if(is_null($geoJson) || empty($geoJson))
        {
            return null;
        }
        
        $geoJson_reader = new GeoJSON();
        try 
        {
            $geometry = $geoJson_reader->read($geoJson);
            if($geometry instanceof Geometry  )
            {
                $wkt_writer = new WKT();
                return $wkt_writer->write($geometry);
            }
        }
        catch(Exception $e) 
        {
            return null;//"GEOMETRYCOLLECTION EMPTY";
        }
        return null;
    }
    
    static function GeoJsonToKML(?string $geoJson) : ?string
    {
        if(is_null($geoJson) || empty($geoJson))
        {
            return null;
        }
        
        $geoJson_reader = new GeoJSON();
        try 
        {
            $geometry = $geoJson_reader->read($geoJson);
            if($geometry instanceof Geometry  )
            {
                $kml_writer = new KML();
                return $kml_writer->write($geometry);
            }
        }
        catch(Exception $e) 
        {
            return null;//"GEOMETRYCOLLECTION EMPTY";
        }
        return null;
    }  
    
    static function GeoJsonToWFS(?string $geoJson,XMLWriter $xml) : void
    {
        if(is_null($geoJson) || empty($geoJson) || !json_validate($geoJson))
        {
            return;
        }
          
        $json=json_decode($geoJson);
        
        if(property_exists($json,"type")&&property_exists($json,"coordinates")&& is_array($json->coordinates)) 
        {
            switch($json->type)
            {
                case 'Point': 
                    $xml->startElement("gml:Point");
                    $xml->writeAttribute("srsName","http://www.opengis.net/gml/srs/epsg.xml#4326");
                        $xml->startElement("gml:coordinates");
                        $xml->writeAttribute("action","myaction");
                        $xml->text($json->coordinates[0] . ' ' . $json->coordinates[1]);
                        $xml->endElement();
                    $xml->endElement();    
                    break;
                case 'Polygon':
                    self::makeGMLPolygon($xml,$json->coordinates);
                    break;
                case 'MultiPolygon':
                    $xml->startElement("gml:geometryMember");
                    $xml->writeAttribute("srsName","http://www.opengis.net/gml/srs/epsg.xml#4326");
                        $xml->startElement("gml:MultiGeometry");
                        foreach($json->coordinates as $coordinate)
                        {
                            self::makeGMLPolygon($xml,$coordinate);
                        }
                        $xml->endElement();
                    $xml->endElement();    
                    break; 
                default:
                        print "IDK what to do with {$json->type}\n";
                        exit;
            }
        }
    } 
    
    /**
     * 
     * @param XMLWriter $xml
     * @param array<mixed,mixed> $coordinate
     * @return void
     */
    private static function makeGMLPolygon(XMLWriter $xml,array $coordinate ) : void
    {
        $exterior = array_shift($coordinate);
        
        $xml->startElement("gml:Polygon");
        $xml->writeAttribute("srsName","http://www.opengis.net/gml/srs/epsg.xml#4326");
            $xml->startElement("gml:exterior");
                $xml->startElement("gml:LinearRing");
                    $xml->startElement("gml:posList");
                        $xml->text(self::coordsToPosList($exterior));
                    $xml->endElement();
                $xml->endElement();
            $xml->endElement();

        foreach( $coordinate as $poly ) 
        {
            $xml->startElement("gml:surfaceMember");
                $xml->startElement("gml:Polygon");
                    $xml->startElement("gml:interior");
                        $xml->startElement("gml:LinearRing");
                            $xml->startElement("gml:posList");
                            $interior = array_shift($poly);
                            $xml->text(self::coordsToPosList($interior));
                            $xml->endElement();
                        $xml->endElement();
                    $xml->endElement();
                $xml->endElement();
            $xml->endElement();
        }
        $xml->endElement();
    }
    
    /**
     * 
     * @param array<mixed,mixed> $coordinates
     * @return string
     */
    private static function coordsToPosList(array $coordinates) : string
    {
        $flatish = array_map( function($e){ return implode(' ', /*array_reverse(*/ $e /*)*/ ); }, $coordinates);
        return implode( ' ', $flatish );
    }    
    

    static function isGeoJSON(?string $string) : bool
    {
        if(!is_null($string)&&!empty($string) && json_validate($string))
        {
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }
    
    static function isWKT(?string $string) : bool
    {
        if(!is_null($string)&&!empty($string))
        {
            foreach(self::$WKT_KEYWORDS as $kw)
            {
                if(str_contains($string, $kw))
                {
                    return true;
                }
            }
        }
        return false;        
    }
    
    public static function wktToGeoJSON(?string $string): string 
    {
        $json="";
        if(self::isWKT($string))
        {
            $polygon=geoPHP::load($string);
            $geoJSON = new GeoJSON();
            $json = $geoJSON->write($polygon); 
        }
        return $json;
    }    
    
    /**
     * 
     * @param string|null $wkt
     * @return array<int,array<int,float>>
     */
    public static function wktToCoordinates(?string $wkt): array 
    {
        if(empty($wkt)) 
        {
            return [];
        }
        if(self::isGeoJSON($wkt))
        {
            $wkt=self::GeoJsonToWKT($wkt);
        }
        $coordinates = explode(',', str_replace(['MULTIPOLYGON','POLYGON','POINT', '(', ')'], '', $wkt));
        $parsedCoordinates = [];
        //KDebugger::getInstance()->dump($coordinates);
        foreach ($coordinates as $coordinate) 
        {
            //KDebugger::getInstance()->dump($coordinate);
            //list($lng, $lat) = explode(' ', trim($coordinate));
            $arrayLngLat= explode(' ', trim($coordinate));
            if(count($arrayLngLat)==2)
            {
                $lng=$arrayLngLat[0];
                $lat=$arrayLngLat[1];
                $parsedCoordinates[] = [(float) $lat, (float) $lng];
            }
        }
        return $parsedCoordinates;
    }  
    
    /**
     * 
     * @param string|null $geometry
     * @return array<int,array<int,float>>
     */
    public static function geomToLatLong(?string $geometry) : array
    {
        if (empty($geometry)) 
        {
            return [];
        }
        if(self::isGeoJSON($geometry))
        {
            $geometry=self::GeoJsonToWKT($geometry);
        }       
        $polygon=geoPHP::load($geometry);
        $centroid = $polygon->getCentroid();
        $centX = $centroid->getX();
        $centY = $centroid->getY();
        return [$centX,$centY];
    }
    
    public static function geomCalcArea(?string $geometry) : float
    {
        if (empty($geometry)) 
        {
            return 0;
        }
        if(self::isGeoJSON($geometry))
        {
            $geometry=self::GeoJsonToWKT($geometry);
        }       
        $polygon=geoPHP::load($geometry);
        //var_dump($polygon);
        $area = $polygon->area();
        //var_dump($area);
        return $area;
    }
    
    
    public function __construct(?string $ewkb)
    {
        if(!empty($ewkb)) 
        {
            $this->EWKB = $ewkb;
            $this->updateFromEWKB();
        }
    }

    public function convertToGeoJsonString(): string
    {
        if (!$this->geometry) {
            return "";
        }
        $json = $this->geometry->out('json');
        if(is_null($json)||empty($json))
        {
            $json='';
        }
        return $json;
    }

    public function getGeometry() : mixed
    {
        return $this->geometry;
    }

    public function getCentroid() : ?KGeometry 
    {
        if($this->geometry) 
        {
            if($point = $this->geometry->getCentroid()) 
            {
                return new KGeometry($point->out('ewkb'));
            }
        }
        return null;
    }

    public function getEWKB() : mixed
    {
        return $this->EWKB;
    }

    public function getEWKT() : mixed 
    {
        return $this->EWKT;
    }

    public function getSrid() : mixed
    {
        return $this->srid;
    }

    public function setEWKB(mixed $EWKB) : void
    {
        $this->EWKB = $EWKB;
        $this->updateFromEWKB();
    }
    
    public function setWKB(mixed $WKB): void
    {
        $this->WKB = $WKB;
        $this->updateFromWKB();
    }    

    public function setWKT(mixed $EWKT): void
    {
        $this->EWKT = $EWKT;
        $this->updateFromEWKT();
    }

    private function updateFromEWKB(): void 
    {
        $ewkb_reader = new EWKB();
        $this->geometry = $ewkb_reader->read($this->EWKB);

        $this->srid = $this->geometry->SRID();

        $ewkt_writer = new EWKT();
        $this->EWKT = $ewkt_writer->write($this->geometry);
    }
    
    private function updateFromWKB(): void 
    {
        $wkb_reader = new WKB();
        $this->geometry = $wkb_reader->read($this->WKB);

        $this->srid = $this->geometry->SRID();

        $ewkt_writer = new EWKT();
        $this->EWKT = $ewkt_writer->write($this->geometry);
    }    

    private function updateFromEWKT(): void 
    {
        $ewkt_reader = new EWKT();
        $this->geometry = $ewkt_reader->read($this->EWKT);//, TRUE);

        $this->srid = $this->geometry->SRID();

        $ewkb_writer = new EWKB();
        $this->EWKB = $ewkb_writer->write($this->geometry);
    }
}
