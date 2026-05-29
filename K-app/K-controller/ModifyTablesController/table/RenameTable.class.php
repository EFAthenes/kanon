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
class RenameTable extends AbstractTableController
{
    private const string COL_NEW_NAME="col_new_name";
    private const string FORM_ID="renameTable";
    private string $col_new_name="";
    private ?FormComponent $form=null;

    public function execute(): bool
    {  
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
                
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE")." => ".$this->getTablename(),"fa fa-signature",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        
        if(!$this->checkPostResponse())
        {
            HistoryPage::getInstance()->deleteLast();
            //KDebugger::getInstance()->dump(self::$GET_PARAM_TABLENAME,"self::ET_PARAM_TABLENAME");
            //KDebugger::getInstance()->dump($this->col_new_name,"this->col_new_name");
            $hist_url=HistoryPage::getInstance()->getLastKUrl();
            if(!is_null($hist_url))
            {
                $hist_url->addOrReplace(self::$GET_PARAM_TABLENAME, $this->col_new_name);
            }
            $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE")." => ".$this->col_new_name,"fa fa-signature");
            return true;
        }
        
        
        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_TITLE")));
        $grid->addRowComponent();   
        $grid->addColComponent(new InputStringComponent($this->col_new_name,
                self::COL_NEW_NAME,
                LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_FIELD"),
                LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_CONDITION "),
                true,false,2,10));
        $grid->addRowComponent();        
        $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_COLUMN_ACTION"), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
        $this->form->addComponent($grid);
        $this->addComponent($this->form);
        

        $this->addComponent(new ShowListOfTablesNames());


        return true;
    }
    
    private function checkPostResponse() : bool
    {
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::COL_NEW_NAME, KInput::$VARIABLE_STRING, $this->col_new_name))
        {
            if(!isAlphaNumericAndUndescore($this->col_new_name) ||!isPhpVariableNameCompliant($this->col_new_name))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_LABEL_ERROR"),
                        LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }                       
            else if(in_array($this->col_new_name,Sql::getInstance()->getAllTablesNamesInArray()))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_LABEL_ERROR"),
                        LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }
            else
            {  
                if(Sql::getInstance()->renameTable($this->getTablename(),$this->col_new_name))
                { 
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_SUCCESS")
                            , "OK => '".$this->getTablename()."' => '".$this->col_new_name."' !!!", KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($notify);
                    
                    
                    $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());
                    
                    HistoryPage::getInstance()->replaceArgInLast(self::$GET_PARAM_TABLENAME,$this->col_new_name);
                    
                    $cli= new KCli();
                    $cli->outputToBuffer(true);                    
                    $manage->buildFiles($cli);
                    $report=$cli->getBufferToString();
                    $manage->removeCaches();
                    $area=new TextAreaComponent($report,"","Log","", false, true);
                    $this->addComponent($area);
                    
                    //$this->initTableName($this->col_new_name);
                    //$this->initKobject();                       
                    //$this->col_new_name="";
                    return false;
                }
                else
                {
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE_LABEL_ERROR"), Sql::getInstance()->getError(), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($notify);                       
                }
            }
        }
        return true;        
    }
    
}
