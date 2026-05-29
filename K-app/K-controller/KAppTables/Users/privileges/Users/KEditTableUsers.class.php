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
 * Description of EditTableUsers
 *
 * @author louis.mulot
 */
class KEditTableUsers extends KController
{
    public static string $GET_NEW="new";
    public static string $ACTION="action";
    public static string $ACTION_DELETE="action_delete";
    public static string $ACTION_RESET_PWD="action_reset_pwd";
    public static string $CHECK_GROUPE="check_groupe";
    public static string $ACTION_IMPERSONATE="action_imper";
    private bool $new=false;
    private ?Kapp_Users $user=null;
    private ?KTitleLayoutAdmin $title=null;

    private function makeTitleNew(): void
    {
        $this->title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_NEW_TITLE"),"fa fa-plus-square");

        $backButton=new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
        $backButton->setActionURL(KRoute::makeURL(RoutesItems::$KSHOW_USERS));
        $this->title->addKTitleButton($backButton);

        $insertButton=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_SAVE"),KTitleButton::$TYPE_SUCCESS,"fa fa-check");
        $insertButton->setSubmitForm("form_edit_user");
        $this->title->addKTitleButton($insertButton);
    }

    private function makeTitleEdit(): void
    {
        $this->title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_TITLE "),"fa fa-users");
        $kurl=new KURL();
        $kurl->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        $kurl->removeArg(self::$GET_NEW);
        $kurl->addOrReplace(Kapp_Groups::$ID,$this->user->getId());
        $this->title->setKurl($kurl);

        $backButton=new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
        $backButton->setActionURL(KRoute::makeURL(RoutesItems::$KSHOW_USERS));
        $this->title->addKTitleButton($backButton);

        $updateButton=new KTitleButton(LanguageManager::_("KAPP_USER_LABEL_BUTTON"),KTitleButton::$TYPE_SUCCESS,"fa fa-check");
        $updateButton->setSubmitForm("form_edit_user");
        $this->title->addKTitleButton($updateButton);

        $deleteButton=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_DELETE"),KTitleButton::$TYPE_DANGER,"fa fa-trash");
        $deleteButton->setClickAction("addJSActionButton(".$this->user->getId().",'".self::$ACTION_DELETE."')");
        $this->title->addKTitleButton($deleteButton);

        if(ParamManager::getInstance()->impersonate)
        {
            $impersonateButton=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_IMPERSONATE"),KTitleButton::$TYPE_WARNING,"fa-solid fa-arrows-turn-to-dots");
            $impersonateButton->setClickAction("addJSActionButton(".$this->user->getId().",'".self::$ACTION_IMPERSONATE."')");
            $this->title->addKTitleButton($impersonateButton);    
        }
    }

