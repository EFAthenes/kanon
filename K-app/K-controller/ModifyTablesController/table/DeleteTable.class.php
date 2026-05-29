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
class DeleteTable extends AbstractTableController
{
    private const string CONFIRM_ACTION="confirm_action";
    private const string  FORM_ID="DeleteTable";
    private int $confirm_action=0;
    private ?FormComponent $form=null;

    public function execute(): bool
    {  
        if(!$this->checkTableName() || ! $this->initKobject())
        {
            return true;
        }
        
        KApp::getInstance()->getLayout()->setTitle($this->getTablename());
        $this->setTitleAndBackButton(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE")."=>".$this->getTablename(),"far fa-trash-alt",KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]));
        
        $url=new KURL();
        $this->form=new FormComponent($url->printURLWithoutAmp(), self::FORM_ID);
        
        if(!$this->checkPostResponse())
        {
            $url=KRoute::makeURL(KRoutesItems::$SHOW_ALL_TABLES);
            $this->getBackButton()->setActionURL($url);
            $grid=new TileGridComponent();
            $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_SUCCESS_TITLE")));
            $grid->addRowComponent();
            
            $grid->addColComponent(
                    (new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_SUCCESS_LINK"), 
                            ButtonComponent::$TYPE_PRIMARY,null, false, false))
                        ->setActionURL($url)
                    );      
            $this->addComponent($grid);
            return true;
        }
        
        //DESIGN UI
        //#############
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_TITLE")));
        $grid->addRowComponent();        
        $grid->addColComponent(new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_ACTION"), ButtonComponent::$TYPE_PRIMARY,null, false, false, self::FORM_ID));      
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
            if(Sql::getInstance()->deleteTable($this->getTablename()))
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_SUCCESS")
                        , LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_SUCCESS_MESSAGE"), KAlertComponent::$TYPE_SUCCESS);
                $this->addComponent($notify);  
                
                
                $manage=new KManageDb(KApp::getInstance()->getAppDirectoryName());
                    
                $cli= new KCli();
                $cli->outputToBuffer(true);                    
                $manage->buildFiles($cli);
                if(!$manage->removeCaches())
                {
                    $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_ERROR")
                            ,"Problem deleting file caches", KAlertComponent::$TYPE_SUCCESS);
                    $this->addComponent($notify);                     
                }
                $report=$cli->getBufferToString();
                $area=new TextAreaComponent($report,"","Log","", false, true);
                $this->addComponent($area);
                               
                return false;
            }
            else
            {
                $notify = new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE_ERROR")
                        , Sql::getInstance()->getError(), KAlertComponent::$TYPE_SUCCESS);
                $this->addComponent($notify);   
                return false;
            }
        }   
        return true;
    }
}