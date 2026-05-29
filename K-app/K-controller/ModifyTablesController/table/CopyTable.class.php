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
class CopyTable extends AbstractTableController
{
    private const string TABLE_NEW_NAME="table_new_name";
    private const string IMPORT_DATA="import_data";
    private const string FORM_ID="renameTable";
    private string $table_new_name="";
    private int $import_data=0;
    private ?FormComponent $form=null;

    public function execute(): bool
    {  
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
                
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE")." => ".$this->getTablename(),"fa fa-copy",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        
        if(!$this->checkPostResponse())
        {
            
            $grid=new TileGridComponent();
            $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_TITLE_2")));      
            $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_COLUMN_ACTION "), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
            $this->form->addComponent($grid);
            $this->form->addHiddenParameters(self::TABLE_NEW_NAME,$this->table_new_name);
            $this->form->addHiddenParameters(self::IMPORT_DATA,1);
            $this->addComponent($this->form);            
            
            
//            HistoryPage::getInstance()->deleteLast();
//            HistoryPage::getInstance()->getLastKUrl()->addOrReplace(self::$GET_PARAM_TABLENAME, $this->table_new_name);
//            $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE")." => ".$this->table_new_name,"fa fa-signature");
            return true;
        }
        
        
        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_TITLE")));
        $grid->addRowComponent();   
        $grid->addColComponent(new InputStringComponent($this->table_new_name,
                self::TABLE_NEW_NAME,
                LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_FIELD"),
                LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_CONDITION "),
                true,false,2,10));
        $grid->addRowComponent();        
        $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_COLUMN_ACTION "), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
        $this->form->addComponent($grid);
        $this->addComponent($this->form);
        

        $this->addComponent(new ShowListOfTablesNames());


        return true;
    }
    
    private function checkPostResponse() : bool
    {
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::TABLE_NEW_NAME, KInput::$VARIABLE_STRING, $this->table_new_name)
                &&KInput::checkInputPost(self::IMPORT_DATA, KInput::$VARIABLE_INT, $this->import_data)
                && $this->import_data == 1)
        {
            $temp_path_data_json=KCache::getInstance()->getPath_cache().KFile::separator()."export_data_".$this->table_new_name.".json";
            
            $load=new KLoadJSONObject();
            if($load->exportDataToFile($this->getTablename(),$temp_path_data_json)
                    &&$load->loadByFile($temp_path_data_json, $this->table_new_name))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_SUCCESS")
                    , "Import OK => '".$this->getTablename()."' => '".$this->table_new_name."' !!!", KAlertComponent::$TYPE_SUCCESS);
                $this->addComponent($notify);   
                return true;
            }     
            else
            {
                // problem
                return true;
            }
        }
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::TABLE_NEW_NAME, KInput::$VARIABLE_STRING, $this->table_new_name))
        {
            if(!isAlphaNumericAndUndescore($this->table_new_name) ||!isPhpVariableNameCompliant($this->table_new_name))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_ERROR"),
                        LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }                       
            else if(in_array($this->table_new_name,Sql::getInstance()->getAllTablesNamesInArray()))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_ERROR"),
                        LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }
            else
            {  
                $this->getKObject()->initKFields();                
                $temp_path_model_json=KCache::getInstance()->getPath_cache().KFile::separator()."export_model_".$this->table_new_name.".json";
                //$temp_path_data_json=KCache::getInstance()->getPath_cache().KFile::separator()."export_data_".$this->table_new_name.".json";
                $load=new KLoadJSONObject();
                if($this->getKObject()->exportModelToJSONFile($temp_path_model_json)
                         /*&& $load->exportDataToFile($this->getTablename(),$temp_path_data_json) */ )
                {
                    if($load->loadModelByFile($temp_path_model_json))
                    {
                        $file=new KFile($temp_path_model_json);
                        $file->delete();
                        
                        $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_SUCCESS")
                            , "OK => '".$this->getTablename()."' => '".$this->table_new_name."' !!!", KAlertComponent::$TYPE_SUCCESS);
                        $this->addComponent($notify);
                        
                        $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());
                        $cli= new KCli();
                        $cli->outputToBuffer(true);                    
                        $manage->buildFiles($cli);
                        $manage->removeCaches();
                        $report=$cli->getBufferToString();
                        $area=new TextAreaComponent($report,"","Log","", false, true);
                        $this->addComponent($area);
                        
                        return false;
                        
                    }
                    else
                    {
                        $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_LABEL_ERROR"), Sql::getInstance()->getError(), KAlertComponent::$TYPE_ERROR);
                        $this->addComponent($notify); 
                    }
                }
                else
                {
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE_ERROR"), Sql::getInstance()->getError(), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($notify);   
                }

            }
        }
        return true;        
    }
    
}
