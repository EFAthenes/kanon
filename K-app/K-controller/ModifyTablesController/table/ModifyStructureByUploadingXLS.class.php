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
class ModifyStructureByUploadingXLS extends AbstractUploadXLSFileForRecords
{
      
    public function execute(): bool
    {
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_COLUMNS")." => ".$this->getTablename(),"fa fa-columns",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
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
                        new TitleComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_UPLOAD_TITLE")),
                        "",
                        true,
                        ["xls","xlsx","XLS","XLSX"]),
                "-12");
        
        $grid->addColComponent(new HTMLComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_LABEL")));
        $grid->addColComponent(new HTMLComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_LABEL_TYPES")));

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
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return;
            }

            $firstLine=$xlsFile->getFirstLine();
            $col_first_line=count($firstLine);
            
            if($col_first_line<3 )
            {
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;                
            }    
            
            if($firstLine[0]!="col_name" || $firstLine[1]!="col_type" || $firstLine[2]!="col_length" )
            {
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_7"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;                 
            }
            
            $kobject=$this->getKObject();
            
            //##################################################################
            // Check Col not present
            //##################################################################
            $firstCol=$xlsFile->getColumnContent("col_name",false);
            $array_error_col_known=[];
            $array_error_col_alpha=[];
            foreach ($firstCol as $fieldName)
            {
                if($kobject->fieldNameExists($fieldName))
                {
                    $array_error_col_known[]=$fieldName;
                }
                else if(!isAlphaNumericAndUndescore($fieldName))
                {
                    $array_error_col_alpha[]=$fieldName;
                }
            }
            
            if(count($array_error_col_known))
            {
                // Col connu
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_3")." ==> ". convertArrayStringsToString($array_error_col_known), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            }
            if(count($array_error_col_alpha))
            {
                // Nom de col incorrecte
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_4")." ==> ". convertArrayStringsToString($array_error_col_alpha), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            }            
            
            $fieldTypes=KField::allTypesToArray();
            $secondCol=$xlsFile->getColumnContent("col_type",false);
            $array_error_col_type=[];
            $array_error_col_length=[];
            $row_check=2;
            foreach ($secondCol as $fieldType)
            {
                if(!in_array($fieldType,$fieldTypes))
                {
                    $array_error_col_type[]=$fieldType;
                }
                $value=$xlsFile->getCellValue(3, $row_check);
                if(!empty($value) && !isInteger($value))
                {
                    $array_error_col_length[]=$value;
                }
                $row_check++;
            }
            
            if(count($array_error_col_type))
            {
                // Type de Col incorrect
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_5")." ==> ". convertArrayStringsToString($array_error_col_type), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            } 
            if(count($array_error_col_length))
            {
                // Taille de col incorrecte
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_6")." ==> ". convertArrayStringsToString($array_error_col_length), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            }
            
            $errorInitializationField=[];
            $list= new ArrayList();
            for($i=2; $i<=$row ; $i++)
            {
                $kField=KField::make($xlsFile->getCellValue(2, $i),null,["name"=>$xlsFile->getCellValue(1, $i),"length"=>$xlsFile->getCellValue(3, $i)]);
                if(!($kField instanceof KFieldUnKnown))
                {
                    $list->add($kField);
                }
                else
                {
                    $errorInitializationField[]=$i;
                }
            }
            
            if(count($errorInitializationField))
            {
                // Erreur d'initialisation de champ
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_8")." ==> ". convertArrayStringsToString($errorInitializationField), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);    
                return;
            }
            
            
            if($list->getSize()==0)
            {
                // Erreur Aucun champ a rajouté
                $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                        Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_9"), KAlertComponent::$TYPE_INFO);
                $this->addComponent($message);    
                return;
            }
            else
            {
                if(Sql::getInstance()->addFields($this->getTablename(),$list))
                {                
//                    
                    $notify = new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_TITLE"),
                            Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_TEXT")." ==> ".$list->getSize(),
                            KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($notify);
                    

                    $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());

                    $cli= new KCli();
                    $cli->outputToBuffer(true);                    
                    $manage->buildFiles($cli);
                    $report=$cli->getBufferToString();
                    $area=new TextAreaComponent($report,"","Log","", false, true);
                    $this->addComponent($area);

                    $this->initKobject();
                }
                else
                {
                    // Erreur Aucun champ a rajouté
                    $message=new KAlertComponent(Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_ERROR_TITLE"),
                            Ki18::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_MODIFY_STRUCTURE_ERROR_10")." ==> ".Sql::getInstance()->getError(), KAlertComponent::$TYPE_INFO);
                    $this->addComponent($message);    
                    return;                   
                } 
            }
        }
    }
}