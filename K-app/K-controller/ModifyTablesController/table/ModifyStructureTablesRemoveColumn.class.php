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
class ModifyStructureTablesRemoveColumn extends AbstractTableController
{
    private const string COL_NAME="col_name";
    private const string FORM_ID="ModifyStructureTables";
    private string $col_value="";
    private ?FormComponent $form=null;

    public function execute(): bool
    {  
        if(!$this->checkTableName() || !$this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN"),"fa fa-minus-circle",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        
        $this->checkPostResponse();
        
        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_TITLE")));
        $grid->addRowComponent();
        $grid->addColComponent(new KSelectComponent(self::COL_NAME,$this->makeListOfFieldsNames(),null,false,LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_NEW_COL"),false,2,10));
        $grid->addRowComponent();        
        $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_ACTION"), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
        $this->form->addComponent($grid);
        $this->addComponent($this->form);
        
        $this->addComponent(new ShowStructureDbTableInTable($this->getTablename()));

        return true;
    }
    
    private function checkPostResponse() : void
    {
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::COL_NAME, KInput::$VARIABLE_STRING, $this->col_value))
        {
            
            if(!in_array($this->col_value,$this->getArrayFieldsName()))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_LABEL_ERROR"), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_ERROR_1"), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($notify);
            }
            else
            {  
                $kField=$this->getKObject()->getKField($this->col_value);
                $list= new ArrayList();
                $list->add($kField);
                if(Sql::getInstance()->dropFields($this->getTablename(),$list))
                {                
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_SUCCESS"), "OK => '".$this->col_value."' !!!", KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($notify);
                    $this->col_value="";
                    
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
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN_LABEL_ERROR"), Sql::getInstance()->getError(), KAlertComponent::$TYPE_ERROR);
                    $this->addComponent($notify);                       
                }
            }
        }        
    }
    /**
     * 
     * @return array<int,string>
     */
    public function makeListOfFieldsNames() : array
    {
        $arrayFieldsNames=[];
        $arrayColumn=Sql::getInstance()->showColumnInformation($this->getTablename());
        $obligatoryFields=$this->getKObject()->fieldsObligatoryLabelToArray(false);
        if(count($arrayColumn))
        {
            /* @var $sqlField SqlField */
            foreach ($arrayColumn as $sqlField)
            {
                if(!in_array($sqlField->getName(), $obligatoryFields))
                {
                    $arrayFieldsNames[]=$sqlField->getName();
                }
            }
        }
        return $arrayFieldsNames;
    }
}
