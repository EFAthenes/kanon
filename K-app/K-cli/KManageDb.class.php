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
class KManageDb
{
    private string $mandatory_field_id="id";
    private string $mandatory_field_date_created="date_created";
    private string $mandatory_field_date_modified="date_modified";
    
    //private $default_encoding="utf8_unicode_ci";
   // private string $default_encoding="utf8mb4_0900_ai_ci";
    
    private string $klink_table_prefix="klink_";
    private string $fk_id_prefix="fk_id_";
    private string $klink_krank="krank";
    private string $klink_krank_static="KRANK";
    
    
    private string $dir_www="www";
    private string $dir_routes="routes";
    private string $dir_middleware="middleware";
    private string $dir_config="config";
    /**
     * 
     * @var array<int,string>
     */
    private array $dir_mandatory=["cache","config","controller","db","i18n","layout","manager","middleware","routes","tmp","utils","view","template","www"];   
    /**
     * 
     * @var array<int,string>
     */
    private array $dir_mandatory_www=["css","js","img"];
    
    
    private string $app_directory="";
    
    //put your code here
    function __construct(string $folderApp)
    {
        $this->app_directory=$folderApp;
    }
    
    public function removeKappCache() : bool
    {
        $file=new KFile(__ROOT__.'/K-cache/a_require_cache.php');
        return $file->delete();        
    }
    
    public function removeAppCache() : bool
    {
        $file=new KFile($this->getDirectoryOfTheApp()."/cache/a_require_cache.php");
        return $file->delete();
    }
    
    public function removeCaches() : bool
    {
        return $this->removeAppCache() && $this->removeKappCache();
    }
    
    public function cleanCache() : bool
    {
        $cache=new KCache($this->getDirectoryOfTheApp()."/cache/");
        return $cache->cleanAll();    
    }
    
    public function createProject(KCli $cli,?string $directory) : void
    {
        if(is_null($directory) || $directory=="")
        {
            $cli->printStringCustom("YOU HAVE TO CHOOSE A DIRECTORY NAME !!!".KCli::br(),KCli::$F_RED);
        }
        else if(!isAlphaNumericAndUndescore($directory))
        {
             $cli->printStringCustom("THE NAME HAS TO CONTAIN ONLY CHARACTERS AND NUMBERS !!! ".KCli::br(),KCli::$F_RED);
        }
        else
        {
            $dir=new KFile(getcwd().KFile::separator().$directory);
            if($dir->exists())
            {
                $cli->printStringCustom("THE DIRECTORY ALREADY EXISTS. ABORTING OPERATION !!! ".KCli::br(),KCli::$F_RED);
            }
            else
            {
                $dir->mkdir();
                /** @phpstan-ignore-next-line */
                if(!$dir->exists())
                {
                    $cli->printStringCustom("THE MAIN DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) ".KCli::br(),KCli::$F_RED);
                }
                else
                {
                    $cli->printString("MAIN PROJECT DIRECTORY CREATED => ".$directory. "!!!! ".KCli::br());
                    foreach ($this->dir_mandatory as $new_dir)
                    {
                        $dir2= new KFile($dir->getPath().KFile::separator().$new_dir);
                        $dir2->mkdir();
                        if(!$dir2->exists())
                        {
                            $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => ".$dir2->getPath().KCli::br(),KCli::$F_RED);
                            return ;
                        }
                        else
                        {
                            $cli->printString(" / => ".$new_dir." -> OK".KCli::br());
                        }
                    }
                    $dir_www=new KFile($dir->getPath().KFile::separator().$this->dir_www);
                    foreach ($this->dir_mandatory_www as $new_dir)
                    {
                        $dir2= new KFile($dir_www->getPath().KFile::separator().$new_dir);
                        $dir2->mkdir();
                        if(!$dir2->exists())
                        {
                            $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => ".$dir2->getPath().KCli::br(),KCli::$F_RED);
                            return ;
                        }
                        else
                        {
                            $cli->printString("www => ".$new_dir." -> OK".KCli::br());
                        }
                    }
                    $cli->printString("ADDING DEFAULT FILES..... ".KCli::br());
                    
                      
                    if(!$this->makeDefaultINCLUDEApp($directory,$dir))
                    {
                        $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => / ".KCli::br(),KCli::$F_RED);
                    }                    
                    if(!$this->makeDefaultWWWIndexFile($dir_www))
                    {
                        $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => www ".KCli::br(),KCli::$F_RED);
                    }
                    if(!$this->makeDefaultWWWActionFile($dir_www))
                    {
                        $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => www ".KCli::br(),KCli::$F_RED);
                    }
                    if(!$this->makeDefaultROUTESFile(new KFile($dir->getPath().KFile::separator().$this->dir_routes)))
                    {
                        $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => routes ".KCli::br(),KCli::$F_RED);
                    }
                    if(!$this->makeDefaultMIDDLEWAREFile(new KFile($dir->getPath().KFile::separator().$this->dir_middleware)))
                    {
                        $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => middleware ".KCli::br(),KCli::$F_RED);
                    }                  
                    if(!$this->makeDefaultCONFIGFile(new KFile($dir->getPath().KFile::separator().$this->dir_config)))
                    {
                        $cli->printStringCustom("THE DIRECTORY CANNOT BE CREATED !!! (CHECK FOLDER PERMISSIONS) => config ".KCli::br(),KCli::$F_RED);
                    }
                    
                    $cli->printString("OK!!!! ".KCli::br());
                    
                    $this->setProject($cli, $directory);
                    $cli->printString("Now you have to edit config/config.php to set site url and database before launching the 'initialize_kapp_tables' ".KCli::br());
                }
            }
        }
    }

    public function setProject(KCli $cli,?string $directory) : void
    {
        if(is_null($directory) || $directory=="")
        {
            $cli->printStringCustom("YOU HAVE TO CHOOSE A DIRECTORY NAME !!!".KCli::br(),KCli::$F_RED);
        }
        else if(!isAlphaNumericAndUndescore($directory))
        {
             $cli->printStringCustom("THE NAME HAS TO CONTAIN ONLY CHARACTERS AND NUMBERS !!! ".KCli::br(),KCli::$F_RED);
        }
        else
        {
            $dir=new KFile(getcwd().KFile::separator().$directory);
            if(!$dir->exists())
            {
                $cli->printStringCustom("THE DIRECTORY DOESN'T EXISTS. ABORTING OPERATION !!! ".KCli::br(),KCli::$F_RED);
            } 
            else
            {
                $this->stringToFile(new KFile(getcwd().KFile::separator()."app.txt"), "AppFolder=".$directory);
                $cli->printString("Active project => ".$directory." !!!! ".KCli::br());
            }
        }        
    }
    
    public function makeDB(KCli $cli,?string $db_name) : void
    {
        if(is_null($db_name) || $db_name=="")
        {
            $cli->printStringCustom("YOU HAVE TO CHOOSE A DB NAME !!!".KCli::br(),KCli::$F_RED);
        }
        else if(!isAlphaNumericAndUndescore($db_name))
        {
             $cli->printStringCustom("THE NAME HAS TO CONTAIN ONLY CHARACTERS AND NUMBERS !!! ".KCli::br(),KCli::$F_RED);
        }
        else
        {
            $error=Sql::getInstanceToCreateDB($db_name);
            if(!is_null($error))
            {
                $cli->printStringCustom("CANNOT CREATE THE DATABASE !".KCli::br(),KCli::$F_RED);
                $cli->printStringCustom($error.KCli::br(),KCli::$F_RED);
            }
            else
            {
                $cli->printString("DB '".$db_name."' created  !!!! ".KCli::br());
            }           
        } 
    }    
    
    
    public function checkDb(KCli $cli) : bool
    {
        $status=true;
        if(!Sql::getInstance())
        {
            $cli->printStringCustom("CANNOT CONNECT TO THE DATABASE !".KCli::br(),KCli::$F_RED);
            return false;            
        }        
        $arrayTables=$this->checkColumns($cli);
        if(count($arrayTables)==0)
        {
            $cli->printString("ALL COLUMNS ARE GOOD!!!! ".KCli::br());
        }
        else
        {
            $status=false;
            $cli->printString("SOME FIELDS NEED TO BE ADDED ".KCli::br());
        }
        
        if($this->checkTables($cli))
        {
            $cli->printString("ALL TABLES ARE GOOD!!!! ".KCli::br());
        }
        else
        {
            $status=false;
            $cli->printString("SOME TABLES NEED TO BE MODIFIED ".KCli::br());
        }
        
        
        if($this->checkPrimaryKey($cli))
        {
            $cli->printString("ALL PRIMARY KEYS ARE GOOD!!!! ".KCli::br());
        }
        else
        {
            $status=false;
            $cli->printString("SOME PRIMARY KEYS NEED TO BE MODIFIED ".KCli::br());
        }
        
        if($this->checkDatesIndexes($cli))
        {
            $cli->printString("ALL DATES INDEXES ARE GOOD!!!! ".KCli::br());
        }
        else
        {
            $status=false;
            $cli->printString("SOME DATES INDEXES NEED TO BE MODIFIED ".KCli::br());
        }        
        return $status;
    }
    
    public function createModelInDb(KCli $cli,mixed $database) : void
    {
        $sql=new Sql();
        if(is_null($database))
        {
            $cli->printString("NOT DATABASE INPUT SELECTING THE DEFAULT ONE => ".$sql->getBdd()." ".KCli::br());
        }
        else
        {
            $cli->printString("NOT DEFAULT DATABASE => ".$sql->getBdd()." ".KCli::br());
            $cli->printString("CHECK IF EXISTS OTHERWISE WE CREATE IT ".KCli::br());
            
            if(!$sql->isDatabaseExisting($database))
            {
                $cli->printString("NOT PRESENT, TRYING TO CREATE IT ".KCli::br());
                if($sql->makeDataBase($database))
                { 
                    $cli->printString("DATABASE ``".$database."`` CREATED! ".KCli::br());
                }
                else
                {
                    $cli->printStringCustom("FAILED CREATING IT  => ".$sql->getError()." ".KCli::br(),KCli::$F_RED);                  
                }
            }
            
            if(!$sql->isDatabaseExisting($database))
            {               
                $cli->printStringCustom("ERROR CANNOT CREATE DB ``".$database."`` => ".$sql->getError()." ".KCli::br(),KCli::$F_RED);
                return;
            }
            $sql->setBdd($database);
        }
        
        $l= new KListObject();
        $CURRENT_DIR=$this->getDirectoryOfTheApp()."/db/normaldb/";
        $list=$l->getListOfKObject($CURRENT_DIR);

        if(!$sql->connect_DB())
        {
            $cli->printStringCustom("ERROR CONNECTION WITH DATABASE => ".$sql->getError()." ".KCli::br(),KCli::$F_RED);
            return;
        }
        for($i=0; $i<$list->getSize(); $i++)
        {
            //echo "new ".$list->get($i)."(); => CREATE TABLE\n";
            $objectName=$list->get($i);
            $obj=new $objectName();
            $obj->initKFields();
            if($obj->createModelInDb($sql))
            {
                $cli->printString("TABLE ``".strtolower($objectName)."`` CREATED! ".KCli::br());
            }
            else
            {
                $cli->printStringCustom("ERROR CANNOT CREATE TABLE => ``".strtolower($objectName)."`` ".$sql->getError()." ".KCli::br(),KCli::$F_RED);
            }
            //return;
        }
        $sql->disconnect_DB();
    }
    
