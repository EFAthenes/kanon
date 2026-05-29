<?php
declare(strict_types=1);
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
class FormEditKapp_UsersComponent extends FormComponent
{
    /**
     * 
     * @var array<int,string>
     */
    private ?array $fieldsReadOnly=null;

    public static string $MODIFY_PASS="new_pass";
    public static string $MODIFY_PASS_2="new_pass_2";
    
    /**
     * 
     * @var array<string,string>
     */
    private array $disallowFields=[];
    /**
     * 
     * @var array<string,string>
     */
    private array $readOnlyFields=[];
    
    /**
     * 
     * @param string $url_form
     * @param array<string,mixed>|null $ids
     */
    function __construct(string $url_form, ?array $ids = [])
    {        
        $url = KRoute::makeKURL($url_form, $ids);
        parent::__construct($url->printURLWithoutAmp(), 'form_edit_user');
    }
    
    /**
     * 
     * @param array<int,string> $disallowFields
     * @return void
     */
    public function disallowFields(array $disallowFields) : void
    {
        foreach ($disallowFields as $field)
        {
            $this->disallowFields[$field]=$field;
        }
    }
    public function disallowField(string $fieldName) : void
    {
        $this->disallowFields[$fieldName]=$fieldName;
    }
    
    /**
     * 
     * @param array<string,string> $readOnlyFields
     * @return void
     */
    public function readOnlyFields(array $readOnlyFields) : void
    {
        foreach ($readOnlyFields as $field)
        {
            $this->readOnlyFields[$field]=$field;
        }
    }
    public function readOnlyField(string $fieldName) : void
    {
        $this->readOnlyFields[$fieldName]=$fieldName;
    }    
    
    /**
     * 
     * @return array<int,string>
     */
    protected function getFieldsReadOnly() : array
    {
        return $this->fieldsReadOnly;
    }
            
    
    function initForm(Kapp_Users $user,?bool $groups = false) : void
    {

        $row = new RowComponent();
        $this->addComponent($row);
        $component=new DivClassComponent("col-12");
        $row->addComponent($component);
        $tile = new TileComponent();
        $component->addComponent($tile);
        
        $this->fieldsReadOnly=[Kapp_Users::$ID,Kapp_Users::$DATE_CREATED,Kapp_Users::$DATE_MODIFIED];
        $user->initKFields();
        if(!array_key_exists(Kapp_Users::$ID,$this->disallowFields))
        {
            $tile->addComponent($this->makeInput($user->getInputValueFieldName(Kapp_Users::$ID),$user->getId(),LanguageManager::_("KAPP_USER_LABEL_ID"),true));
        }
        if(!array_key_exists(Kapp_Users::$FIRST_NAME,$this->disallowFields))
        {        
            $tile->addComponent($this->makeInput($user->getInputValueFieldName(Kapp_Users::$FIRST_NAME),$user->getFirst_name(),LanguageManager::_("KAPP_USER_LABEL_PRENOM"),$this->isFieldReadOnly(Kapp_Users::$FIRST_NAME)));
        }
        if(!array_key_exists(Kapp_Users::$LAST_NAME,$this->disallowFields))
        {
            $tile->addComponent($this->makeInput($user->getInputValueFieldName(Kapp_Users::$LAST_NAME),$user->getLast_name(),LanguageManager::_("KAPP_USER_LABEL_NOM"),$this->isFieldReadOnly(Kapp_Users::$LAST_NAME)));           
        }
        if(!array_key_exists(Kapp_Users::$EMAIL,$this->disallowFields))
        {        
            $tile->addComponent($this->makeInput($user->getInputValueFieldName(Kapp_Users::$EMAIL),$user->getEmail(),LanguageManager::_("KAPP_USER_LABEL_MAIL"),$this->isFieldReadOnly(Kapp_Users::$EMAIL)));
        }
        if(!array_key_exists(self::$MODIFY_PASS,$this->disallowFields))
        {
            $tile->addComponent($this->makeInputNewPassword(self::$MODIFY_PASS,LanguageManager::_("KAPP_USER_LABEL_PASS")));
            $tile->addComponent($this->makeInputNewPassword(self::$MODIFY_PASS_2,LanguageManager::_("KAPP_USER_LABEL_PASSN")));
        }

        if($groups) //Lecture et ecriture
        {
            $tile->addComponent($this->makeKSelectComponent($user->getInputValueFieldName(Kapp_Users::$TIMEZONE), DateTimeZone::listIdentifiers(DateTimeZone::ALL), 
                    [empty($user->getTimezone()) ? $user->getKField(Kapp_Users::$TIMEZONE)->getDefaultString() : $user->getTimezone()],
                    LanguageManager::_("KAPP_USER_LABEL_TIMEZ"),false));
            $tile->addComponent($this->makeKSelectComponent("groupes", $this->getGroups(), $user->getGroupsId(), "Groupes", false));
        }
        else //ReadOnly
        {
            $tile->addComponent($this->makeInput($user->getInputValueFieldName(Kapp_Users::$TIMEZONE),$user->getTimezone(),LanguageManager::_("KAPP_USER_LABEL_TIMEZ"),true));
            $tile->addComponent($this->makeInput("groupes", $this->printUserGroups($user->getGroupsId()), "Groupes", true));
        }
          
        $tile->addComponent(            
                new KSelectComponent(
                        $user->getInputValueFieldName(Kapp_Users::$LANGUAGE), 
                        LanguageManager::getInstance()->getArrayOfLanguages(), 
                        $user->getLanguage(), 
                        false,
                        LanguageManager::_("KAPP_USER_LABEL_LANGUAGE"),
                        false,3,6));                      
        $tile->addComponent($this->makeInput("",$user->getDate_created(),LanguageManager::_("KAPP_USER_LABEL_CREA"),true));
        $tile->addComponent($this->makeInput("",$user->getDate_modified(),LanguageManager::_("KAPP_USER_LABEL_MODIF"),true));
    }
    
