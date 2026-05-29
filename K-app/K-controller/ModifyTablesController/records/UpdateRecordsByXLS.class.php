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
class UpdateRecordsByXLS extends AbstractUploadXLSFileForRecords
{
    public function execute(): bool
    {
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS")." => ".$this->getTablename(),"fa fa-file-download",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        $url=$this->getKTitle()->getKurl();
        $url->removeArg(self::$GET_INPUT_UPLOAD_FILE);
        $url->removeArg(KUploadFiles::UPLOAD_TOKEN);
        
        if($this->manageUploadedFiles())
        {
            $this->readXLSFile();
        }
        
        $grid= new TileGridComponent();
        $kurl =new KURL();
        $kurl->addOrReplace(self::$GET_INPUT_UPLOAD_FILE,"1");

        $grid->addColComponent(
                new KUploadFiles(
                        $kurl,
                        false,
                        new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_TITLE")),
                        "",
                        true,
                        ["xls","xlsx","XLS","XLSX"]),
                "-12");

        $this->addComponent($grid);
        return true;
    }
    
    public function readXLSFile() : void
    {
        $arrayFile=$this->getArrayFilesUploaded();

        require_once __ROOT__.'/K-lib/K-excel/KExcel.class.php';
        $xlsFile= new KExcel();
        if($xlsFile->openFile($arrayFile[0]->getPath()))
        {
            $row = $xlsFile->getMaxRow();
            $col = $xlsFile->getMaxColumn();
            //$this->addStringPrint_r("row=$row//col=$col<br />");
            if($row<2)
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }
            if($col<2)
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }            
            
            //##################################################################
            // Check ID Column
            $col_id=0;
            if(($col_id=$xlsFile->findColumn(KObject::$ID))<0)
            {
                //NEED ID COLUMN
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_3"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;                
            }
            //$this->addStringPrint_r("id col=$col_id<br />");
            
            $arrayOfIds=$xlsFile->getColumnContent(KObject::$ID,false);
            
            if(is_null($arrayOfIds)||count($arrayOfIds)==0)
            {
                //NO ID PRESENTS
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_4"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;                 
            }
            
            
            //##################################################################
            // Check Other Columns
            $firstLine=$xlsFile->getFirstLine();
            $copyFirstLine=[];
            foreach ($firstLine as $column)
            {
                $copyFirstLine[]=trim($column);
            }
            $firstLine=$copyFirstLine;
            
            $kobject=$this->getKObject();
            //$kobject->getMapFieldName();
            $array_error_col_unknown=[];
            //KDebugger::getInstance()->dump($firstLine);
            foreach ($firstLine as $column)
            {
                if(!$kobject->fieldNameExists($column))
                {
                    $array_error_col_unknown[]=$column;
                }
            }
            
            if(count($array_error_col_unknown))
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_5"). convertArrayStringsToString($array_error_col_unknown), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            }            
            //##################################################################
            
            
            
            $arrayKobjects=[];
            $array_error_id_not_found=[];
            $array_error_init_field=[];
            $objectName=$this->getObjectName();
            $line=1;
            
            //$this->addStringPrint_r($arrayOfIds);
            /* @var $tempKobject KObject */
            foreach($arrayOfIds as $id)
            {
                $line++;
                $tempKobject= new $objectName();
                if(!$tempKobject->initById($id))
                {
                    $array_error_id_not_found[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_6").$id." ".LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_8")." ".$line;
                    break;
                }
                else
                {
                    //echo print_r($firstLine,true);
                    //$this->addStringPrint_r($tempKobject);
                    
                    $max_item=$col;
                    if($col>count($firstLine))
                    {
                        $max_item=count($firstLine);
                    }
                    
                    for($j=1;$j<=$max_item ;$j++)
                    {
                        //$this->addStringPrint_r("$j - $col_id => ($col)/$line");
                        //echo "$j - $col_id => ($col)/$line <br />";
                        if($j != $col_id)
                        {
                            //echo "i=".($j-1)."==>".$firstLine[$j-1];
                            //$this->addStringPrint_r("OK");
                            //$this->addStringPrint_r($col_id."//".$firstLine[$j-1]."=>".$xlsFile->getCellValue($j, $line)."<br />");
                            if(!$tempKobject->setFieldValue($firstLine[$j-1], "".$xlsFile->getCellValue($j, $line)))
                            {
                                $array_error_init_field[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_8")." $line // ".LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_9")." $j => ".$tempKobject->getKerror();
                            }
                        }  
                        //$this->addStringPrint_r("KO");
                    }
                }
                $arrayKobjects[]=$tempKobject;
            }
     
            
            if(count($array_error_id_not_found))
            {
                //ID NOT FOUND
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),convertArrayStringsToString($array_error_id_not_found,"<br />"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);                
            }  
            else if(count($array_error_init_field))
            {
                //ID NOT FOUND
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_10"). convertArrayStringsToString($array_error_init_field,"<br />"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);                
            }              
            else
            {
                $array_error_update_object=[];
                Sql::getInstance()->beginTransaction();
                $line=1;
                foreach ($arrayKobjects as $tempKobject)
                {
                    $line++;
                    if(!$tempKobject->updateInBd())
                    {
                        $array_error_update_object[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_7")." $line :".$tempKobject->getKerror();                     
                        break;
                    }
                }
                
                if(count($array_error_update_object))
                {
                    Sql::getInstance()->rollbackTransaction();
                    $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_10"). convertArrayStringsToString($array_error_update_object,"<br />").LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_ERROR_11"), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($message);
                }
                else
                {
                    Sql::getInstance()->commitTransaction();
                    $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_SUCCESS_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_SUCCESS_TEXT").count($arrayOfIds)." <br />", KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($message);         
                }
            }
        }
    }
}