    public function exportDaTaToJSON(KCli $cli,mixed $directory,mixed $table) : void
    {
        if(is_null($directory))
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD A DIRECTORY TO EXPORT THE FILES ".KCli::br(),KCli::$F_RED);
            return;
        }
        $dir= new KFile($directory);
        if(!$dir->exists()||!$dir->isDirectory())
        {
            $cli->printStringCustom("THE DIRECTORY ``".$directory."`` DOESNT EXIST".KCli::br(),KCli::$F_RED);
            return;
        }
        $newDir=new KFile($dir->getPath().KFile::separator()."DATA");
        if($newDir->exists()&&$newDir->isFile())
        {
            $cli->printStringCustom("CANNOT CREATE DIRECTORY ``".$newDir->getPath()."`` FILE ALREADY THERE".KCli::br(),KCli::$F_RED);
            return;            
        }
        else if(!$newDir->exists())
        {
            $newDir->mkdir();
        }
        if(!$newDir->exists())
        {
            $cli->printStringCustom("CANNOT CREATE DIRECTORY ``".$newDir->getPath()."`` ".KCli::br(),KCli::$F_RED);
            return;             
        }
        
        if(is_null($table))
        {
            $cli->printStringCustom("YOU NEED TO SPECIFY A TABLE (OR KEYWORD ``all`` TO EXPORT EVERYTHING) ".KCli::br(),KCli::$F_RED);
            return;             
        }
               
        $classname=ucfirst($table);
        if($table!="all")
        {
            if(!class_exists($classname))
            {
                $cli->printStringCustom("TABLE ``".$table."`` DOESN'T EXIST ".KCli::br(),KCli::$F_RED);
                return;                 
            }
        }
        
        $status=false;
        
        $CURRENT_DIR=$this->getDirectoryOfTheApp()."/db/normaldb/";