    private function isFieldReadOnly(string $fieldName) : bool
    {
        if(array_key_exists($fieldName,$this->readOnlyFields))
        { 
            return true;
        }          
        return false;
    }
    
    private function makeInput(string $name_input,mixed $field_value,string $field_label,bool $readonly=false) : KComponent
    {                
        $comp =new InputStringComponent("".$field_value, $name_input, $field_label,"",false, $readonly, 3,6);
        $comp->setAutocomplete(false);
        return $comp;
    }
    
    private function makeInputNewPassword(string $name_input,string $field_label) : KComponent
    {      
        $comp =new InputStringComponent("", $name_input, $field_label,"",false,false, 3,6);
        $comp->setAutocomplete(false);   
        $comp->setInputType("password");
        return $comp;
    }   

    /**
     * 
     * @param string $name_input
     * @param array<mixed,mixed> $array
     * @param array<mixed,mixed>|null $user_groups
     * @param string $field_label
     * @param bool $multiple
     * @return KComponent
     */
    private function makeKSelectComponent(string $name_input,array $array,?array $user_groups,string $field_label, bool $multiple) : KComponent
    {
        $kselect = new KSelectComponent($name_input, $array, $user_groups, $multiple,$field_label,false,3,6);
        return $kselect;
    }
    
    /**
     * 
     * @return array<int,array<int,string>>
     */
    private function getGroups() : array
    {
        $groups_list = new DbList(Kapp_Groups::class);
        $groups = $groups_list->getAll();
        $array = [];
        
        foreach ($groups as $group)
        {
            $array[] = [$group->getId(), $group->getLabel()];
        }
        
        return $array;
    }
    
    /**
     * 
     * @param array<int,mixed> $groupes
     * @return string
     */
    private function printUserGroups(array $groupes) : string
    {
        $group_list = new DbList(Kapp_Groups::class);
        $string = "";
        foreach($groupes as $groupe)
        {
            $label = $group_list->getDistinctField(Kapp_Groups::$LABEL, [new QueryField(Kapp_Groups::$ID, $groupe)]);   
            $string .= $label->getLast();
            if($groupe != end($groupes))
            {
                $string .= " / ";
            }
        }      
        return $string;
    }
}