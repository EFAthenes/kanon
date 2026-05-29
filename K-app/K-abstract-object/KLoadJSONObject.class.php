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
class KLoadJSONObject
{
    private string $statusString="";
    private string $errorString="";
    private string $tablename="";
    private int $row_imported=0;
    public function __construct()
    {
        
    }

    public function __destruct()
    {
        
    }
    function getTablename() : string
    {
        return $this->tablename;
    }

    function getRow_imported() : int
    {
        return $this->row_imported;
    }

    function setTablename(string $tablename) : void
    {
        $this->tablename=$tablename;
    }

    function setRow_imported(int $row_imported) : void
    {
        $this->row_imported=$row_imported;
    }
    
    function getStatusString() : string
    {
        return $this->statusString;
    }

    function getErrorString() : string
    {
        return $this->errorString;
    }

    function setStatusString(string $statusString) : void
    {
        $this->statusString=$statusString;
    }

    function setErrorString(string $errorString) : void
    {
        $this->errorString=$errorString;
    }

    public function loadByFile(string $path,?string $tablename=null) : bool
    {
        $this->setErrorString("");
        $this->setStatusString("");
        $this->setTablename("");
        $this->setRow_imported(0);
        
        $status=true;
        $kFile=new KFile($path);
        
        $sql=new Sql();
        $sql->connect_DB();
        
        if($kFile->exists()&&$kFile->isFile())
        {
            $json=file_get_contents($kFile->getPath());

            $array = json_decode($json, true);
            foreach($array as $key=> $val)
            {
                if(is_array($val))
                {
                    //echo "ARRAY=>$key \n";
                    if(class_exists($key))
                    {
                        $object=null;
                        $kobjectName=null;
                        if(!is_null($tablename))
                        {
                            $this->setTablename($tablename);
                            $kobjectName=KObject::makeClassNameFromTableName($tablename);
                            $object=new $kobjectName();                            
                        }
                        else
                        {
                            $this->setTablename(strtolower($key));
                            $object=new $key();
                        }
                        
                        if($object instanceof KObject)
                        {
                            $object->initByDefault();
                            foreach($val as $key2=> $val2)
                            {
                                if(is_array($val2))
                                {
                                    foreach($val2 as $key3=> $val3)
                                    {
                                        //echo "$key3 => $val3\n";
                                        $object->setFieldValue($key3,$val3);
                                    }
                                }
                                else
                                {
                                    $this->setErrorString("KLoadJSONObject::loadByFile() JSON STRUCTURE NOT GOOD");
                                    $sql->disconnect_DB();
                                    return false;
                                }
                                //echo $object->toString();
                                if(!$object->insertSql($sql))
                                {
                                    $this->setErrorString("KLoadJSONObject::loadByFile() SQL ERROR WHEN INSERT =>".$object->getKerror());
                                    $sql->disconnect_DB();
                                    return false;
                                }
                                
                                $this->row_imported++;
                                
                                if(!is_null($kobjectName))
                                {
                                    $object=new $kobjectName();     
                                }
                                else
                                {
                                    $object=new $key();
                                }
                                
                                $object->initByDefault();
                            }
                        }
                        else
                        {
                            $this->setErrorString("KLoadJSONObject::loadByFile() Class dnot Instance of  KObject =>".$key);
                            $sql->disconnect_DB();
                            return false;                    
                        }
                    }
                    else
                    {
                        $this->setErrorString("KLoadJSONObject::loadByFile() Class doesn't exist =>".$key);
                        $sql->disconnect_DB();
                        return false;                    
                    }                    
                }
                else
                {
                    $this->setErrorString("KLoadJSONObject::loadByFile() JSON doesn't contain Class name");
                    $sql->disconnect_DB();
                    return false;                    
                }
            }
        }
        $sql->disconnect_DB();
        return $status;
    }
    
    public function loadModelByFile(string $path) : bool
    {
        $this->setErrorString("");
        $this->setStatusString("");
        $this->setTablename("");
        $this->setRow_imported(0);
        
        $status=true;
        $kFile=new KFile($path);
        
        $sql=new Sql();
        $sql->connect_DB();
        
        if($kFile->exists()&&$kFile->isFile())
        {
            $tablename=strtolower(str_replace("export_model_","",$kFile->getNameWithoutExt()));
            $json=file_get_contents($kFile->getPath());

            $array = json_decode($json, true);
            
            $list= new ArrayList();
            foreach($array as $key=> $val)
            {
                //echo "KEY=>".$key."//".print_r($val)."\n";
                if($sql->isTableExisting($tablename))
                {
                    $this->setErrorString("KLoadJSONObject::loadModelByFile() Table already exists ".$tablename."  =>".$key.KCli::br());
                    $sql->disconnect_DB();
                    return false; 
                }
                
                $field=KField::makeByArray($val);
                
                if(!($field instanceof KFieldUnKnown))
                {
                    //echo $field->toString("\n");
                    $list->add($field);
                }
            }
            
            if($list->getSize()<1)
            {
                $this->setErrorString("KLoadJSONObject::loadModelByFile() No Kfields Found !".KCli::br());
                $sql->disconnect_DB();
                return false;
            }
            
            $this->setRow_imported($list->getSize());
            $this->setTablename($tablename);
            if(!$sql->createModelInDb($tablename,$list))
            {
                $this->setErrorString("KLoadJSONObject::loadModelByFile() Error While creating Table !".$sql->getError().KCli::br());
                $sql->disconnect_DB();
                return false;
            }
        }
        else
        {
            $this->setErrorString("KLoadJSONObject::loadModelByFile() File not found =>".$path." !".KCli::br());
            $sql->disconnect_DB();
            return false;                 
        }
        
        return true;
    } 
    
    public function exportDataToFile(string $tablename,string $export_path) : bool
    {
        $classname=ucfirst($tablename);
        $db=new DbList($classname);
        $list=$db->getByArray();
        if($list!=null)
        {      
            $jsonFile=new KFile($export_path);
            if($jsonFile->exists())
            {
                $jsonFile->delete();
            }
            $log= new Klog($jsonFile->getParentKFile()->getPath(),$jsonFile->getName());
            $string="{\"".$classname."\":[";
            $log->addLine($string);
            for($i=0; $i<$list->getSize(); $i++)
            {
                $string="";
                $obj=$list->getKObject($i);
                if($obj!=null)
                {
                    if($i!=0)
                    {
                        $string.=",";
                    }
                    $string.=$obj->fieldsValueToJson();
                }
                $log->addLine($string);
            }
            $string="]}";
            $log->addLine($string); 
            
            $jsonFile= new KFile($export_path);
            if($jsonFile->exists()&&$jsonFile->isFile())
            {
                return true;
            }
        }

        $this->setErrorString("KLoadJSONObject::exportDataToFile() error for table =>".$tablename." and path =>".$export_path." !".KCli::br());
        return false;        
    }
}