        $l= new KListObject();
        $list=$l->getListOfKObject($CURRENT_DIR);        
        for($i=0; $i<$list->getSize(); $i++)
        {
            if($table=="all"||$list->get($i)==$classname)
            {
                if($this->exportTableToJSON($list->get($i),$newDir->getPath(),$cli))
                {
                    $cli->printString("EXPORT DATA TO JSON ".$list->get($i)." in ".$newDir->getPath()." ".KCli::br());
                    $status=true;
                }
                else
                {
                    $cli->printStringCustom("ERROR :: EXPORT DATA TO JSON ".$list->get($i)." in ".$newDir->getPath()." ".KCli::br(),KCli::$F_RED);
                }
            }
        }        
        if(!$status)
        {
            $cli->printStringCustom("ERROR :: EXPORT DATA TO JSON CLASS NOT FOUND ".$classname." in ".$newDir->getPath()." ".KCli::br(),KCli::$F_RED);
        }
    }
    
    public function exportTableToJSON(string $tablename,string $path,KCli $cli) : bool
    {
        $load= new KLoadJSONObject();
        $classname=ucfirst($tablename);
        $export_path=$path.KFile::separator()."export_data_".$classname.".json";
        if($load->exportDataToFile($tablename, $export_path))
        {
            return true;
        }
        $cli->printStringCustom("ERROR :: exportTableToJSON ERROR ".$tablename.KCli::br().$load->getErrorString().KCli::br(),KCli::$F_RED);
        return false;
    }
    
    public function exportModelToJSON(KCli $cli,string $directory,string $table) : void
    {
        if(empty($directory))
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD A DIRECTORY TO EXPORT THE FILES ".KCli::br(),KCli::$F_RED);
            return;
        }
        $dir= new KFile($directory);
        if(!$dir->exists()||!$dir->isDirectory())
        {
            $cli->printStringCustom("THE DIRECTORY ``".$directory."`` DOESNT EXIST".KCli::br(),KCli::$F_RED);
            return;
        }
        $newDir=new KFile($dir->getPath().KFile::separator()."MODEL");
        if($newDir->exists()&&$newDir->isFile())
        {
            $cli->printStringCustom("CANNOT CREATE DIRECTORY ``".$newDir->getPath()."`` FILE ALREADY THERE".KCli::br(),KCli::$F_RED);
            return;            
        }
        else if(!$newDir->exists())
        {
            $newDir->mkdir();
        }
        if(!$newDir->exists())
        {
            $cli->printStringCustom("CANNOT CREATE DIRECTORY ``".$newDir->getPath()."`` ".KCli::br(),KCli::$F_RED);
            return;             
        }
        
        if(empty($table))
        {
            $cli->printStringCustom("YOU NEED TO SPECIFY A TABLE (OR KEYWORD ``all`` TO EXPORT EVERYTHING) ".KCli::br(),KCli::$F_RED);
            return;             
        }
        
        $classname=ucfirst($table);
        $classname = preg_replace_callback('/_([a-z]?)/', function($match) {
            return "_".strtoupper($match[1]);
        }, $classname);
        //echo $classname;
        if($table!="all")
        {
            if(!class_exists($classname))
            {
                $cli->printStringCustom("TABLE ``".$table."`` DOESN'T EXIST ".KCli::br(),KCli::$F_RED);
                return;
            }
        }
        
        $status = false;
        $l= new KListObject();
        $CURRENT_DIR=$this->getDirectoryOfTheApp()."/db/normaldb/";
        $list=$l->getListOfKObject($CURRENT_DIR);        
        for($i=0; $i<$list->getSize(); $i++)
        {
            //echo "CLASSNAME => ".$list->get($i)."\n";
            if($table=="all"||$list->get($i)==$classname)
            {
                $this->exportTableModelToJSON($list->get($i),$newDir->getPath());
                $cli->printString("EXPORT MODEL TO JSON ".$list->get($i)." in ".$newDir->getPath()." ".KCli::br());
                $status = true;
            }
        }
        if(!$status)
        {
            $cli->printStringCustom("CLASSNAME NOT FOUND ".$classname.KCli::br(),KCli::$F_RED);
        }
    }    

    public function exportTableModelToJSON(string $tablename,string $path) : bool
    {
        $status=false;
        $classname=ucfirst($tablename);
        $kObject= new $classname();
        $kObject->initKFields();
        //echo $path.KFile::separator()."export_model_".$classname.".json\n";
        
        $kObject->exportModelToJSONFile($path.KFile::separator()."export_model_".$classname.".json");
        
        $newFile=new KFile($path.KFile::separator()."export_model_".$classname.".json");
        if($newFile->exists()&&$newFile->isFile())
        {
            $status=true;
        }
        return $status;
    } 
    
    public function importDataFromJSON(KCli $cli,?string $path) : void
    {
        if(is_null($path))
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD A DIRECTORY OR A FILE TO IMPORT DATA ".KCli::br(),KCli::$F_RED);
            return;
        }
        $dir= new KFile($path);
        if(!$dir->exists()||!($dir->isDirectory()|| $dir->isFile()))
        {
            $cli->printStringCustom("THE PATH ``".$path."`` DOESNT EXIST".KCli::br(),KCli::$F_RED);
            return;
        }
        
        if(!Sql::getInstance())
        {
            $cli->printStringCustom("CANNOT CONNECT TO THE DATABASE !".KCli::br(),KCli::$F_RED);
            return;            
        }
        
        $load=new KLoadJSONObject();
        if($dir->isFile())
        {
            if($load->loadByFile($path))
            {
                $cli->printString("IMPORT FOR JSON OK => Table : ".$load->getTablename()." | Row : ".$load->getRow_imported()." ".KCli::br());
            }
            else
            {
                $cli->printStringCustom("ERROR WHEN IMPORT FILE ".$path.KCli::br().$load->getErrorString(),KCli::$F_RED);
            }
        }
        else if($dir->isDirectory())
        {
            $list=$dir->listFilesToList();
            $file=new KFile();
            for($i=0; $i<$list->getSize(); $i++)
            {
                $file=$list->get($i);
                if($load->loadByFile($file->getPath()))
                {
                    $cli->printString("IMPORT FOR JSON OK => Table : ".$load->getTablename()." | Row : ".$load->getRow_imported()." ".KCli::br());
                }
                else
                {
                    $cli->printStringCustom("ERROR WHEN IMPORT FILE ".$file->getPath().KCli::br().$load->getErrorString(),KCli::$F_RED);
                }
            }
        }
    }   
    
    public function importModelFromJSON(KCli $cli,string $path) : void
    {
        if(empty($path))
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD A DIRECTORY OR A FILE TO IMPORT DATA ".KCli::br(),KCli::$F_RED);
            return;
        }
        $dir= new KFile($path);
        if(!$dir->exists()||!($dir->isDirectory()|| $dir->isFile()))
        {
            $cli->printStringCustom("THE PATH ``".$path."`` DOESNT EXIST".KCli::br(),KCli::$F_RED);
            return;
        }
        
        if(!Sql::getInstance())
        {
            $cli->printStringCustom("CANNOT CONNECT TO THE DATABASE !".KCli::br(),KCli::$F_RED);
            return;            
        }        
        
        $load=new KLoadJSONObject();
        if($dir->isFile())
        {
            if($load->loadModelByFile($path))
            {
                $cli->printString(KCli::br()."IMPORT FOR JSON OK => Table : ".$load->getTablename()." | Fields : ".$load->getRow_imported()." ".KCli::br());
            }
            else
            {
                $cli->printStringCustom("ERROR WHEN IMPORT FILE [2] ".$path.KCli::br().$load->getErrorString().KCli::br(),KCli::$F_RED);
            }
        }
        else if($dir->isDirectory())
        {
            $list=$dir->listFilesToList();
            $file=new KFile();
            for($i=0; $i<$list->getSize(); $i++)
            {
                $file=$list->get($i);
                if($load->loadModelByFile($file->getPath()))
                {
                    $cli->printString("IMPORT FOR JSON OK => Table : ".$load->getTablename()." | Row : ".$load->getRow_imported()." ".KCli::br());
                }
                else
                {
                    $cli->printStringCustom("ERROR WHEN IMPORT FILE [3] ".$file->getPath().KCli::br().$load->getErrorString(),KCli::$F_RED);
                }
            }
        }
    }     
    
    public function addColumnToDb(KCli $cli) : void
    {
        if(!Sql::getInstance())
        {
            $cli->printStringCustom("CANNOT CONNECT TO THE DATABASE !".KCli::br(),KCli::$F_RED);
            return;            
        }  
        
        $arrayTables=$this->checkColumns($cli);
        if(count($arrayTables)==0)
        {
            $cli->printString("ALL DB IS GOOD!!!! ".KCli::br());
        }
        else
        {
            $cli->printString("ADDING COLUMNS ..... ".KCli::br());
            $sql=new Sql();
            $sql->connect_DB();
            /* @var $table KTablesToManage */
            foreach($arrayTables as $table)
            {
                //echo print_r($table);
                $cli->printString("-------------------".KCli::br());
                $cli->printString("TABLE ".$table->name." => ADDING FIELDS".KCli::br());
                
                foreach($table->fields as $field)
                {
                    //$cli->printString($field.KCli::br());
                    if($field==$this->mandatory_field_id)
                    {
                        if($sql->addFieldId($table->name))
                        {
                            $cli->printStringCustom("Field == ".$field." => OK".KCli::br(),KCli::$F_CYAN);
                        }
                        else
                        {
                            $cli->printStringCustom("Field == ".$field." => KO".KCli::br(),KCli::$F_RED);
                            $cli->printStringCustom($sql->getError().KCli::br(),KCli::$F_RED);
                        }
                    }
                    else if($field==$this->mandatory_field_date_created)
                    {
                        if($sql->addFieldDateCreated($table->name))
                        {
                            $cli->printStringCustom("Field == ".$field." => OK".KCli::br(),KCli::$F_CYAN);
                        }
                        else
                        {
                            $cli->printStringCustom("Field == ".$field." => KO".KCli::br(),KCli::$F_RED);
                            $cli->printStringCustom($sql->getError().KCli::br(),KCli::$F_RED);
                        }
                    }
                    else if($field==$this->mandatory_field_date_modified)
                    {
                        if($sql->addFieldDateModified($table->name))
                        {
                            $cli->printStringCustom("Field == ".$field." => OK".KCli::br(),KCli::$F_CYAN);
                        }
                        else
                        {
                            $cli->printStringCustom("Field == ".$field." => KO".KCli::br(),KCli::$F_RED);
                            $cli->printStringCustom($sql->getError().KCli::br(),KCli::$F_RED);
                        }                        
                    }
                }
                $cli->printString("-------------------".KCli::br());
            }
            $sql->disconnect_DB();
        }
    }
    
    public function makeIndexesKlink(KCli $cli) : void
    {    
        $sql=new Sql();
        $sql->connect_DB();
        
        $cli->printString("MAKING INDEXES FOR KLINKS ..... ".KCli::br());
        
        $results=$sql->getAllTablesNamesInArray();
        foreach ($results as $tableName)
        {
            if(substr($tableName, 0, 6 ) === $this->klink_table_prefix)
            {
                // Klink $tableName 
                $resultsColumn=$sql->showColumnInformation($tableName);
                /* @var $field SqlField */    
                //$field=new SqlField();
                $arrayFieldsForIndexes=[];
                foreach($resultsColumn as $field)
                {          
                    //$field->getName();
                    if(substr($field->getName(), 0, 6 ) === $this->fk_id_prefix)
                    {
                        // make index if not present
                        $arrayFieldsForIndexes[]=$field->getName();
                        if($sql->createIndex($tableName, [$field->getName()]))
                        {
                            $cli->printStringCustom("Index == ".$tableName."_".$field->getName()." => CREATED ".KCli::br(),KCli::$F_CYAN);
                        }
                        else
                        {
                            $cli->printStringCustom("Index == ".$tableName."_".$field->getName()." => KO".KCli::br(),KCli::$F_RED);
                            $cli->printStringCustom($sql->getError().KCli::br(),KCli::$F_RED);
                        }
                    }                    
                }
                
                for ($rotate=0; $rotate < (count($arrayFieldsForIndexes)) ; $rotate++)
                {
                    if($rotate!=0)
                    {
                        $arrayFieldsForIndexes=$this->leftRotatebyOne($arrayFieldsForIndexes);
                    }
                    if($sql->createIndex($tableName, $arrayFieldsForIndexes))
                    {
                        $cli->printStringCustom("Index == ".$tableName."_".$this->arrayToString($arrayFieldsForIndexes)." => CREATED ".KCli::br(),KCli::$F_CYAN);
                    }
                    else
                    {
                        $cli->printStringCustom("Index == ".$this->arrayToString($arrayFieldsForIndexes)." => KO".KCli::br(),KCli::$F_RED);
                        $cli->printStringCustom($sql->getError().KCli::br(),KCli::$F_RED);
                    }
                }
            }
        }
        $sql->disconnect_DB();       
    }
    
    public function testCode(KCli $cli,int $start=1) : void
    {
        $cli->printString("TESTING CODE ..... ".KCli::br());
        $includesFile=new KFile($this->getDirectoryOfTheApp()."/cache/a_require_cache.php");
        
        $includesFile->normalizePathName();
        
        if(!$includesFile->exists()||!$includesFile->isFile())
        {
            $cli->printStringCustom("Problem == the cache file doesn't exist => ".$includesFile->getPath().KCli::br(),KCli::$F_RED);
            return;
        }
        
        require_once($includesFile->getPath());
        
        $cli->printString("Including  ==> ".$includesFile->getPath().KCli::br());
        
        $controllers=[];
        $middlewares=[];
        $objects=[];
        $components=[];
        $others=[];
        $sessions=[];
        $actions=[];
        $frameworks=[];
        $frameworkArray=["KRoutesItems","HashMap","ArrayList","KLayout"];
        
        while($line=$includesFile->readFileByLine())
        { 
            if(!str_starts_with($line,"<?php")&&!str_starts_with($line,"?>"))
            {
                $phpFile=new KFile($line);
                $name=$phpFile->getNameWithoutAllExt();
                if($name!="config")
                {
                    if(!class_exists($name))
                    {
                        $cli->printStringCustom("ClassName Problem == ".$name.KCli::br(),KCli::$F_RED);
                        $cli->printStringCustom($phpFile->getPath().KCli::br(),KCli::$F_RED);
                    }
                    elseif(is_subclass_of($name,"KController"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {  
                            $controllers[]=$name;
                        }
                    }
                    elseif(is_subclass_of($name,"KMiddleware"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {  
                            $middlewares[]=$name;
                        }                        
                    }
                    elseif(is_subclass_of($name,"KObject"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {  
                            $objects[]=$name;
                        }                        
                    } 
                    elseif(is_subclass_of($name,"KComponent"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {  
                            $components[]=$name;
                        }                        
                    } 
                    elseif(is_subclass_of($name,"SessionMemoryItem"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {  
                            $sessions[]=$name;
                        }                        
                    }    
                    elseif(is_subclass_of($name,"KAction"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {  
                            $actions[]=$name;
                        }                        
                    }                   
                    else
                    {
                        $found=false;
                        foreach ($frameworkArray as $type)
                        {
                            if(is_subclass_of($name,$type))
                            {
                                $frameworks[]=$name;
                                $found=true;
                            }
                        }
                        if(!$found)
                        {
                            $others[]=$name;                                                
                        }
                    }                     
                }
            }
        }
        
        $folderApp=KManageDb::getAppFolder();
        $kApp=new KApp($folderApp,false,false,false);
        
        $cli->printString("Testing Controllers  ==> ".KCli::br());
        
        $i=1;
        $outTotal="";
        foreach ($controllers as $controller)
        {
            if($i>=$start)
            {
                $cli->printString($i.") ".$controller.KCli::br());
                $testString=false;
                ob_start();
                try {
                    /* @var $object Kcontroller */
                    $object= new $controller();
                    $object->init();
                    if($object->canTest())
                    {
                        $testString=true;
                        $object->execute();
                        $object->after();   
                    }
                } 
                catch (Exception $exception) 
                {
                    $cli->printStringCustom("Class instance Problem == ".$controller.KCli::br(),KCli::$F_RED);
                    $cli->printStringCustom($exception->getMessage().KCli::br(),KCli::$F_RED);
                    return;
                }  
                
                $out= ob_get_contents();
                ob_end_clean();
                //$outTotal.=$out;
                if(!$testString)
                {
                    $cli->printStringCustom("NOT TESTED ==>".$controller.KCli::br(),KCli::$F_CYAN);
                }
            }
            $i++;
        }
        //$cli->printStringCustom("OUTPUT => :".KCli::br().$outTotal,KCli::$B_YELLOW);
        
        $cli->printString("OK!!! ".KCli::br());
        
        
        $cli->printString("Testing Middlewares  ==> ".KCli::br());
        
        //$i=1;
        foreach ($middlewares as $middleware)
        {
            $cli->printString($i.") ".$middleware.KCli::br());
            ob_start();
            try {
                $object= new $middleware();
                $object->handle();
                $object->terminate();
                
            } 
            catch (Exception $exception) 
            {
                $cli->printStringCustom("Class instance Problem == ".$middleware.KCli::br(),KCli::$F_RED);
                $cli->printStringCustom($exception->getMessage().KCli::br(),KCli::$F_RED);
                return;
            }  
            $i++;
            $out= ob_get_contents();
            ob_end_clean();
        }
        //$cli->printStringCustom("OUTPUT => :".KCli::br().$outTotal,KCli::$B_YELLOW);
        
        $cli->printString("OK!!! ".KCli::br()); 
        
        
        $cli->printString("Testing Objects  ==> ".KCli::br());
        
        //$i=1;
        foreach ($objects as $kobject)
        {
            $cli->printString($i.") ".$kobject.KCli::br());
            ob_start();
            try {
                $object= new $kobject();   
            } 
            catch (Exception $exception) 
            {
                $cli->printStringCustom("Class instance Problem == ".$kobject.KCli::br(),KCli::$F_RED);
                $cli->printStringCustom($exception->getMessage().KCli::br(),KCli::$F_RED);
                return;
            }  
            $i++;
            $out= ob_get_contents();
            ob_end_clean();
        }        
        
        $cli->printString("OK!!! ".KCli::br()); 
        
        $cli->printString("List Others  ==> ".KCli::br());
        //$i=1;
        foreach ($others as $other)
        {
            $cli->printStringCustom($i.") ".$other.KCli::br(),KCli::$F_LIGHT_PURPLE);
            $i++;
        }
        
            /*
                    elseif(is_subclass_of($name,"KController"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {
                            try {
                                $object= new $name();
                                $object->init();
                            } 
                            catch (Exception $exception) 
                            {
                                $cli->printStringCustom("Class instance Problem == ".$name.KCli::br(),KCli::$F_RED);
                                $cli->printStringCustom($phpFile->getPath().KCli::br(),KCli::$F_RED);                            
                                $exception->getMessage();
                            }
                        }
                    }
                    elseif(is_subclass_of($name,"KMiddleware"))
                    {
                        $class = new ReflectionClass($name);
                        if(!$class->isAbstract())
                        {
                            try {
                                $object= new $name();
                                $object->handle();
                                $object->terminate();
                            } 
                            catch (Exception $exception) 
                            {
                                $cli->printStringCustom("Class instance Problem == ".$name.KCli::br(),KCli::$F_RED);
                                $cli->printStringCustom($phpFile->getPath().KCli::br(),KCli::$F_RED);                            
                                $exception->getMessage();
                            }  
                        }
                    }                    
                }
                
            }
             * 
             */
        
    } 
    
    private function checkTables(KCli $cli) : bool
    {
        $sql=new Sql();
        if($sql->getEngine_type()==Sql::$POSTGRES)
        {
            return true;
        }
        $status=true;
        $cli->printString(KCli::br()."CHECKING TABLES IN DATABASE\n---------------- ".KCli::br().$sql->getConnectionParametersString().KCli::br());
        $sql->connect_DB();       
        $tilde='`';
        $schema='';
        if($sql->getEngine_type()==Sql::$POSTGRES)
        {
            $tilde='';
            $schema=$sql->getSchema().".";
        }  
        
        $array_1=array();
        $array_2=array();
        $results=$sql->getAllTablesNamesInArray();
        foreach ($results as $tableName)
        {
            $array_1[]=$tableName;
        }

        $cli->printString("FOUND => ".count($array_1)." TABLES ".KCli::br()."---------------- ".KCli::br());
        
        for($l=0; $l<count($array_1); $l++)
        {
            $cli->printString("TABLE ".($l+1)." => ".$array_1[$l].KCli::br());

            $query_1="SHOW TABLE STATUS where Name ='".$array_1[$l]."'";
            if($result=$sql->queryFetch_SQL($query_1))
            {
                if($result['Engine']!='InnoDB')
                {
                    $query="ALTER TABLE ".$array_1[$l]." ENGINE = InnoDB";               
                    if($sql->request_SQL($query))
                    {
                        $cli->printStringCustom("CONVERTED IN INNODB! ".KCli::br(),KCli::$F_CYAN);
                    }
                    else
                    {
                        $cli->printStringCustom("ERROR NOT CONVERTED IN INNODB! => ".$sql->getError().KCli::br(),KCli::$F_RED);
                        $status=false;
                    }
                }
                else
                {
                    $cli->printString("ALREADY INNODB! ".KCli::br());
                }
                if($result['Collation']!=$sql->getStringCollation())
                {
                    $query="ALTER TABLE ".$array_1[$l]." collate ".$sql->getStringCollation();               
                    if($sql->request_SQL($query))
                    {
                        $cli->printStringCustom("CHANGED IN ".$sql->getStringCollation()."! ".KCli::br(),KCli::$F_CYAN);
                        
                        $query="ALTER TABLE ".$array_1[$l]." convert to character set ".$sql->getStringCharset()." collate ".$sql->getStringCollation();
                        if($sql->request_SQL($query))
                        {
                            $cli->printStringCustom("CONVERTED IN ".$sql->getStringCollation()."! ".KCli::br(),KCli::$F_CYAN);
                        }
                        else
                        {
                            $cli->printStringCustom("ERROR NOT CONVERTED IN ".$sql->getStringCollation()."! => ".$sql->getError().KCli::br(),KCli::$F_RED);
                            $status=false;
                        }
                    }
                    else
                    {
                        $cli->printStringCustom("ERROR NOT CHANGED IN ".$sql->getStringCollation()."! => ".$sql->getError().KCli::br(),KCli::$F_RED);
                        $status=false;
                    }
                }
                else
                {
                    $cli->printString("ALREADY ".$sql->getStringCollation()."! ".KCli::br());
                }                
                
            }
        }
        return $status;
    }
    
    private function checkDatesIndexes(KCli $cli) : bool
    {
        $sql=new Sql();

        $status=true;
        $cli->printString(KCli::br()."CHECKING DATES INDEXES IN DATABASE\n---------------- ".KCli::br().$sql->getConnectionParametersString().KCli::br());
        $sql->connect_DB();       
        $tilde='`';
        $schema='';
        if($sql->getEngine_type()==Sql::$POSTGRES)
        {
            $tilde='';
            $schema=$sql->getSchema().".";
        }  
        
        $array_1=array();
        $results=$sql->getAllTablesNamesInArray();
        foreach ($results as $tableName)
        {
            $array_1[]=$tableName;
        }

        $cli->printString("FOUND => ".count($array_1)." TABLES ".KCli::br()."---------------- ".KCli::br());
        
        for($l=0; $l<count($array_1); $l++)
        {
            $status2=true;
            $found=false;
            $cli->printString("TABLE ".($l+1)." => ".$array_1[$l].KCli::br());
            $tableName=$array_1[$l];
            $results=$sql->showColumnInformation($array_1[$l]);
            /* @var $field SqlField */
            foreach($results as $field)
            {
                if($field->getName()===$this->mandatory_field_date_modified)
                {
                    $found=true;
                    if($sql->createIndex($tableName, [$field->getName()]))
                    {
                        $cli->printStringCustom("Index == ".$tableName."_".$field->getName()." => CREATED ".KCli::br(),KCli::$F_CYAN);
                    }
                    else
                    {
                        $cli->printStringCustom("Index == ".$tableName."_".$field->getName()." => KO".KCli::br(),KCli::$F_RED);
                        $cli->printStringCustom($sql->getError().KCli::br(),KCli::$F_RED);
                    }
                }
            } 
            if(!$found)
            {
                $cli->printStringCustom("FIELD '.$this->mandatory_field_date_modified.' NOT FOUND IN ".$tableName." !!! ".KCli::br(),KCli::$F_RED);
                $cli->printStringCustom("You have to launch add_columns first".KCli::br(),KCli::$F_YELLOW);
                $status2=false;
            }            
            
            
            if(!$status2)
            {
                $status=false;
            }
        }        
        return $status;        
    }
    
    
    private function checkPrimaryKey(KCli $cli) : bool
    {
        $sql=new Sql();

        $status=true;
        $cli->printString(KCli::br()."CHECKING PRIMARY KEYS IN DATABASE\n---------------- ".KCli::br().$sql->getConnectionParametersString().KCli::br());
        $sql->connect_DB();       
        $tilde='`';
        $schema='';
        if($sql->getEngine_type()==Sql::$POSTGRES)
        {
            $tilde='';
            $schema=$sql->getSchema().".";
        }  
        
        $array_1=array();
        $array_2=array();
        $results=$sql->getAllTablesNamesInArray();
        foreach ($results as $tableName)
        {
            $array_1[]=$tableName;
        }

        $cli->printString("FOUND => ".count($array_1)." TABLES ".KCli::br()."---------------- ".KCli::br());
        
        for($l=0; $l<count($array_1); $l++)
        {
            $status2=false;
            $found=false;
            $cli->printString("TABLE ".($l+1)." => ".$array_1[$l].KCli::br());
            $results=$sql->showColumnInformation($array_1[$l]);
            /* @var $field SqlField */
            foreach($results as $field)
            {
                if($field->getName()===$this->mandatory_field_id)
                {
                    $found=true;
                    $status2=true;
                    if($field->getTypeName()==SqlFieldType::$INTEGER)
                    {
//                        if($field->getType()->getLength()==11)
//                        {
                        $updated_in_db=false;
                        if($field->getPrimary_key())
                        {
                            $cli->printStringCustom("FIELD id is present in INTEGER and PRIMARY KEY ".KCli::br(),KCli::$F_CYAN);                              
                        }
                        else
                        {
                            //Not Primary Key Yet
                            $cli->printStringCustom("FIELD id is present but it's not a PRIMARY KEY ".KCli::br(),KCli::$F_RED);
                            //$query="ALTER TABLE ".$array_1[$l]." ADD PRIMARY KEY(`".$this->mandatory_field_id."`);";               
                            if($sql->setFieldIdPK($array_1[$l]))
                            {
                                $cli->printStringCustom("TABLE ".$array_1[$l]." Has now PRIMARY KEY ID ".KCli::br(),KCli::$F_BLUE);
                                $updated_in_db=true;
                            }
                            else
                            {
                                $cli->printStringCustom("SQL ERROR =>".$sql->getError().KCli::br(),KCli::$F_RED);
                                $cli->printStringCustom("You have to manage this error directly in the database".KCli::br(),KCli::$F_YELLOW);
                                $status2=false;                                                   
                            }                                
                        }
                        
                        // CHECK AUTOINCREMENT
                        if(!$field->getAuto_increment() && !$updated_in_db)
                        {
                            $cli->printStringCustom("FIELD id is present but it's not a AUTOINCREMENT ".KCli::br(),KCli::$F_RED);   
                            if($sql->setFieldIdAutoIncrement($array_1[$l]))
                            {
                                $cli->printStringCustom("FIELD id it's now AUTOINCREMENT ".KCli::br(),KCli::$F_CYAN);   
                                $status2=true;
                            }
                            else
                            {
                                $cli->printStringCustom("FIELD id CANNOT CHANGE IT TO AUTOINCREMENT CHECK DB ".KCli::br(),KCli::$F_RED);  
                                $status2=false;
                            }
                        }
                        else
                        {
                            $cli->printStringCustom("FIELD id is also AUTO INCREMENT ".KCli::br(),KCli::$F_CYAN);   
                        }
                        
                        
//                        }
                        /*
                        else
                        {
                            // NOT INTEGER 11
                            $cli->printStringCustom("FIELD id is present but it's not an INTEGER(11) ".KCli::br(),KCli::$F_RED);
                            $query="ALTER TABLE ".$array_1[$l]." CHANGE `".$this->mandatory_field_id."` `".$this->mandatory_field_id."` INT(11) UNSIGNED NOT NULL";               
                            if($sql->request_SQL($query))
                            {
                                $cli->printStringCustom("FIELD id converted ".KCli::br(),KCli::$F_BLUE);
                            }
                            else
                            {
                                $cli->printStringCustom("SQL ERROR =>".$sql->getError().KCli::br(),KCli::$F_RED);
                                $cli->printStringCustom("You have to mange this error directly in the database".KCli::br(),KCli::$F_YELLOW);
                                $status2=false;                                                   
                            }
                            
                            if($field->getPrimary_key())
                            {
                                $cli->printStringCustom("FIELD id is PRIMARY KEY ".KCli::br(),KCli::$F_CYAN);  
                            }
                            else if($status2)
                            {
                                //Not Primary Key Yet
                                $query="ALTER TABLE ".$array_1[$l]." ADD PRIMARY KEY(`".$this->mandatory_field_id."`);";               
                                if($sql->request_SQL($query))
                                {
                                    $cli->printStringCustom("TABLE ".$array_1[$l]." Has now PRIMARY KEY ID ".KCli::br(),KCli::$F_BLUE);
                                }
                                else
                                {
                                    $cli->printStringCustom("SQL ERROR =>".$sql->getError().KCli::br(),KCli::$F_RED);
                                    $cli->printStringCustom("You have to manage this error directly in the database".KCli::br(),KCli::$F_YELLOW);
                                    $status2=false;                                                   
                                }
                            }
                            else
                            {
                                $cli->printStringCustom("FIELD id is present but it's not PRIMARY KEY ".KCli::br(),KCli::$F_RED);
                            }
                        }
                         * 
                         */
                    }
                    else
                    { 
                        // NOT INTEGER 11 AT ALL
                        $status2=false;
                        $cli->printStringCustom("FIELD id is present but it's not an INTEGER ".KCli::br(),KCli::$F_RED);
                        $cli->printStringCustom("You have to manage this error directly in the database".KCli::br(),KCli::$F_YELLOW);
                    }
                }
            } 
            
            if(!$found)
            {
                $cli->printStringCustom("FIELD id NOT FOUND !!! ".KCli::br(),KCli::$F_RED);
                $cli->printStringCustom("You have to launch add_columns first".KCli::br(),KCli::$F_YELLOW);
                $status2=false;
            }            
            
            if(!$status2)
            {
                
                $status=false;
            }
        }        
        return $status;
    }    
    
    /**
     * 
     * @param array<mixed,mixed> $source
     * @param array<mixed,mixed> $target
     * @return array<mixed,mixed>
     */
    private function findMissingFields(array $source, array $target): array
    {
        // Remove null values from source (optional depending on your need)
        $source = array_filter($source, fn($v) => $v !== null);

        // Find values in source that are NOT in target
        $missing = array_diff($source, $target);

        // Reindex array (optional)
        return array_values($missing);
    }    
    
        
    /**
     * 
     * @param KCli $cli
     * @return array<int,KTablesToManage>
     */
    private function checkColumns(KCli $cli) : array
    {
        $arrayTables=array();
        //$arrayFieldObliged=array($this->mandatory_field_id,$this->mandatory_field_date_created,$this->mandatory_field_date_modified);
        $sql=new Sql();
        //$sql->init_DB(Sql::$POSTGRES,"localhost","postgres","Zar4SQL359#pol","manu_test");
        $cli->printString(KCli::br()."CHECKING COLUMNS IN DATABASE\n---------------- ".KCli::br().$sql->getConnectionParametersString().KCli::br());
        $sql->connect_DB();

        $tilde='`';
        $schema='';
        if($sql->getEngine_type()==Sql::$POSTGRES)
        {
            $tilde='';
            $schema=$sql->getSchema().".";
        }  
        
        $array_1=array();
        $array_2=array();
        $results=$sql->getAllTablesNamesInArray();
        foreach ($results as $tableName)
        {
            $array_1[]=$tableName;
        }

        $cli->printString("FOUND => ".count($array_1)." TABLES ".KCli::br()."---------------- ".KCli::br());
        
        for($l=0; $l<count($array_1); $l++)
        {
            $arrayFieldObliged=array($this->mandatory_field_id,$this->mandatory_field_date_created,$this->mandatory_field_date_modified);
            $cli->printString("TABLE ".($l+1)." => ".$array_1[$l]);
            $results=$sql->showColumnInformation($array_1[$l]);
            //$arrayFieldMandatory=[];
            $arrayFields=[];
            foreach($results as $field)
            {
                $arrayFields[]=$field->getName();             
            }
            $arrayFieldMandatory=$this->findMissingFields($arrayFieldObliged,$arrayFields);
            
            if(count($arrayFieldMandatory)==0)
            {
                $cli->printStringCustom(" => OK".KCli::br(),KCli::$F_CYAN);
                //$cli->printString(" => OK".KCli::br());
            }
            else
            {
                $cli->printStringCustom(" => KO".KCli::br(),KCli::$F_RED);
                $arrayTables[]=new KTablesToManage($array_1[$l],$arrayFieldMandatory);
            }
            foreach ($arrayFieldMandatory as $fieldObliged)
            {
                $field_to_add=$fieldObliged;
                $cli->printString("Field to ADD => ".$field_to_add.KCli::br());
            }  
        }
        $sql->disconnect_DB();
        return $arrayTables;
    }

    public function buildFiles(KCli $cli) : void
    {

        $CURRENT_DIR=$this->getDirectoryOfTheApp();
        $DIRECTORY_1=$CURRENT_DIR."/db/abstractdb/";
        $DIRECTORY_2=$CURRENT_DIR."/db/normaldb/";

        $dir1=new KFile($CURRENT_DIR);
        $dir1->mkdir();
        $dir3=new KFile($DIRECTORY_2);
        $dir3->mkdir();
        $dir2=new KFile($DIRECTORY_1);
        $dir2->mkdir();

        $arrayNotMakeMembers=array("id","date_created","date_modified");


        $sql=new Sql();
        //$sql->init_DB(Sql::$POSTGRES,"localhost","postgres","Zar4SQL359#pol","manu_test");
        $cli->printString(KCli::br()."CHECKING DATABASE\n---------------- ".KCli::br().$sql->getConnectionParametersString().KCli::br());

        $sql->connect_DB();

        $tilde='`';
        $schema='';
        if($sql->getEngine_type()==Sql::$POSTGRES)
        {
            $tilde='';
            $schema=$sql->getSchema().".";
        }


        $array_1=array();
        $array_tablename=array();
        $array_Klink=array();
        $array_Klinked=array();
        $array_Klink_Krank=array();
        
        $array_Krank=array();
        $array_not_Klink=array();
        $array_not_Klink_FK=array();
        $array_not_Klink_FK_Multiple=array();
        
        // GET TABLES NAMES
        $results=$sql->getAllTablesNamesInArray();
        foreach ($results as $tableName)
        {
            $array_1[]=$this->makeClassName($tableName);
            $array_tablename[]=($tableName);
            if(substr($tableName, 0, 6 ) === $this->klink_table_prefix)
            {
                $array_Klink[]=$tableName;
            }
            else
            {
                $array_not_Klink[]=$tableName;
            }
        }
        
        
        //GET LINKED TABLES
        foreach($array_Klink as $tableItem)
        {
            $arrayTemporaryKLinked=array();
            // je check
            $results=$sql->showColumnInformation($tableItem);
            /* @var $field SqlField */    
            //$field=new SqlField();
            foreach($results as $field)
            {
                if(substr($field->getName(), 0, 6 ) === $this->fk_id_prefix)
                {
                    $potentialTables=explode($this->fk_id_prefix,$field->getName());                    
                    foreach($potentialTables as $fk_id_tables)
                    {
                        //echo $tableItem."//".$fk_id_tables."\n";
                        if(in_array($fk_id_tables,$array_tablename))
                        {
                            //echo "YES\n";
                            $arrayTemporaryKLinked[]=$fk_id_tables;
                        }
                    }                    
                }
                // Check if Rank exists
                else if($field->getName()===$this->klink_krank)
                {
                    $array_Klink_Krank[]=$tableItem;
                }
            }
            
            if(count($arrayTemporaryKLinked)>0)
            {
                $array_Klinked[$tableItem]=$arrayTemporaryKLinked;
            }
        }
        
        
        //GET FOREIGN KEY IN NORMAL TABLES TABLES
        foreach($array_not_Klink as $tableItem)
        {
            $arrayTemporary=array();
            $arrayTemporaryMultiple=array();
            // je check
            $results=$sql->showColumnInformation($tableItem);
            /* @var $field SqlField */    
            //$field=new SqlField();
            foreach($results as $field)
            {
                if(substr($field->getName(), 0, 6 ) === $this->fk_id_prefix)
                {
                    
                    $arrayMultipleFK=explode("_",$field->getName());
                    $potential_number=$arrayMultipleFK[count($arrayMultipleFK)-1];
                    if(isInteger($potential_number))
                    {
                        //FK_ID_NAME_TABLE => MULTIPLE
                        $temp_field_name=str_replace($this->fk_id_prefix, "", $field->getName());
                        $temp_field_name=str_replace("_".$potential_number, "", $temp_field_name);
                        
                        if(in_array($temp_field_name,$array_tablename) &&$temp_field_name!=$tableItem )
                        {
                            $arrayTemporaryMultiple[]=array($temp_field_name,$potential_number);
                        }
                    }
                    else
                    {
                        // NORMAL FK_ID_NAME_TABLE
                    
                        $potentialTables=explode($this->fk_id_prefix,$field->getName());
                        foreach($potentialTables as $fk_id_tables)
                        {
                            //echo $tableItem."//".$fk_id_tables."\n";
                            if(in_array($fk_id_tables,$array_tablename)) // && strcmp($fk_id_tables,$tableItem)!=0 )
                            {
                                //echo $tableItem."//".$fk_id_tables."||".$field->getName()." ==> YES\n\n";
                                //echo "YES\n";
                                $arrayTemporary[]=$fk_id_tables;
                            }
                        }     
                    }
                }
                // Check if Rank exists
                else if($field->getName()===$this->klink_krank)
                {
                    $array_Krank[]=$tableItem;
                }
            }
            
            if(count($arrayTemporary)>0)
            {
                $array_not_Klink_FK[$tableItem]=$arrayTemporary;
            }
            
            if(count($arrayTemporaryMultiple)>0)
            {
                $array_not_Klink_FK_Multiple[$tableItem]=$arrayTemporaryMultiple;
            }
        }
        
//        echo print_r($array_not_Klink_FK);
//        exit();
//        echo "######\n";
//        echo print_r($array_not_Klink_FK);
//        echo "\n--------\n";
//        echo print_r($array_Krank);
//        echo "######\n";
//        exit();
        $cli->printString(KCli::br(2)."FOUND => ".count($array_1)." TABLES \n---------------- ".KCli::br());

        
        
        for($l=0; $l<count($array_1); $l++)
        {
            $type_class="KObject";
            //##################################################################
            $klinkTable=false;
            $FOUND=" NO";
            if(in_array($array_tablename[$l],$array_Klink))
            {
                $klinkTable=true;
                $FOUND=" TRUE";
                $type_class="KLinkObject";
            }
            
            //##################################################################
            
            $cli->printString("TABLE ".($l+1)." => ".$array_1[$l]." Klinked? ".$FOUND.KCli::br());

            $class_name=$array_1[$l];
            $the_table_name=$array_tablename[$l];
            

            //##############################################################################
//
            $fieldsDb='
    public static string $TABLE_NAME="'.$the_table_name.'";';

            $fieldsDbArrayValue=array();
            $fieldsClass='';
            
            $constructorDeclaration='';


            $fieldsDbArray=array();

            $fieldsDbArrayGetter=array();
            $fieldsDbArrayGetterThis=array();
            $fieldsDbArrayGetterReturn=array();
            $fieldsDbArrayGetterCast=array();

            $fieldsDbArrayType=array();
            $fieldsDbArrayComment=array();



//$query = "SHOW FULL COLUMNS FROM `$the_table_name`";
//echo "Get Fields From ".$the_table_name.KCli::br();
            $results=$sql->showColumnInformation($the_table_name);

            
//echo print_r($results);
//exit;
            $initMapFieldName="";
            $stringFkIdFields="";
            /* @var $field SqlField */
            foreach($results as $field)
            {
                $fieldsDbArrayType[]=$field;
//    echo print_r($field);
                //echo "==>".$field->getName()."<==\n";
                
                //CHECK if field is FK id
                

                $stringFkIdFields.=$this->makeStringFkIdFields($the_table_name,$array_tablename,$field,$klinkTable);

                //exit();
                if(!in_array($field->getName(),$arrayNotMakeMembers))
                {
                    $fieldsDb.="
    public static string $".toUpper($field->getName())."=\"".$field->getName()."\";";
                    
                }
                
                $initMapFieldName.=' 
        $this->addFieldName(static::$'.toUpper($field->getName()).');';
                //exit();

                $value_fieldsClass=$field->getDefault();

                if(!in_array($field->getName(),$arrayNotMakeMembers))
                {
                    $fieldsClass.="
    private mixed $".strtolower($field->getName())."=NULL;";
                    $constructorDeclaration.="
        $".strtolower($field->getName())."=new ";
                            
                }

                $fieldsDbArray[]="static::$".toUpper($field->getName());
                // insert fields value    
                $fieldsDbArrayValue[]='$sql->real_escape_string("".$this->get'.ucfirst($field->getName()).'())';

                //get'.$fieldsDbArrayGetter[$i].'()
                // GETTERS AND SETTERS
                $fieldsDbArrayGetter[]=ucfirst($field->getName());
                $fieldsDbArrayGetterThis[]=$field->getName();
                $fieldsDbArrayGetterReturn[]=$field->getTypeReturnPHP();
                $fieldsDbArrayGetterCast[]=$field->getCastTypePHP();

                //TYPE
                //echo $row['Type']."\n";

                if(($field->getComment())!=NULL)
                {
                    //echo "\n\n OK => ".$row['Comment']." \n\n";
                    $fieldsDbArrayComment[]=$field->getComment();
                }
                else
                {
                    $fieldsDbArrayComment[]="";
                }
            }
            
            
            $printKlinkTables="";
            $stringKTables="";
            $printKTablesGetters="";
            //getForeignNotKlinkTables
            $arrayForeignNotKlinkTables=[];
            if($klinkTable)
            {
                if(array_key_exists($array_tablename[$l], $array_Klinked))
                {
                //echo print_r($array_Klinked);
                //echo print_r($array_Klinked[$array_2]);
                    foreach($array_Klinked[$array_tablename[$l]] as $tableLinked)
                    {
                    $tmpTableLinked=$this->makeClassName($tableLinked);
                    //Epoque_Fiche_Type
                    $printKlinkTables.='
    public function get'.$tmpTableLinked.'() : ?'.$tmpTableLinked.'
    {
        $item=$this->getKObjectFromFk('.$tmpTableLinked.'::class);
        if(!is_null($item) &&$item instanceOf '.$tmpTableLinked.')
        {
            return $item;
        }
        return null;
    }
';
                    $stringKTables.='
        $this->makeKlinkObjectItem('.$tmpTableLinked.'::class,self::$FK_ID_'.strtoupper($tableLinked).');';
                    
                    }
                }
               
            }
            else
            {
                // list Klink Tables linking this tables
                foreach($array_Klinked as $key => $tableLinked)
                {
                    //echo "Test =>".$the_table_name."//".$tableLinked."\n";
                    foreach($tableLinked as $tlink)
                    {
                        //echo "Test =>".$the_table_name."//".$tlink."##".$key."\n";
                        if($tlink===$the_table_name)
                        {
//                            echo "ok =>".$the_table_name."//".$key;
//                           exit();
                            //$tmpTableName=$this->makeClassName($the_table_name);
                            $stringKTables.='
        $this->makeKlinkObjectTableItem('.$this->makeClassName($key).'::class,'.$this->makeClassName($key).'::$FK_ID_'.strtoupper($the_table_name).');';                            
                    
                            $orderString="";
                            if(in_array($key,$array_Klink_Krank))
                            {
                                $orderString='
        if(is_null($order))
        {
            $order= new SqlOrder('.$this->makeClassName($key).'::$'.$this->klink_krank_static.',SqlOrder::$ASC);  
        }
';                                
                            }

                                $printKTablesGetters.='
    public function getMap_'.$this->makeClassName($key).'(?SqlOrder $order=null) : HashMap
    {'.$orderString.'
        return $this->getMapOfKlinkTableOjects('.$this->makeClassName($key).'::class,$order);
    }  
';
                        }                      
                    }
                }
                
                $tmp1='';
                // For table with FK id but there are not Klinks
                if(array_key_exists($array_tablename[$l],$array_not_Klink_FK))
                {
                    $tablesFK=$array_not_Klink_FK[$array_tablename[$l]];
                    /** @phpstan-ignore-next-line */
                    if(!is_null($tablesFK))
                    {
                        foreach($tablesFK as $tableFK)
                        {
                            $tmp1=$this->makeClassName($tableFK);
                            $tmp2=$this->makeClassName($array_tablename[$l]);
                        $printKlinkTables.='
    public function get'.$tmp1.'() : ?'.$tmp1.'
    {
        /** @phpstan-ignore-next-line */
        return $this->getForeignKeyKObject('.$tmp2.'::$FK_ID_'.strtoupper($tmp1).');
    }
    ';                       
                        }
                    }  
                }
                
                // For table with FK id Multiples but there are not Klinks
                if(array_key_exists($array_tablename[$l],$array_not_Klink_FK_Multiple))
                {
                    $status_fk_multiple=array();
                    $status_fk_multiple_name_method="";
                    $tablesFK=$array_not_Klink_FK_Multiple[$array_tablename[$l]];
                    /** @phpstan-ignore-next-line */
                    if(!is_null($tablesFK))
                    {
                        foreach($tablesFK as $tableFK)
                        {
                            /** @phpstan-ignore-next-line */
                            if(count($tableFK)==2)
                            {
                                $status_fk_multiple_name_method=$tmp1;
                                $tmp1=$this->makeClassName($tableFK[0]);                           
                                $tmp2=$this->makeClassName($array_tablename[$l]);
                                $tmp3=$this->makeClassName($tableFK[0])."_".$tableFK[1]; 
                                $printKlinkTables.='
    public function get'.$tmp3.'() : ?'.$tmp1.'
    {
        /** @phpstan-ignore-next-line */
        return $this->getForeignKeyKObject('.$tmp2.'::$FK_ID_'.strtoupper($tmp3).');
    }
    ';                       
                                $status_fk_multiple[]='
        $map->put('.$tmp2.'::$FK_ID_'.strtoupper($tmp3).',$this->getForeignKeyKObject('.$tmp2.'::$FK_ID_'.strtoupper($tmp3).'));';
                                
                            }
                        }
                    }
                    /** @phpstan-ignore-next-line */
                    if(count($status_fk_multiple))
                    {
                        $printKlinkTables.='
    public function getEvery'.$status_fk_multiple_name_method.'() : HashMap
    {
        $map=new HashMap();';                                          
                        foreach ($status_fk_multiple as $string)
                        {
                            $printKlinkTables.=$string;                            
                        }
                        
                        $printKlinkTables.='
        return $map;
    }                        
    ';    
                    }
                }                
                
                
                 // For tables which are linked by foreign tables
                 foreach($array_not_Klink_FK as $value_table => $table_not_Klink_FK)
                 {
                    foreach($table_not_Klink_FK  as $foreign_table_not_Klink_FK)
                    {
                        if($foreign_table_not_Klink_FK==$array_tablename[$l])
                        {
                            $tmp1=$this->makeClassName($value_table);
                            $orderString='';
                            if(in_array ($value_table,$array_Krank))
                            {
                                $orderString='
        if(is_null($order))
        {
            $order=new SqlOrder('.$tmp1.'::$'.$this->klink_krank_static.',SqlOrder::$ASC);
        }
        ';
                                // is ranked
                            }
                            
                        $printKlinkTables.='
    public function getAll_'.$tmp1.'(?SqlOrder $order=null) : HashMap
    {'.$orderString.'
        return $this->getMapOfForeignTableOjects('.$tmp1.'::class,'.$tmp1.'::$'.strtoupper($this->fk_id_prefix.$array_tablename[$l]).',$order);
    }
    ';                             
                            $arrayForeignNotKlinkTables[]=[$tmp1.'::class',$tmp1.'::$'.strtoupper($this->fk_id_prefix.$array_tablename[$l])];
                        }
                    }
                 }             
            }
            
            
            $string='<?php declare(strict_types=1); 
/**
 * Description of Models of '.$class_name.'Db ==> SQL TABLE '.$the_table_name.'
 *
 * @author Mulot Louis
 */
abstract class '.$class_name.'Db extends '.$type_class.'
{ 
// private class fields 
'.$fieldsClass.'

// DB Static fields
'.$fieldsDb.'

    protected function __construct()
    {
        parent::__construct();
        $this->setTable_name("'.$the_table_name.'");
        $this->setAlias_sql("'.$the_table_name.'");
        $this->initCustom();
    }
    public function __destruct()
    {
    }
    public function initCustom() : void
    {'.$stringFkIdFields.''.$stringKTables.'
    }
    protected function initMapFieldName() : bool
    {'.$initMapFieldName.'
        return true;
    }
    //--------------------------
    // GETTER && SETTER
    //--------------------------
';

            $string_d="";
            for($i=0; $i<count($fieldsDbArrayGetter); $i++)
            {
                if(!in_array(strtolower($fieldsDbArrayGetter[$i]),$arrayNotMakeMembers))
                {
                    $filterValue="";
                    $filterValueSql="";
                    if($fieldsDbArrayType[$i]->getTypeName()==SqlFieldType::$BOOL)
                    {
                        $filterValue="convertToBool";
                        $filterValueSql='$sql->convertToBool';
                    }
                    
                    
                    $string_d.='
    public function get'.$fieldsDbArrayGetter[$i].'ForSql(Sql $sql) : mixed
    {
        return '.$filterValueSql.'($this->'.$fieldsDbArrayGetterThis[$i].');
    }  
    
    public function get'.$fieldsDbArrayGetter[$i].'()'.$fieldsDbArrayGetterReturn[$i].'
    {
        return '.$fieldsDbArrayGetterCast[$i].'$this->'.$fieldsDbArrayGetterThis[$i].';
    }
    
    public function set'.$fieldsDbArrayGetter[$i].'(mixed $'.$fieldsDbArrayGetterThis[$i].',bool $verification=true) : bool
    {
        if(!$verification)
        {
            $this->'.$fieldsDbArrayGetterThis[$i].' = '.$filterValue.'($'.$fieldsDbArrayGetterThis[$i].');
            return true;
        }
        if($this->setKFieldValue(static::$'.toUpper($fieldsDbArrayGetterThis[$i]).',$'.$fieldsDbArrayGetterThis[$i].'))
        {
            $this->'.$fieldsDbArrayGetterThis[$i].' = '.$filterValue.'($'.$fieldsDbArrayGetterThis[$i].');
            return true;
        }
        return false;
    }
';
                }
            }
            $string.=$string_d;


            $string_d="
    //--------------------------
    // FOR POST AND GET
    //--------------------------
";
            for($i=0; $i<count($fieldsDbArrayGetter); $i++)
            {
                $string_d.='
    public function getInputName_'.$fieldsDbArrayGetter[$i].'() : string
    {
        return  $this->getInputValueFieldName(static::$'.toUpper($fieldsDbArrayGetterThis[$i]).');
    }
    
    public function getInputGetValue_'.$fieldsDbArrayGetter[$i].'() : bool
    {
        return $this->getInputValueByGet(static::$'.toUpper($fieldsDbArrayGetterThis[$i]).');
    }
    
    public function getInputPostValue_'.$fieldsDbArrayGetter[$i].'()  : bool
    {
        return $this->getInputValueByPost(static::$'.toUpper($fieldsDbArrayGetterThis[$i]).');
    }
';
                
            }
            
            $string.=$string_d;
   
            $string.=$this->makeDeclarationInitKFields($fieldsDbArrayType);
            
            $string.=$printKlinkTables;
            
            
            
            $string.=$printKTablesGetters;
            
            $string.=$this->makeForeignNotKlinkTablesArray($arrayForeignNotKlinkTables);

            $string.='
}
';
       
        
            $file=new printToFile($string,$DIRECTORY_1.$class_name."Db.class.php");

            //echo KCli::br()." 1)".$class_name."Db.class.php ==> DONE !! ==".$DIRECTORY_1.$class_name."Db.class.php";
            $cli->printStringCustom(" 1)".$class_name."Db.class.php ==> DONE !! ==".$DIRECTORY_1.$class_name."Db.class.php".KCli::br(),KCli::$F_CYAN);

            $file2=new KFile($DIRECTORY_2.$class_name.".class.php");
            if(!$file2->exists())
            {
                $addcode="";
                if(strcmp($class_name,"Kapp_Users")==0)
                {
                    $addcode=$this->makeCodeForUser();
                }
                else if(strcmp($class_name,"Kapp_Groups")==0)
                {
                    $addcode=$this->makeCodeForGroup();
                }                
                $string='<?php declare(strict_types=1); 
/**
 * Description of Class derivation from '.$class_name.'Db ==> SQL TABLE '.$the_table_name.'
 *
 * @author Mulot Louis
 */
class '.$class_name.' extends '.$class_name.'Db
{
    '.$addcode.'
    //################################
    // !!!!! DO NOT CHANGE THIS !!!!!
    //################################
    public function __construct()
    {
        parent::__construct();
    }
    //################################
    
    // HERE YOU CAN ADD OR REDEFINES METHODS
    //--------------------------------------
}
';
                $file=new printToFile($string,$file2->getPath());
                //echo KCli::br()." 2)".$class_name.".class.php ==> DONE !!";
                $cli->printStringCustom(" 2)".$class_name."Db.class.php ==> DONE !! ==".$DIRECTORY_2.$class_name.".class.php".KCli::br(),KCli::$F_CYAN);
            }
        }
        
        
        //DELETE OLD FILES 
        $cli->printString(KCli::br(2)."CHECKING FILES TO BE DELETED\n---------------- ".KCli::br());
        
        if(!$this->deleteOldFiles($cli,$array_1,$DIRECTORY_1,"Db.class.php"))
        {
            $cli->printString("NONE".KCli::br());
        }
        if(!$this->deleteOldFiles($cli,$array_1,$DIRECTORY_2,".class.php"))
        {
            $cli->printString("NONE".KCli::br());
        }        
        
        
        $sql->disconnect_DB();
        
        $this->removeCaches();
    }
    
    /**
     * 
     * @param array<int,SqlField> $fieldsDbArrayType
     * @return string
     */
    private function makeDeclarationInitKFields(array $fieldsDbArrayType)
    {
        $primary_key=false;
        $string='
    public function initKFields() : bool
    {
        $this->setInitKFields(true);
';
        for($i=0; $i<count($fieldsDbArrayType); $i++)
        {
            /* @var $field SqlField */
            $field=$fieldsDbArrayType[$i];
            if($field->getTypeName()==SqlFieldType::$VARCHAR)
            {
                $string.='
        $kField=new KFieldVarChar();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setLength('.$field->getLength().');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefaultString().');
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');
        $this->addKField($kField);
';
            }
            else if($field->getTypeName()==SqlFieldType::$TEXT)
            {
                $string.='
        $kField=new KFieldText();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefaultString().');
        $this->addKField($kField);
';
            }
            else if($field->getTypeName()==SqlFieldType::$DATETIME
                    ||$field->getTypeName()==SqlFieldType::$DATE
                    ||$field->getTypeName()==SqlFieldType::$TIME
                    ||$field->getTypeName()==SqlFieldType::$TIMESTAMP
                    ||$field->getTypeName()==SqlFieldType::$YEAR
                    )
            {
                if($field->getTypeName()==SqlFieldType::$DATETIME)
                {
                    $string.='
        $kField=new KFieldDateTime();';                   
                }
                else if($field->getTypeName()==SqlFieldType::$DATE)
                {
                    $string.='
        $kField=new KFieldDate();';                      
                }
                else if($field->getTypeName()==SqlFieldType::$TIME)
                {
                    $string.='
        $kField=new KFieldTime();';                      
                }
                else if($field->getTypeName()==SqlFieldType::$TIMESTAMP)
                {
                    $string.='
        $kField=new KFieldTimeStamp();';                      
                }
                else if($field->getTypeName()==SqlFieldType::$YEAR)
                {
                    $string.='
        $kField=new KFieldYear();';                      
                }                
                $string.='
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefaultString().');
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');
        $this->addKField($kField);
';
            }            
            else if($field->getTypeName()==SqlFieldType::$INTEGER)
            {
                
                $string.='
        $kField=new KFieldInteger();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setAuto_increment_ByInt('.$field->getAuto_increment_byInt().');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefault().');
        $kField->setUnsigned('.$field->getUnsigned().');';
                
                if(!$primary_key&&$field->getPrimary_key())
                {
                    $primary_key=true;
                $string.='
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');';                    
                }
                else
                {
                $string.='
        $kField->setPrimary_key_ByInt(0);';                     
                }
                
                if($field->getForeign_key())
                {
                $string.='
        $kField->setForeign_key_byInt(1);
        $kField->setForeign_key_table("'.$field->getForeign_key_table().'");';                         
                }
                
                $string.='
        $this->addKField($kField);
';
            }
            else if($field->getTypeName()==SqlFieldType::$DOUBLE)
            {
                $string.='
        $kField=new KFieldDouble();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefault().');
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');
        $this->addKField($kField);
';
            } 
            else if($field->getTypeName()==SqlFieldType::$FLOAT)
            {
                $string.='
        $kField=new KFieldFloat();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefault().');
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');
        $this->addKField($kField);
';
            } 
            else if($field->getTypeName()==SqlFieldType::$BOOL)
            {
                $string.='
        $kField=new KFieldBool();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefault().');
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');
        $this->addKField($kField);
';
            } 
            else if($field->getTypeName()==SqlFieldType::$GEOMETRY
                    ||$field->getTypeName()==SqlFieldType::$POINT
                    ||$field->getTypeName()==SqlFieldType::$LINESTRING
                    ||$field->getTypeName()==SqlFieldType::$POLYGON
                    ||$field->getTypeName()==SqlFieldType::$MULTIPOINT
                    ||$field->getTypeName()==SqlFieldType::$MULTILINESTRING
                    ||$field->getTypeName()==SqlFieldType::$MULTIPOLYGON
                    )
            {
                $string.='
        $kField=new KFieldGeometry();
        $kField->setName(static::$'.toUpper($field->getName()).');
        $kField->setIs_null_ByInt('.$field->getIs_nullValue().');
        $kField->setDefault('.$field->getDefaultString().');
        $kField->setPrimary_key_ByInt('.$field->getPrimary_key_byInt().');
        $kField->setGeometry_type("'.$field->getTypeName().'");
        $kField->setSrid("'.$field->getSrid().'");
        $kField->setCoord_dimension("'.$field->getCoord_dimension().'");
        $this->addKField($kField);
';
            }
            else
            {
                echo "ERROR FIELD TYPE => ".$field->getTypeName()."".KCli::br();
                echo $field->toString();
                exit();
            }
        }
        $string.='
        return true;
    }
';
        return $string;
    }
    
    /**
     * 
     * @param KCli $cli
     * @param array<int,string> $arrayName
     * @param string $directory
     * @param string $extension_name
     * @return bool
     */
    private function deleteOldFiles(KCli $cli,array $arrayName,string $directory,string $extension_name)
    {
        $cli->printString(KCli::br(2)."CHECKING DIRECTORY => ".$directory."\n---------------- ".KCli::br());
        //return true;
        $found_one=false;
        $dir=new KFile($directory);
        if($dir!=null&&$dir->isDirectory())
        {
            $files=$dir->listFilesNameToArray();
            if($files)
            {
                foreach($files as $file)
                {
                    $found=false;
                    //$cli->printString("CHECKING => ".$file.KCli::br());
                    foreach($arrayName as $objectName)
                    {
                        //$cli->printString("CHECKING => ".$file."//".$objectName.$extension_name.KCli::br());
                        if($objectName.$extension_name==$file)
                        {
                            $found=true;
                            break;
                        }
                    }
                    if(!$found)
                    {
                        //delete it
                        $delete_file=new KFile($directory.$file);
                        $delete_file->delete();
                        $cli->printStringCustom("DELETE => ".$directory.$file." TABLES".KCli::br(),KCli::$F_CYAN);
                        $found_one=true;
                    }
                }
            }
        }
        return $found_one;
    }
    
    /**
     * 
     * @param string $the_table_name
     * @param array<int,string> $array_tables
     * @param SqlField $field
     * @param bool $klinkTable
     * @return string
     */
    private function makeStringFkIdFields(string $the_table_name, array $array_tables, SqlField $field,bool $klinkTable)
    {
        $string ="";
        //echo "FIELD ==>".$field."\n";
        if(stringStartsWith($field->getName(),$this->fk_id_prefix))
        {
            foreach($array_tables as $table)
            {
                //if(stringStartsWith($field,"fk_id_".$table."_"))
                //echo "COMPARE ".$field." AND "."fk_id_".$table."\n";
                if(stringStartsWith($field->getName(),$this->fk_id_prefix.$table."") && strcmp($the_table_name,$table)!=0)
                {
                    $field_rest=str_replace($this->fk_id_prefix.$table."","",$field->getName());
                    //echo "OK ".$field->getName()." AND "."fk_id_".$table."-> ".$field_rest."\n";
                    if(strlen($field_rest)==0||$this->multipleFK($field_rest))
                    {
                        $field->setForeign_key_ByInt(1);
                        $field->setForeign_key_table($table);
                        if(!$klinkTable)
                        {
                        $string.='
        $object=new '.$this->makeClassName($table).'();
        $object->setAlias_sql($this->getAliasPrefix().self::$'.toUpper($field->getName()).');
        $this->getMapForeignKeyFields()->put(self::$'.toUpper($field->getName()).',$object);';
                        }
                    }
                }
            }
        }
        return $string;
    }
       
    /**
     * 
     * @param array<int,array<int, string>> $arrayForeignNotKlinkTables
     * @return string
     */
    public function makeForeignNotKlinkTablesArray(array $arrayForeignNotKlinkTables) : string
    {
        if(count($arrayForeignNotKlinkTables))
        {
            $string='';
            foreach ($arrayForeignNotKlinkTables as $stringForeignTable)
            {
                //$arrayForeignNotKlinkTables[]=[$tmp1.'::class"',$tmp1.'::$'.strtoupper($this->fk_id_prefix.$array_tablename[$l])];
                $string.='$array[]=['.$stringForeignTable[0].','.$stringForeignTable[1].'];';
            }
            return '
    /**
    * @return array <int,array<int,string>>
    */
    public function getForeignNotKlinkTables() : array
    {
        $array=[];
        '.$string.'
        return $array;
    }                
';
        }
        return "";
    }
    
    public static function makeClassName(string $string) : string
    {
        return KObject::makeClassNameFromTableName($string);
    }

//    public function setAppFolder($cli)
//    {
//        
//    }
    
    public static function getAppFolder(?string $appFolderString=null) : string
    {   
        $appFolder="App";
        if(!is_null($appFolderString))
        {
            $appFolder=$appFolderString;
        }
        else
        {
            $appFolder=self::readFileToGetAppFolder(__DIR__.KFile::separator()."../../"."app.txt");
        }
        return $appFolder; 
    }
    
    private static function readFileToGetAppFolder(string $path) :?string
    {
        $appFolder=null;
        $file=new KFile($path);
        //echo __DIR__.KFile::separator()."../../"."app.txt"; 
        //exit();
        if($file->exists()&&$file->isFile())
        {
            $handle = fopen($file->getPath(), "r");
            if($handle)
            {
                while (($line = fgets($handle)) !== false) 
                {
                    $line=trim($line);
                    if(!stringStartsWith($line,"#"))
                    {
                        if(string_contains("=", $line))
                        {
                            $array=explode ("=" ,$line ,2);
                            if($array!=null&&count($array)==2)
                            {
                                if($array[0]=="AppFolder")
                                {
                                    $appFolder=$array[1];
                                }
                            }
                        }
                    }
                }
            }
            fclose($handle); 
        }  
        return $appFolder;
    }
    
    public static function getAppFolderFromRoot() : string
    {   
        $appFolder=self::readFileToGetAppFolder(__DIR__);
        if(!is_null($appFolder))
        {
            return $appFolder;
        }
        return ""; 
    }

    private function makeCodeForUser() : string
    {
        $string='
        use UserTrait;
';
       return $string;
    }
    
    private function makeCodeForGroup() : string
    {
        $string='
        use GroupTrait;
';
       return $string;
    }    
    
    private function makeDefaultINCLUDEApp(string $directory,KFile $dir) : bool
    {
        $content='<?php declare(strict_types=1); 
require_once("../../K-app/include.php");

$folderApp=KManageDb::getAppFolder("'.$directory.'");
$kApp=new KApp($folderApp);
           
';
        $file= new KFile($dir->getPath().KFile::separator()."includeApp.php");
        return $this->stringToFile($file,$content);        
    }
    
    private function makeDefaultWWWIndexFile(KFile $dir) : bool
    {
        $content='<?php declare(strict_types=1);    
require_once("../includeApp.php");
KDebugger::enable();

KRoute::launchDefaultRoute();

//#### ENTER YOUR CODE HERE



//#### END OF YOUR CODE

KRoute::endDefaultRoute();           
';
        $file= new KFile($dir->getPath().KFile::separator()."index.php");
        return $this->stringToFile($file,$content);
    }
    
    private function makeDefaultWWWActionFile(KFile $dir) : bool
    {
        $content='<?php declare(strict_types=1); 
require_once("../includeApp.php");
KDebugger::enable();
KRoute::stayActionConnected();
//KRoute::activateManageUsers();
//KRoute::launchDefaultActionRoute();
KRoute::endDefaultActionRoute();
';        
        $file= new KFile($dir->getPath().KFile::separator()."action.php");
        return $this->stringToFile($file,$content);        
    }    

    private function makeDefaultROUTESFile(KFile $dir) : bool
    {
        $content='<?php declare(strict_types=1);
class RoutesItems extends KRoutesItems
{
    // ADD NEW ROUTES ITEMS
    //##################################
    public static string $HOME="home";
    public static string  $CONNECTION="connection";
    public static string  $DECONNECTION="deconnection";
    public static string  $BASIC_VIEW="basic_view";
    public static string  $TEST_JSON="test_json";
    
    
    // END NEW ROUTES ITEMS
    //##################################
    
    //--------------------------------------------------------------------------
    public function __construct() 
    {
        parent::__construct();
    }    
}
';        
        $file= new KFile($dir->getPath().KFile::separator()."RoutesItems.class.php");
        return $this->stringToFile($file,$content);          
    } 
    
    private function makeDefaultMIDDLEWAREFile(KFile $dir) : bool
    {
        $content='<?php declare(strict_types=1);
class test_middleware extends KMiddleware
{
    function __construct() 
    {
        
    }
    function __destruct() 
    {
        
    }    
    public function handle() : bool
    {
        return true;
    }
    public function terminate() : bool
    {
        return true;
    }
}
';        
        $file= new KFile($dir->getPath().KFile::separator()."test_middleware.class.php");
        return $this->stringToFile($file,$content);                 
    }

    private function makeDefaultCONFIGFile(KFile $dir) : bool
    {
  $content='<?php declare(strict_types=1);
// LIST VARIABLE NEEDED BY THE FRAMEWORK
//ParamManager::getInstance()->sql_host="";
//ParamManager::getInstance()->add();
//ParamManager::getInstance()->get();


//##############################################################################
//
//              LOCAL PARAM
//
//##############################################################################


ParamManager::getInstance()->add("PERSO","VARIABLE");


//##############################################################################
//
//              ParamManager 
//
//##############################################################################

// DEBUG
//#######################
if(ParamManager::getInstance()->debug)
{
	
}
else
{
	
}

// SITE NAMES
//#######################
ParamManager::getInstance()->app_name="APP_NAME";
ParamManager::getInstance()->site_title="The html title";
ParamManager::getInstance()->debug=true;
ParamManager::getInstance()->site_session_name="K-APP_SESSION";


// URLS
//#######################
ParamManager::getInstance()->site_root="http://localhost/www/index.php";
ParamManager::getInstance()->site_action="http://localhost/www/action.php";
ParamManager::getInstance()->server_url="http://localhost/";
ParamManager::getInstance()->server_name="localhost";


//DATABASE
//#######################
ParamManager::getInstance()->sql_engine=Sql::$POSTGRES;
ParamManager::getInstance()->sql_host="localhost";
ParamManager::getInstance()->sql_user="user";
ParamManager::getInstance()->sql_database="database";      
ParamManager::getInstance()->sql_pass="password";


//ANALYTICS
//#######################


// EMAILS
//#######################
ParamManager::getInstance()->error_email="error_email@domain.tld"; 
ParamManager::getInstance()->public_email="public_email@domain.tld";


// EMAIL EXCHANGE PARAM
//#######################
ParamManager::getInstance()->email_server=""; //"{domain.tld:993/imap/ssl/novalidate-cert}"; // using IMAP for storing with self signed
ParamManager::getInstance()->email_smtp_server="domain.tld";
ParamManager::getInstance()->email_transport="smtp"; // smtp gmail null
ParamManager::getInstance()->email_mail="public_email@domain.tld";   
ParamManager::getInstance()->email_username="DOMAIN\public_email";
ParamManager::getInstance()->email_password="password";
ParamManager::getInstance()->email_port="587"; // 25 587 ... 
ParamManager::getInstance()->email_encryption="ssl"; // tls, ssl, or null
ParamManager::getInstance()->email_auth_mode="login"; // plain, login, cram-md5, ntlm or null.
ParamManager::getInstance()->email_verify_peer="true"; // if self sign certificate set false

// LOGS
//#######################
ParamManager::getInstance()->log_dir_mail="/dir_log_email/";
ParamManager::getInstance()->log_directory="/dir_log_dir/";


';        
        $file= new KFile($dir->getPath().KFile::separator()."config.php");
        return $this->stringToFile($file,$content);         
    } 

    private function stringToFile(KFile $file,string $content) : bool
    {         
        $printFile=new printToFile($content,$file->getPath());
        return $file->exists() && $file->isFile();
    }
    
    private function multipleFK(string $rest) : bool
    {
        if(substr( $rest, 0, 1 ) === "_")
        {
            $array=explode("_", $rest);
            if(count($array)==2)
            {
                return isInteger($array[1]);
            }
        }
        return false;
    }
    
    /**
     * 
     * @param array<int,string> $array
     * @return array<int,string>
     */
    private function leftRotatebyOne(array $array) :array
    { 
        $newArray=[];
        $temp = $array[0]; 
        $count=0;
        for ($i = 0; $i < count($array) - 1; $i++) 
        {
            $newArray[$i] = $array[$i + 1]; 
            $count=$i;
        }
        $count++;
        $newArray[$count] = $temp;
        return $newArray; 
    } 
    /**
     * 
     * @param array<int,string> $array
     * @param string $separator
     * @return string
     */
    private function arrayToString(array $array,string $separator="_") : string
    {
        $string="";
        foreach ($array as $item)
        {
            if($string!="")
            {
                $string.=$separator;
            }
            $string.=$item;
        }
        return $string;
    }
    
    private function getDirectoryOfTheApp() : string
    {
        return $this->getDirectoryOfTheFramework().$this->app_directory;
    }
    
    private function getDirectoryOfTheFramework() : string
    {
        return __DIR__.KFile::separator()."..".KFile::separator()."..".KFile::separator();
    }
}