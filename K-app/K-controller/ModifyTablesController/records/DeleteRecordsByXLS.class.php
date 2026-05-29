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
class DeleteRecordsByXLS extends AbstractUploadXLSFileForRecords
{
    public function execute(): bool
    {
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS")." => ".$this->getTablename(),"fa fa-trash",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
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
                        new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_TITLE")),
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
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }
            if($col<1)
            {
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }            
            
            //$firstLine=$xlsFile->getFirstLine();
            if(!$xlsFile->findColumnBool(KObject::$ID))
            {
                //NEED ID COLUMN
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_3"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;                
            }
            $arrayOfIds=$xlsFile->getColumnContent(KObject::$ID,false);
            
            if(is_null($arrayOfIds)||count($arrayOfIds)==0)
            {
                //NO ID PRESENTS
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_4"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;                 
            }
            
            $array_error_id_not_found=[];
            $array_error_during_delete=[];
            $objectName=$this->getObjectName();
            $line=1;
            Sql::getInstance()->beginTransaction();
            /* @var $tempKobject KObject */
            foreach($arrayOfIds as $id)
            {
                $line++;
                $tempKobject= new $objectName();
                if(!$tempKobject->initById($id))
                {
                    $array_error_id_not_found[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_5").$id." ".LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_6")." ".$line;
                    break;
                }
                else
                {
                    if(!$tempKobject->deleteAll())
                    {
                        $array_error_during_delete[]=LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_7").$id." ".LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_6")." ".$line." => ".$tempKobject->getKerror();
                        break;
                    }
                }
            }
     
            
            if(count($array_error_id_not_found))
            {
                Sql::getInstance()->rollbackTransaction();
                //ID NOT FOUND
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_8"). convertArrayStringsToString($array_error_id_not_found,"<br />"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);                
            }
            else if(count($array_error_during_delete))
            {
                Sql::getInstance()->rollbackTransaction();
                //ERROR WHEN DELETING
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_ERROR_8"). convertArrayStringsToString($array_error_during_delete,"<br />"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);                
            }            
            else
            {
                // ALL DELETE
                Sql::getInstance()->commitTransaction();
                $message=new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_SUCCESS_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS_SUCCESS_TEXT").count($arrayOfIds)." <br />", KAlertComponent::$TYPE_SUCCESS);
                $this->addComponent($message);                              
            }
        }
    }
}