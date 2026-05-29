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
class ImportRecordsByXLS extends AbstractUploadXLSFileForRecords
{
      
    public function execute(): bool
    {
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS")." => ".$this->getTablename(),"fa fa-download",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
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
                        new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_UPLOAD_TITLE")),
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
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }
            if($col<1)
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }            
            
            $firstLine=$xlsFile->getFirstLine();
            
            $col_first_line=count($firstLine);
            
            if($col_first_line<1 )
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_9")." => ".$col_first_line." != ".$col, KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;                
            }            
            
            $kobject=$this->getKObject();
            //$kobject->getMapFieldName();
            $array_error_col_unknown=[];
            foreach ($firstLine as $column)
            {
                if(!$kobject->fieldNameExists($column))
                {
                    $array_error_col_unknown[]=$column;
                }
            }
            
            if(count($array_error_col_unknown))
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_3"). convertArrayStringsToString($array_error_col_unknown), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            }
            

            
            $arrayKobjects=[];
            $array_error_init_field=[];
            $objectName=$this->getObjectName();
            $col_first_line=count($firstLine);
            for($i=2; $i<=$row ; $i++)
            {
                /* @var $tempKobject KObject */
                $tempKobject=new $objectName();
                for ($j=1;$j<=$col_first_line ;$j++)
                {
                    if(!$tempKobject->setFieldValue($firstLine[$j-1], "".$xlsFile->getCellValue($j, $i)))
                    {
                        $array_error_init_field[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_4")." $i // ".LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_5")." $j => ".$tempKobject->getKerror();
                    }
                    //$this->addString("$i:$j".$xlsFile->getCellValue($j, $i)."//".$firstLine[$j]."<br />");                   
                }
                $arrayKobjects[]=$tempKobject;
            }
              
            
            $array_error_insert_object=[];
            if(count($array_error_init_field))
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_6"). convertArrayStringsToString($array_error_init_field,"<br />"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);                
            }
            else
            {
                Sql::getInstance()->beginTransaction();
                $line=1;
                foreach ($arrayKobjects as $tempKobject)
                {
                    $line++;
                    if(!$tempKobject->insert())
                    {
                        $array_error_insert_object[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_7")." $line :".$tempKobject->getKerror();
                        break;
                    }
                }
                
                if(count($array_error_insert_object))
                {
                    Sql::getInstance()->rollbackTransaction();
                    $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_6"). convertArrayStringsToString($array_error_insert_object,"<br />").LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_8"), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($message);                
                }  
                else
                {
                    Sql::getInstance()->commitTransaction();
                    $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_UPLOAD_SUCCESS_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_UPLOAD_SUCCESS_TEXT").count($arrayKobjects)." <br />", KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($message); 
                }              
            }
        }
    }
}