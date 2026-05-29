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
/**
 * Description of EditTableGroups
 *
 * @author Mateo
 * @author louis.mulot
 * @author Hippolyte
 */
class KEditTableGroups extends UtilsEditTable
{

    public function init(): void
    {
//        $this->CREATION_ID=Modifications::CREATE_GROUP;
//        $this->MODIFICATION_ID=Modifications::UPDATE_GROUP;
    }

    public function execute(): bool
    {
        if($this->firstProcessingExecute())
        {
            return true;
        }
        $form=new KFormGroupComponent(RoutesItems::$KEDIT_GROUPS,[KObject::$ID=>($this->getObject()->getId()),self::$GET_NEW=>$this->new]);
        $form->initForm($this->getGroup());
        $this->addComponent($form);
        return true;
    }
    
    protected function getGroup() : Kapp_Groups
    {
        /* @var $obj Kapp_Groups */
        $obj=$this->getObject();
        if(is_null($obj)||!($obj instanceof Kapp_Groups))
        {
            $obj=new Kapp_Groups();
            $this->setObject($obj);
        }
        return $obj;
    }

    protected function initObject(): bool
    {
        $this->getGroup();
        KInput::checkInputGet(self::$GET_NEW,KInput::$VARIABLE_BOOL,$this->new);
        return $this->new ? true : $this->loadExistingGroup();
    }

    protected function makeTitle(): void
    {
         $this->setRouteAndIdForm(RoutesItems::$KSHOW_GROUPS,"form_edit_group");
        $this->new ? $this->makeTitleNew() : $this->makeTitleEdit();
        $this->addString($this->addJSActionButtonDelete(RoutesItems::$KMANAGE_GROUPS,RoutesItems::$KSHOW_GROUPS,self::$ACTION_DELETE));
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->title);
        KApp::getInstance()->getLayout()->setTitle($this->title->getTitle());
    }

    protected function makeTitleEdit(string $routeShow="",string $idForm=""): void
    {
        $this->setRouteAndIdForm($routeShow, $idForm);
        $this->title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_TITLE "),"fa fa-users");
        $kurl=new KURL();
        $kurl->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        $kurl->removeArg(self::$GET_NEW);
        $kurl->addOrReplace(Kapp_Groups::$ID,$this->getGroup()->getId());
        $this->title->setKurl($kurl);
        $backButton=new KTitleButton(LanguageManager::_("GDA_BACK_TO_LIST"),KTitleButton::$TYPE_INFO,"fa fa-backward");
        $backButton->setActionURL(KRoute::makeURL(RoutesItems::$KSHOW_GROUPS));
        $this->title->addKTitleButton($backButton);
        $updateButton=new KTitleButton(LanguageManager::_("KAPP_USER_LABEL_BUTTON"),KTitleButton::$TYPE_SUCCESS,"fa fa-check");
        $updateButton->setSubmitForm($this->getIdForm());
        $this->title->addKTitleButton($updateButton);
        $deleteButton=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_DELETE"),KTitleButton::$TYPE_DANGER,"fa fa-trash");
        $deleteButton->setClickAction("addJSActionButtonDelete(".$this->getGroup()->getId().")");
        $this->title->addKTitleButton($deleteButton);
    }

    private function loadExistingGroup(): bool
    {
        $sql=Sql::getInstance();
        $group_id=0;
        if((!KInput::checkInput(KInput::$INPUT_GET,KObject::$ID,KInput::$VARIABLE_INT,$group_id))||(!$this->getGroup()->initById($group_id)))
        {
            $this->addComponent(new KAlertComponent(
                    LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                    LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_7")." '".$group_id."' "
                        .LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_8")."<br />"
                        .$this->getGroup()->getKerror()."<br /><br />".$sql->getQuery()
                        ."<br /><br />",KAlertComponent::$TYPE_ERROR));
            return false;
        }
        return true;
    }

    protected function checkIfIsCorrect(): bool
    {
        if(!is_string($this->getGroup()->getLabel())||preg_match('/[\'^£$%&*[()}\'\"\`\/\\\{\]@#~!:;.?><>,|=_+¬]/',$this->getGroup()->getLabel())||$this->getGroup()->getLabel()=="")
        {
            $this->displayErrorMessage("KAPP_GROUP_MODIFICATIONS_ERROR_2");
            return false;
        }

        /*
        if(!is_string($this->getGroup()->getDescription())||preg_match('/[\'^£$%&*[()}\'\"\`\/\\\{\]@#~!:;.?><>,|=_+¬]/',$this->getGroup()->getDescription())||$this->getGroup()->getDescription()=="")
        {
            $this->displayErrorMessage("KAPP_GROUP_MODIFICATIONS_ERROR_3");
            return false;
        }
         * 
         */
        return true;
    }

}