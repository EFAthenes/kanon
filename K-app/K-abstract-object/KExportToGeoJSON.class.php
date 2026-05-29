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
class KExportToGeoJSON
{
//    private $srs="";
//    private $bbox="";
    public function __construct()
    {
        
    }

    public function __destruct()
    {
        
    }

    public function exportKObject(KObject $object) : string
    {
        $status=false;
        $geometry="";
        $srid="";
        $object->fillKFields();
        $map=$object->getMapKField();
        $list=$map->toArrayList();
        $arrayProperties=array();
        for ($i=0;$i<$list->getSize();$i++)
        {
            $kField=$list->get($i);
            if($kField instanceof KFieldGeometry)
            {
               if($kField->initGeometric())
               {
                   $status=true;
                   $geometry=$kField->getJson();
                   $srid=$kField->getSrid();
                   $arrayProperties["the_geom"]=$kField->getName();
               }
            }
            else
            {
                $arrayProperties[$kField->getName()]=$kField->get();
            }
        }
        $string="";
        if($status)
        {
            $string.='{
    "type": "FeatureCollection",
    "crs": {
        "type": "name",
        "properties": {
            "name": "EPSG:'.$srid.'"
        }
    },
';
            $string.='"features": [{"type": "Feature", "geometry":'.$geometry.',"properties":'.json_encode($arrayProperties).'}]';
            $string.='}';
        }
        return $string;
    }
    public function exportKObjects(ArrayList $listObjects) : string
    {
        $status=false;
        $geometry="";
        $srid="";
        $string="";
        /* @var  $object KObject  */
        for($j=0; $j<$listObjects->getSize(); $j++)
        {
            $status=false;
            $object=$listObjects->get($j);
            $object->fillKFields();
            $map=$object->getMapKField();
            $list=$map->toArrayList();
            $arrayProperties=array();
            for ($i=0;$i<$list->getSize();$i++)
            {
                $kField=$list->get($i);
                if($kField instanceof KFieldGeometry)
                {
                   if($kField->initGeometric())
                   {
                       $status=true;
                       $geometry=$kField->getJson();
                       $srid=$kField->getSrid();
                       $arrayProperties["the_geom"]=$kField->getName();
                   }
                }
                else
                {
                    $arrayProperties[$kField->getName()]=$kField->get();
                }
            }
            
            if($status)
            {
                if($string!="")
                {
                    $string.=',';
                }
                $string.='{"type": "Feature", "geometry":'.$geometry.',"properties":'.json_encode($arrayProperties).'}';
            }
            
        }
        
        $stringJSON="";
        if($string!="")
        {
            $stringJSON.='{
    "type": "FeatureCollection",
    "crs": {
        "type": "name",
        "properties": {
            "name": "EPSG:'.$srid.'"
        }
    },
';
            $stringJSON.='"features": ['.$string.']';
            $stringJSON.='}';
        }        
        return $stringJSON;
    }  
    
    public function exportKObjectsToJSONFile(string $filename,ArrayList $listObjects) : bool
    {
        $status=false;
        if(file_exists($filename))
        {
            unlink($filename);
        }
        $string=$this->exportKObjects($listObjects);
        if($string!="")
        {
            $fp = fopen($filename, "w");

            if(fwrite($fp, $string))
            {
                $status=true;
            }
            fclose($fp);
        }
        return $status;     
    }
    
    public function exportKObjectToJSONFile(string $filename,KObject $object) : bool
    {
        $status=false;
        if(file_exists($filename))
        {
            unlink($filename);
        }
        $string=$this->exportKObject($object);
        if($string!="")
        {
            $fp = fopen($filename, "w");

            if(fwrite($fp, $string))
            {
                $status=true;
            }
            fclose($fp);
        }
        return $status;     
    }    
}