    private function initUser(): bool
    {
        $this->user=new Kapp_Users();

        $this->new=false;
        KInput::checkInputGet(self::$GET_NEW,KInput::$VARIABLE_BOOL,$this->new);
        if($this->new)
        {
            return true;
        }

        $user_id=0;
        if(!KInput::checkInput(KInput::$INPUT_GET,KObject::$ID,KInput::$VARIABLE_INT,$user_id))
        {
            $sql=Sql::getInstance();
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                            LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_7")." '".$user_id."' ".LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_8")."<br />".$this->user->getKerror()."<br /><br />".$sql->getQuery()."<br /><br />",
                            KAlertComponent::$TYPE_ERROR));
            return false;
        }
        else if(!$this->user->initById($user_id))
        {
            $sql=Sql::getInstance();
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                            LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_7")." '".$user_id."' ".LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_8")."<br />".$this->user->getKerror()."<br /><br />".$sql->getQuery()."<br /><br />",
                            KAlertComponent::$TYPE_ERROR));
            return false;
        }
        return true;
    }

    private function makeTitle(): void
    {
        if($this->new)
        {
            $this->makeTitleNew();
        }
        else
        {
            $this->makeTitleEdit();
        }
        $this->addString(self::addJSActionButton());
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->title);
        KApp::getInstance()->getLayout()->setTitle($this->title->getTitle());
    }

    public function execute(): bool
    {     

        if(!$this->initUser())
        {
            return true;
        }
              
        if(KFormGroupComponent::isAlreadyPost())
        {
            $CorrectInput=true;
            $password_1=null;
            $password_2=null;
            if(!KInput::checkInput(KInput::$INPUT_POST,FormEditKapp_UsersComponent::$MODIFY_PASS,KInput::$VARIABLE_STRING,$password_1)||!KInput::checkInput(KInput::$INPUT_POST,FormEditKapp_UsersComponent::$MODIFY_PASS_2,KInput::$VARIABLE_STRING,$password_2))
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_1"),KNotify::$TYPE_DANGER));
            }                
            
            if($this->user->getAllInputPost()|| !is_null($password_1)&&!is_null($password_2))
            {
                if(!is_string($this->user->getFirst_name())||preg_match('/[\'^£$%&*[()}\'\"\`\/\\\{\]@#~!:;.?><>,0123456789|=_+¬]/',$this->user->getFirst_name()))
                {
                    $this->addComponent(new KNotify(
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_2"),
                                    KNotify::$TYPE_DANGER
                    ));
                    $CorrectInput=false;
                }

                if(!is_string($this->user->getLast_name())||preg_match('/[\'^£$%&*[()}\'\"\`\/\\\{\]@#~!:;.?><>,0123456789|=_+¬]/',$this->user->getLast_name()))
                {
                    $this->addComponent(new KNotify(
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_3"),
                                    KNotify::$TYPE_DANGER
                    ));
                    $CorrectInput=false;
                }

                if(!filter_var($this->user->getEmail(),FILTER_VALIDATE_EMAIL))
                {
                    $this->addComponent(new KNotify(
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_4"),
                                    KNotify::$TYPE_DANGER
                    ));
                    $CorrectInput=false;
                }
                else if($this->user->isThisEmailPresentInDbNotMine($this->user->getEmail()))
                {
                    $this->addComponent(new KNotify(
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_5"),
                                    KNotify::$TYPE_DANGER
                    ));
                    $CorrectInput=false;
                }


                if(($password_1!=""&&$password_2=="")||($password_1==""&&$password_2!=""))
                {
                    $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_3"),KNotify::$TYPE_DANGER));
                    $CorrectInput=false;
                }
                else if($password_1!=""&&$password_2!="")
                {
                    if($password_1!=$password_2)
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_3"),KNotify::$TYPE_DANGER));
                        $CorrectInput=false;
                    }
                    else if(strlen($password_1)<8)
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_7"),KNotify::$TYPE_DANGER));
                        $CorrectInput=false;
                    }
                    else if(!stringContainsLetter($password_1))
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_4"),KNotify::$TYPE_DANGER));
                        $CorrectInput=false;
                    }
                    else if(!stringContainsDigit($password_1))
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_5"),KNotify::$TYPE_DANGER));
                        $CorrectInput=false;
                    }
                    else if(!stringContainsSpecial($password_1))
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_6"),KNotify::$TYPE_DANGER));
                        $CorrectInput=false;
                    }
                    else
                    {
                        $this->user->setPasswordHash($password_1);
                        //$this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_NO_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE"),KNotify::$TYPE_SUCCESS));
                    }
                }
                
                if($CorrectInput)
                {
                    Sql::getInstance()->beginTransaction();
                    if($this->new)
                    {
                        if($this->user->insert())
                        {
                            $this->updateGroupes($this->user);//,Modifications::CREATE_USER);
                            $this->new=false;
                        }
                        else
                        {
                            $this->addComponent(new KNotify(
                                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_MSG"),
                                            KNotify::$TYPE_DANGER
                            ));
                            Sql::getInstance()->rollbackTransaction();
                        }
                    }
                    else
                    {
                        if($this->user->updateInBd())
                        {
                            $this->updateGroupes($this->user);//,Modifications::UPDATE_USER);
                        }
                        else
                        {
                            $this->addComponent(new KNotify(
                                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_MSG"),
                                            KNotify::$TYPE_DANGER
                            ));
                            Sql::getInstance()->rollbackTransaction();
                        }
                    }
                }
            }
            else if(!$this->new)
            {
                $this->updateGroupes($this->user);//,Modifications::UPDATE_USER);
            }
        }

        $this->makeTitle();
        $form=new FormEditKapp_UsersComponent(KRoute::makeURL(RoutesItems::$KEDIT_USERS,[KObject::$ID=>$this->user->getId(),self::$GET_NEW=>$this->new]));
        $form->initForm($this->user,true);
        $this->addComponent($form);

        return true;
    }

    private function updateGroupes(Kapp_Users $user) : void
    {
        $groupes=null;
        KInput::checkInputPost("groupes",KInput::$VARIABLE_INT,$groupes);
        
        if(isInteger($groupes)&&!Klink_Kapp_Users_Groups::updateSelfKlinksInDbFromFK(Kapp_Users::class,$user->getId(),Kapp_Groups::class,[$groupes]))
        {
            $this->addComponent(new KNotify(
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_MSG"),
                            KNotify::$TYPE_DANGER
            ));
            Sql::getInstance()->rollbackTransaction();
        }
        else
        {
//            $modif=new Modifications();
//            if(!$modif->modifUser($this->user->getId(),$type_modif,$this->user->getEmail()))
//            {
//                $this->addComponent(new KAlertComponent("Erreur","Erreur update Modifications :<br />".$modif->getKerror(),KAlertComponent::$TYPE_ERROR));
//            }
            $this->addComponent(new KNotify(
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_OK_TITLE"),
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_OK_MSG"),
                            KNotify::$TYPE_SUCCESS
            ));
            Sql::getInstance()->commitTransaction();
        }
    }

    public static function addJSActionButton() : string
    {
        $script='
<script>            
function addJSActionButton(id_user, action)
{
    let message = document.createElement("div");
    if(action == "'.self::$ACTION_DELETE.'"){
        message.innerHTML = "'.LanguageManager::_("KAPP_USER_MODIFICATIONS_MSSG_DELETE_1").'"+id_user+". '.LanguageManager::_("KAPP_USER_MODIFICATIONS_MSSG_DELETE_2").'";
    }
    else if(action == "'.self::$ACTION_RESET_PWD.'"){
        message.innerHTML = "'.LanguageManager::_("KAPP_USER_MODIFICATIONS_MSSG_RESEND_PASS_1").'"+id_user+". '.LanguageManager::_("KAPP_USER_MODIFICATIONS_MSSG_RESEND_PASS_2").'";
    }
    else if(action == "'.self::$ACTION_IMPERSONATE.'"){
        message.innerHTML = "'.LanguageManager::_("KAPP_USER_IMPERSONATE_MSSG").'";
    }
    
    swal(
    {
        content: message,
        buttons: ["'.LanguageManager::_("KAPP_USER_MODIFICATIONS_CANCEL").'", "'.LanguageManager::_("KAPP_USER_MODIFICATIONS_CONTINUE").'"],
    }).then((result) =>
    {
        if (result) 
        {
            $.ajax({
                url: "'.KRoute::makeActionURL(RoutesItems::$KMANAGE_USERS).'",
                type: "POST",
                data: {
                    id_user:id_user,
                    action:action
                },
                success: function(data)
                {
                    if(action == "'.self::$ACTION_DELETE.'"){
                        window.location.href = "'.KRoute::makeURLNoAmp(RoutesItems::$KSHOW_USERS,[self::$ACTION_DELETE=>1]).'";
                    }
                    else if(action == "'.self::$ACTION_RESET_PWD.'"){
                        window.location.href = "'.KRoute::makeURLNoAmp(RoutesItems::$KSHOW_USERS,[self::$ACTION_RESET_PWD=>1]).'";
                    }
                    else if(action == "'.self::$ACTION_IMPERSONATE.'"){
                        window.location.href = "'.KRoute::makeURLNoAmp(RoutesItems::$HOME).'";
                        //console.log(data);    
                    }                    
                },    
                error: function(data)
                {
                    if(action == "'.self::$ACTION_DELETE.'"){
                        window.location.href = "'.KRoute::makeURLNoAmp(RoutesItems::$KSHOW_USERS,[self::$ACTION_DELETE=>2]).'";
                    }
                    else if(action == "'.self::$ACTION_RESET_PWD.'"){
                        window.location.href = "'.KRoute::makeURLNoAmp(RoutesItems::$KSHOW_USERS,[self::$ACTION_RESET_PWD=>2]).'";
                    }
                    else if(action == "'.self::$ACTION_IMPERSONATE.'"){
                        window.location.href = "'.KRoute::makeURLNoAmp(RoutesItems::$KSHOW_USERS,[self::$ACTION_IMPERSONATE=>2]).'";
                    }                    
                }
            });
        }
        else 
        {

        }
    });
}   
</script>
';
        return $script;
    }

}