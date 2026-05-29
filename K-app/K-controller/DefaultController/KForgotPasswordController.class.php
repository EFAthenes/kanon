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
class KForgotPasswordController extends KController
{
    public static string $GET_INPUT_MODIFICATION_PASSWORD="modification_password";
    public function execute(): bool
    {
        $ktoken="";
        if(!KInput::checkInput(KInput::$INPUT_GET,KForgotPasswordComponent::$GET_TOKEN,KInput::$VARIABLE_STRING,$ktoken))
        {
           KRoute::redirectRoute(KRoutesItems::$HOME);
           return false;
        }
        
        $db=new DbList(Kapp_Ktokens::class);
        $results=$db->getByArray([new QueryField(Kapp_Ktokens::$LABEL,$ktoken)]);
        
        /* @var $token Kapp_Ktokens */
        /* @var $tokenFound Kapp_Ktokens */
        $tokenFound=null;
        foreach($results as $token)
        {
            $tokenFound=$token;
            break;
        }
        
        if(is_null($tokenFound))
        {
            KRoute::redirectRoute(KRoutesItems::$HOME);
            return false;
        }
        
        $user= new Kapp_Users();
        if(!$user->initById($tokenFound->getFk_id_kapp_users()))
        {
            KRoute::redirectRoute(KRoutesItems::$HOME);
            return false;            
        }
        
        $now=date("Y-m-d H:i:s");
        
        if($now > $tokenFound->getDate_max())
        {
            KRoute::redirectRoute(KRoutesItems::$HOME);
            return false;   
        }
        
        $password_1="";
        $password_2="";
        $check=0;
        if(KInput::checkInput(KInput::$INPUT_GET,KForgotPasswordComponent::$CHECK_FORM,KInput::$VARIABLE_INT,$check) && $check==1)
        {
            if(     !KInput::checkInput(KInput::$INPUT_POST,KForgotPasswordComponent::$INPUT_NEW_PASSWORD_1,KInput::$VARIABLE_STRING,$password_1)
                ||  !KInput::checkInput(KInput::$INPUT_POST,KForgotPasswordComponent::$INPUT_NEW_PASSWORD_2,KInput::$VARIABLE_STRING,$password_2))      
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_1"),
                        KNotify::$TYPE_DANGER));
            }
            else if($password_1==""||$password_2=="")
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_2"),
                        KNotify::$TYPE_DANGER));
            }
            else if($password_1!=$password_2)
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_3"),
                        KNotify::$TYPE_DANGER));
            }
            else if(strlen($password_1)<8)
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_7"),
                        KNotify::$TYPE_DANGER));
            }             
            else if(!stringContainsLetter($password_1))
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_4"),
                        KNotify::$TYPE_DANGER));
            } 
            else if(!stringContainsDigit($password_1))
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_5"),
                        KNotify::$TYPE_DANGER));
            } 
            else if(!stringContainsSpecial($password_1))
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_CHANGE_ERROR_MSG_6"),
                        KNotify::$TYPE_DANGER));
            }             
            else
            {
                $user->setPasswordHash($password_1);
                $user->updateInBd();
                $tokenFound->delete();
                KRoute::redirectRoute(KRoutesItems::$CONNECTION,[self::$GET_INPUT_MODIFICATION_PASSWORD => "1"]);
                //$this->addComponent(new KNotify("OK","OK"));
                return true;
            }
        }
             
        $this->addComponent(new KForgotPasswordComponent($user->getEmail()));        
        return true;
    }
}
