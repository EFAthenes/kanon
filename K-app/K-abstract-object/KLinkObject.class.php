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
abstract class KLinkObject extends KObject
{
    /**
     * 
     * @var array<int,string>
     */
    private static array $errorUpdateKlinkArray=[];
    private static bool $is_modified=false;
    
    public static function getMapOfKLinkObjectsFromFk(string $className,mixed $fk_value) : HashMap
    {
        /* @var $item KLinkObjectItem */
        $class=get_called_class();
        $klink=new $class();
        $item=$klink->getKlinkObjectItem($className);
        $map=new HashMap();
        if(!is_null($item)&&isInteger($fk_value))
        {              
            if($klink->fieldNameExists($item->fk_id_field))
            {
                $dBList=new DbList($class);
                $map=$dBList->getByArrayInIdMap(array(
                    new QueryField($item->fk_id_field,$fk_value,QueryField::$EQUAL)));               
            }
        }
        return $map;
    }    
    
    protected function getKObjectFromFk(string $className) : ?KObject
    {
        /* @var $item KLinkObjectItem */
        $item=$this->getKlinkObjectItem($className);
        if(!is_null($item)&&class_exists($item->className)&&$this->fieldNameExists($item->fk_id_field))
        {
            $object = new $item->className();
//            if($object instanceof KObject)
//            {
                $object->initById($this->getFieldValue($item->fk_id_field));                
                return $object;
//            }           
        }
        return null;
    }  
    
    /**
     * 
     * @param string $classNameSrc
     * @param int $fk_value_src
     * @param string $classNameDst
     * @param array<mixed,mixed>|null $fk_values_dst
     * @param array<string,mixed>|null $optionnal_fields
     * @return bool
     */
    public static function updateSelfKlinksInDbFromFK(string $classNameSrc,int $fk_value_src,string $classNameDst,?array $fk_values_dst,?array $optionnal_fields=null) : bool
    {
        $class=get_called_class();
        $klink=new $class();
        return self::updateKlinksInDbFromFK($klink,$classNameSrc,$fk_value_src,$classNameDst,$fk_values_dst,$optionnal_fields);
    }
    /**
     * 
     * @param KLinkObject $klink
     * @param string $classNameSrc
     * @param int $fk_value_src
     * @param string $classNameDst
     * @param array<mixed,mixed>|null $fk_values_dst
     * @param array<string,mixed>|null $optionnal_fields
     * @return bool
     */
    public static function updateKlinksInDbFromFK(KLinkObject $klink,string $classNameSrc,int $fk_value_src,string $classNameDst,?array $fk_values_dst,?array $optionnal_fields=null) : bool
    {
        self::$is_modified=false;
        /* @var $item_src KLinkObjectItem */
        /* @var $item_dst KLinkObjectItem */
        $item_src=$klink->getKlinkObjectItem($classNameSrc);       
        $item_dst=$klink->getKlinkObjectItem($classNameDst);
        
        if(is_null($fk_values_dst))
        {
            $fk_values_dst=[];
        }
        
        $arrayFKexists=array(); // Do nothing
        $arrayFKdoesntExist=array(); // TO REMOVE
        $arrayFKPOSTdoesntExist=array(); // TO CREATE
        
        self::$errorUpdateKlinkArray=[];
        
        if(!is_null($item_src) && !is_null($item_dst))
        {
            $klinkClassName=$klink->getClassName();
            $newKlink= new $klinkClassName();
            
            $dBList=new DbList($klink->getClassName());
            $arrayField=array(new QueryField($item_src->fk_id_field,$fk_value_src,QueryField::$EQUAL));
            if(!is_null($optionnal_fields) && count($optionnal_fields))
            {
                foreach ($optionnal_fields as $key=>$value)
                {
                    if(!$newKlink->setFieldValue($key,$value))
                    {
                        self::$errorUpdateKlinkArray[]=$newKlink->getKerror();
                    }
                    else
                    {
                        $arrayField[]=new QueryField($key,$value,QueryField::$EQUAL);
                    }
                }                
            }
            $list=$dBList->getByArray($arrayField);
            /* @var $klink_item KLinkObject */
            foreach ($list as $klink_item)
            {
                $fk_id_check=$klink_item->getFieldValue($item_dst->fk_id_field);
                if(!in_array($fk_id_check, $fk_values_dst))
                {
                    $arrayFKdoesntExist[]=$klink_item;
                }
                else
                {
                    $arrayFKexists[]=$fk_id_check;
                }
            }
            
            foreach ($fk_values_dst as $fk_value_dst)
            {
                $found=false;
                foreach ($list as $klink_item)
                {
                    $fk_id_check=$klink_item->getFieldValue($item_dst->fk_id_field);
                    if($fk_id_check==$fk_value_dst)
                    {
                        $found=true;
                        break;
                    }
                }
                
                if(!$found)
                {
                    $arrayFKPOSTdoesntExist[]=$fk_value_dst;
                }
            }
            
            //DELETE
            /* @var $klink_item KLinkObject */
            foreach ($arrayFKdoesntExist as $klink_item)
            {
                self::$is_modified=true;
                if(!$klink_item->delete())
                {
                    self::$errorUpdateKlinkArray[]=$klink_item->getKerror();
                }
            }
            
            //CREATE
            foreach ($arrayFKPOSTdoesntExist as $fk_value_dst)
            {
                /* @var $newKlink KLinkObject */
                $newKlink= new $klinkClassName();
                $newKlink->setFieldValue($item_src->fk_id_field,$fk_value_src);
                $newKlink->setFieldValue($item_dst->fk_id_field,$fk_value_dst);
                
                if(!is_null($optionnal_fields) && count($optionnal_fields))
                {
                    foreach ($optionnal_fields as $key=>$value)
                    {
                        if(!$newKlink->setFieldValue($key,$value))
                        {
                            self::$errorUpdateKlinkArray[]=$newKlink->getKerror();
                        }
                    }
                }
                
                self::$is_modified=true;
                if(!$newKlink->insert())
                {
                    self::$errorUpdateKlinkArray[]=$newKlink->getKerror();
                }
            }                        
        }
        
        if(count(self::$errorUpdateKlinkArray)>0)
        {
            return false;
        }
        return true;
    }
    
    static function isUpdateKlinkModified() : bool
    {
        return self::$is_modified;
    }
    
    static function getErrorUpdateKlinkString(string $string_delimitor="<br />") : string
    {
        $string="";
        foreach (self::$errorUpdateKlinkArray as $stringError)
        {
            $string.=$stringError.$string_delimitor;
        }
        return $string;
    }
}