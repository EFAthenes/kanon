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
class EmptyTable extends AbstractTableController
{
    private const string CONFIRM_ACTION="confirm_action";
    private const string FORM_ID="EmptyTable";
    private int $confirm_action=0;
    private ?FormComponent $form=null;

    public function execute(): bool
    {  
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE")."=>".$this->getTablename(),"far fa-recycle",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        
        $this->checkPostResponse();
  
        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $dB=new DbList($this->getObjectName());
        $count=$dB->getNb();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_TITLE")." | ".
                LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_TITLE_2")." ".$count." ".
                LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_TITLE_3")));
        $grid->addRowComponent();        
        $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_ACTION"), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
        $this->form->addComponent($grid);
        $this->form->addHiddenParameters(self::CONFIRM_ACTION,1);
        $this->addComponent($this->form);
        

        return true;
    }
    
    private function checkPostResponse() : bool
    {
        if($this->form->isAlreadyPost()
                &&KInput::checkInputPost(self::CONFIRM_ACTION, KInput::$VARIABLE_INT, $this->confirm_action)
                &&$this->confirm_action==1)
        {
            if(Sql::getInstance()->emptyTable($this->getTablename()))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_SUCCESS")
                        , LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_SUCCESS_MESSAGE "), KAlertComponent::$TYPE_SUCCESS);
                $this->addComponent($notify);  
                
                
                $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());
                    
                $cli= new KCli();
                $cli->outputToBuffer(true);                    
                $manage->buildFiles($cli);
                $report=$cli->getBufferToString();
                $area=new TextAreaComponent($report,"","Log","", false, true);
                $this->addComponent($area);
                
                return false;
            }
            else
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE_ERROR")
                        , Sql::getInstance()->getError(), KAlertComponent::$TYPE_SUCCESS);
                $this->addComponent($notify);   
                return false;
            }
        }   
        return true;
    }
}