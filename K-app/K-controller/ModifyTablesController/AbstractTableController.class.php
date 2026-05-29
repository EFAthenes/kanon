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
abstract class AbstractTableController extends KController
{
    public static string $GET_PARAM_TABLENAME="tablename";
    private ?string $tablename=null;
    private ?string $objectName=null;
    private ?KObject $kObject=null;
    /**
     * 
     * @var array<int,string>|null
     */
    private ?array $arrayFieldsName=null;
    private ?KTitleButton $backButton=null;
    private ?KTitleLayoutAdmin $ktitle=null;   
    
    public function checkTableName() : bool
    {
        $tablename="";
        if(!KInput::checkInput(KInput::$INPUT_GET, self::$GET_PARAM_TABLENAME,KInput::$VARIABLE_STRING,$tablename))
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                        LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_1"),
                        KAlertComponent::$TYPE_ERROR));              
            return false;
        }
        if(!Sql::getInstance()->isTableExisting($tablename))
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                        LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_2")." '".$tablename."' ".LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_3"),
                        KAlertComponent::$TYPE_ERROR));               
            return false;           
        }

        return $this->initTableName($tablename);
    }
    
    public function initTableName(string $tablename) : bool
    {
        $this->objectName=KObject::makeClassNameFromTableName($tablename);
        
        if(!class_exists($this->objectName))
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_5")." '' ".$this->objectName." '' ". LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_6"),
                KAlertComponent::$TYPE_ERROR));            
            return false;            
        } 
        $this->tablename=$tablename;
        return true;
    }
    
    public function initKobject() : bool
    {        
        /* @var $class KObject */
        $this->kObject= new $this->objectName();    
        if($this->kObject instanceof  KObject)
        {
            $this->kObject->initByDefault();
            $this->arrayFieldsName=$this->kObject->fieldsNonObligatoryLabelToArray(false);      
            return true;
        }
        return false;
    }
    
    
    public function getTablename(): ?string
    {
        return $this->tablename;
    }

    public function setTablename(string $tablename) : void
    {
        $this->tablename = $tablename;
    }
    
    public function getObjectName() : ?string
    {
        return $this->objectName;
    }

    public function setObjectName(string $objectName) : void
    {
        $this->objectName = $objectName;
    }
    
    /**
     * 
     * @return array<int,string>|null
     */
    public function getArrayFieldsName() : ?array
    {
        return $this->arrayFieldsName;
    }
  
    public function getKObject() : ?KObject
    {
        return $this->kObject;
    }

    /**
     * 
     * @param string $title
     * @param string $icon
     * @param array<int,KTitleButton>|null $arrayButtons
     * @return void
     */
    public function setTitleAndButtons(string $title, string $icon="",?array $arrayButtons=null): void
    {

        $consultantComp = new TileComponent();

        $user=SessionMemory::getInstance()->getUser();   
        
            if ($user->isInGroup(Kapp_Groups::ADMIN_GROUP))
            {
                $this->ktitle=new KTitleLayoutAdmin($title,"fa ".$icon);
                $kurl=new KURL();
                $kurl->removeArg(ShowTableContent::$GET_PARAM_NEW_ITEM);
                $this->ktitle->setKurl($kurl);         
                KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->ktitle);
            }else{
                $title = new TitleComponent($title);
                $this->addComponent($title);
                // $title = new KTitleLayoutAdmin("Similitude", "fas fa-signal");
            } 
        

        if(!is_null($arrayButtons))
        {
            foreach ($arrayButtons as $button)
            {
                /** @phpstan-ignore-next-line */
                if($button instanceof KTitleButton)
                {
                    $this->ktitle->addKTitleButton($button);                         
                }
            }
        }
    }
    
    public function getKTitle() : ?KTitleLayoutAdmin
    {
        return $this->ktitle;
    }
    
    public function getBackButton() : ?KTitleButton
    {
        return $this->backButton;
    }

        
    public function setTitleAndBackButton(string $title, string $icon="",?string $back_url_button=null) : void
    {   
        if(!is_null($back_url_button))
        {
            $this->backButton= new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
            $this->backButton->setActionURL($back_url_button);               
        }
        else
        {
            $back_url=HistoryPage::getInstance()->getLastDifferent();
            $this->backButton=null;
            if(!is_null($back_url))
            {
                $this->backButton= new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
                $this->backButton->setActionURL($back_url->printURL());            
            }
        }
        $this->setTitleAndButtons($title, $icon, [$this->backButton]);
    } 
}