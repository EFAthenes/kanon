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
class CreateTable extends AbstractTableController
{
    private const string FORM_ID="createTable";
    private const string TABLE_NAME="table_name";
    private ?string $table_value=null;
    private ?FormComponent $form=null;

    public function execute(): bool
    {  

        KApp::getInstance()->getLayout()->setTitle(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE"));
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE"),"fa fa-copy"); 
        
        
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        $this->checkPostResponse();
        
        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_TITLE")));
        $grid->addRowComponent();
        $grid->addColComponent(new InputStringComponent($this->table_value,self::TABLE_NAME,LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_1"),LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_2"),true,false,2,10));
        $grid->addRowComponent();
        $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_BUTTON_LABEL"), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));
        $this->form->addComponent($grid);
        $this->addComponent($this->form);
        
   
        return true;
    }
    
    private function checkPostResponse() : void
    {
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::TABLE_NAME, KInput::$VARIABLE_STRING, $this->table_value))
        {
            if(!isAlphaNumericAndUndescore($this->table_value) ||!isPhpVariableNameCompliant($this->table_value))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_ERROR"),
                        LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }                       
            else if(in_array($this->table_value,Sql::getInstance()->getAllTablesNamesInArray()))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_ERROR"),
                        LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_ERROR_2"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }
            else
            {  
                if(Sql::getInstance()->createTable($this->table_value)
                        && Sql::getInstance()->addFieldId($this->table_value)
                        && Sql::getInstance()->addFieldDateCreated($this->table_value)
                        && Sql::getInstance()->addFieldDateModified($this->table_value)
                        /*&& Sql::getInstance()->setFieldIdPK($this->table_value)*/)
                { 
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_SUCCESS")
                            , LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_SUCCESS_1")." ".$this->table_value." ".LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_SUCCESS_2"), KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($notify);
                    
                    
                    $button =new ButtonComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_SUCCESS_BUTTON"), ButtonComponent::$TYPE_PRIMARY, "fa fa-greater-than");
                    $button->setActionURL(KRoute::makeKURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->table_value])->printURLWithoutAmp());
                    $this->addComponent($button);
                    $this->addComponent(new BreakLineComponent(2));
                    
                    
                    $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());
                    
                    HistoryPage::getInstance()->replaceArgInLast(self::$GET_PARAM_TABLENAME,$this->table_value);
                    
                    $cli= new KCli();
                    $cli->outputToBuffer(true);                    
                    $manage->buildFiles($cli);
                    $report=$cli->getBufferToString();
                    $manage->removeCaches();
                    $area=new TextAreaComponent($report,"","Log","", false, true);
                    $this->addComponent($area);
                    
                    return ;
                }
                else
                {
                    //isTableExisting
                    if(Sql::getInstance()->isTableExisting($this->table_value))
                    {
                        Sql::getInstance()->deleteTable($this->table_value);
                    }
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_ALL_TABLES_NEW_TABLE_LABEL_ERROR"), Sql::getInstance()->getError(), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($notify);                       
                }
            }
        }        
    }
    
}
