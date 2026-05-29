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
class KFieldGeometry extends KField
{
    private ?KGeometry $geom=null;
    private ?string $srid=null;
    private ?string $json=null;
    private ?string $coord_dimension=null;
    private ?string $geometry_type=null;
    
    function __construct()
    {
        $this->setType(static::$GEOMETRY);
        $this->setIs_null(true);
        $this->setValueVar(NULL);
        $this->labelError="KFieldGeometry::";
    }
    
    protected function affectValue(mixed $value,bool $defaultValue=false) : bool
    {
        $valueString="".$value;
        if(KGeometry::isGeoJSON($valueString)) 
        {
            //echo "GEOJSON => ".$valueString."<br />";
            $this->json = $valueString;
            $this->setVarValue(KGeometry::GeoJsonToWKT($valueString),$defaultValue);
            return true;
        }
//        elseif(!KGeometry::isWKT($valueString))
//        {
//            $this->setVarValue(KGeometry::set($valueString),$defaultValue);
//        }
        

        if(isStringNull($value))
        {
            if($this->getIs_null())
            {
                $this->setVarValue(NULL,$defaultValue);
                return true;
            }
            else
            {
                $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value cannot be NULL ");
                return false;                
            }
        }
        if($value=="")
        {
           $this->setVarValue(NULL,$defaultValue);
           return true;
        }
        if(!is_string($value))
        {
            $this->setKerror($this->labelError.$this->getStringMethodError($defaultValue)."[".$this->getName()."] => Value is not Geometry ->".$value);
            return false;
        }
        $this->setVarValue($value,$defaultValue);
        return true;
    }
    
    function initGeometric() : bool
    {
        //echo "\n\n\nTHIS->GET ".$this->get();
        $this->geom=new KGeometry($this->get());
        $this->json=$this->geom->convertToGeoJsonString();
        $this->srid=$this->geom->getSrid();
        if(!empty($this->json) && !is_null($this->srid))
        {
            return true;
        }
        return false;
    }
    
    function setSrid(mixed $srid) : void
    {
        $this->srid="".$srid;
    }

    function setJson(mixed $json) : void
    {
        $this->json="".$json;
    }

        
    function getSrid() : ?string
    {
        return $this->srid;
    }

    function getJson() : ?string
    {
        return $this->json;
    }
    function getGeom() :?KGeometry
    {
        return $this->geom;
    }

    function getCoord_dimension() : ?string
    {
        return $this->coord_dimension;
    }

    function getGeometry_type() : ?string
    {
        return $this->geometry_type;
    }

    function setGeom(?KGeometry $geom) : bool
    {
        if(!is_null($geom))
        {
            $this->geom=$geom;
            return true;
        }
        return false;
    }

    function setCoord_dimension(mixed $coord_dimension) : void
    {
        $this->coord_dimension=$coord_dimension;
    }

    function setGeometry_type(mixed $geometry_type) : void
    {
        $this->geometry_type=$geometry_type;
    }
    
//    public function convertTypeToSqlString(Sql $sql,$value) : string
//    {
//        return $sql->stringFunctionToGeo($value);
//    }
    
    public function addFunctionSQLToValue(Sql $sql,mixed $value) : string
    {
        return $sql->stringFunctionToGeo("".$value);
    }     

    function toString(string $delimiter="\n") : string
    { 
        $string="#######################".$delimiter."+ KField".$delimiter.parent::toString().$delimiter;
        $array = get_object_vars($this);
        $properties = array_keys($array);
        $string.="+ ".get_class($this).$delimiter;
        foreach ($properties as $key => $value) 
        {
            $string .= "$key = $value =>".$this->$value.$delimiter;
        }
        $string.="#######################".$delimiter;
        return $string;
    } 
    
    /**
     * 
     * @return array<int,array<mixed,mixed>>
     */
    public function jsonSerialize() : array
    {
        $arrayJSON=parent::jsonSerialize();
        $array = get_object_vars($this);
        $properties = array_keys($array);
        foreach ($properties as $key => $value) 
        {
            $arrayJSON[$value]=$this->$value;
        }
        return [
            $arrayJSON
        ];
    }
    
    public function notEqualValue(mixed $value) : bool
    {
        // If empty or null string we set it as no value for Geometry
        if($value==$this->get())
        {
            return false;
        }
        
        if(empty($value)&&empty($this->get()))
        {
            return false;
        }

        
        if(!empty($value)&&!empty($this->get()))
        {       
            $wkt=KGeometry::GeoJsonToWKT($value);
            if(str_replace(" ","", $this->get())==str_replace(" ","", $wkt))
            {
                return false;
            }
        }
        
        
        return parent::notEqualValue($value);
    }
}