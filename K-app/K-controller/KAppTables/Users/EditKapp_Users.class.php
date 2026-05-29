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
class EditKapp_Users extends KController
{
    public function execute(): bool
    {
        $user=SessionMemory::getInstance()->getUser();
        $form = new FormEditKapp_UsersComponent(KRoute::makeURL(RoutesItems::$EDIT_KAPP_USER));
        if($form->isAlreadyPost())
        {        

            $password_1=null;
            $password_2=null;
            KInput::checkInput(KInput::$INPUT_POST,FormEditKapp_UsersComponent::$MODIFY_PASS,KInput::$VARIABLE_STRING,$password_1);
            KInput::checkInput(KInput::$INPUT_POST,FormEditKapp_UsersComponent::$MODIFY_PASS_2,KInput::$VARIABLE_STRING,$password_2);
            
            if($user->getAllInputPost()||(!is_null($password_2)||!is_null($password_1)))
            {
                $CorrectInput=true;

                if(!is_string($user->getFirst_name())||preg_match('/[\'^£$%&*[()}\'\"\`\/\\\{\]@#~!:;.?><>,0123456789|=_+¬]/',$user->getFirst_name()))
                {
                    $form->addComponent(new KNotify(
                                LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_2"),
                                KNotify::$TYPE_DANGER
                                ));                                
                    $CorrectInput=false;
                }

                if(!is_string($user->getLast_name())||preg_match('/[\'^£$%&*[()}\'\"\`\/\\\{\]@#~!:;.?><>,0123456789|=_+¬]/',$user->getLast_name()))
                {
                    $form->addComponent(new KNotify(
                                LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                                LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_3"),
                                KNotify::$TYPE_DANGER
                                ));
                    $CorrectInput=false;
                }

                if(!filter_var($user->getEmail(),FILTER_VALIDATE_EMAIL))
                {
                    $form->addComponent(new KNotify(
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_4"),
                            KNotify::$TYPE_DANGER
                            ));
                    $CorrectInput=false;
                }
                else if($user->isThisEmailPresentInDbNotMine($user->getEmail()))
                {
                    $form->addComponent(new KNotify(
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_TITLE"),
                            LanguageManager::_("KAPP_USER_MODIFICATIONS_ERROR_5"),
                            KNotify::$TYPE_DANGER
                            ));
                    $CorrectInput=false;
                }

                if($CorrectInput)
                {
                    $comp=new EmptyComponent();
                    $CorrectInput= self::checkPasswordModify($password_1, $password_2, $comp, $user);
                    $this->addComponent($comp);
                }

                if($CorrectInput)
                {
                    if($user->updateInBd())
                    {
                        $form->addComponent(new KNotify(
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_OK_TITLE"),
                                    LanguageManager::_("KAPP_USER_MODIFICATIONS_OK_MSG"),
                                    KNotify::$TYPE_SUCCESS
                                    ));    
                    }
                }
            }
        }

        $title=new KTitleLayoutAdmin(LanguageManager::_("KAPP_USER_LABEL_TITLE"),"fa fa-pencil-square-o");
        $title->removeFormGetArg();
        $sauvButton=new KTitleButton(LanguageManager::_("KAPP_USER_LABEL_BUTTON"),KTitleButton::$TYPE_SUCCESS,"fa fa-check");
        $sauvButton->setSubmitForm('form_edit_user');
        $title->addKTitleButton($sauvButton);
        $form->initForm($user, false);       
        
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        $this->addComponent($form);


        return true;

    }
    
    public static function checkPasswordModify(string $password_1,string $password_2, KComponent $comp, Kapp_Users $user) : bool
    {
        $CorrectInput=true;
        if(($password_1!="" && $password_2=="") || ($password_1=="" && $password_2!=""))
        {
            $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_3"),KNotify::$TYPE_DANGER));
            $CorrectInput=false;
        }
        else if($password_1!=""&&$password_2!="")
        {
            if($password_1!=$password_2)
            {
                $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_3"),KNotify::$TYPE_DANGER));
                $CorrectInput=false;
            }
            else if(strlen($password_1)<8)
            {
                $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_7"),KNotify::$TYPE_DANGER));
                $CorrectInput=false;
            }
            else if(!stringContainsLetter($password_1))
            {
                $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_4"),KNotify::$TYPE_DANGER));
                $CorrectInput=false;
            }
            else if(!stringContainsDigit($password_1))
            {
                $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_5"),KNotify::$TYPE_DANGER));
                $CorrectInput=false;
            }
            else if(!stringContainsSpecial($password_1))
            {
                $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_6"),KNotify::$TYPE_DANGER));
                $CorrectInput=false;
            }
            else
            {
                $user->setPasswordHash($password_1);
                $comp->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_NO_ERROR_TITLE"),LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE"),KNotify::$TYPE_SUCCESS));
            }
        }  
        return $CorrectInput;
    }
            
}
