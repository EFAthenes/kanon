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
class KConnectionController extends KController
{
    public function execute(): bool
    {
        //$this->addComponent(new SessionComponent());
        $forgot_password="";
        $modifications_password="";
        if(KInput::checkInput(KInput::$INPUT_GET,KForgotPasswordController::$GET_INPUT_MODIFICATION_PASSWORD,KInput::$VARIABLE_STRING,$modifications_password))
        {
            $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_OK_TITLE"),
                    LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_OK_TXT"),
                    KNotify::$TYPE_SUCCESS)); 
        }
        else if(KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_FORGOT_PASSWORD,KInput::$VARIABLE_STRING,$forgot_password))
        {
            if(filter_var($forgot_password,FILTER_VALIDATE_EMAIL))
            {
                // valid address                
                $db=new DbList(Kapp_Users::class);
                $results=$db->getByArray([new QueryField(Kapp_Users::$EMAIL,$forgot_password,QueryField::$EQUAL)]);
                
                $send_email=false;
                
                /* @var $user Kapp_Users */
                foreach($results as $user)
                {                   
                    $token = new Kapp_Ktokens();
                    $token->setLabel(KRandom::makeRandomString(100));
                    $token->setFk_id_kapp_users($user->getId());
                    
                    $date = new DateTime(date("Y-m-d H:i:s"));
                    $date->add(new DateInterval('P1D'));
                    $token->setDate_max($date->format('Y-m-d H:i:s'));
                    
                    $token->insert();
                    
                    $url=ParamManager::getInstance()->site_root.KRoute::makeURL(RoutesItems::$FORGOT_PASSWORD,[KForgotPasswordComponent::$GET_TOKEN=>$token->getLabel()]);
                    
                    $titre_email=LanguageManager::_("CONNEXION_RESEND_PASSWORD_TITLE")." ".ParamManager::getInstance()->site_title;
                    //$message=LanguageManager::_("CONNEXION_RESEND_PASSWORD_MESSAGE").$url.LanguageManager::_("EMAIL_KAPP_FOOTER");
                    //$message=LanguageManager::_("CONNEXION_RESEND_PASSWORD_MESSAGE").$url.LanguageManager::_("EMAIL_KAPP_FOOTER");
                    $footer=(new KAppTemplateForPHPFileComponent("/view/mail/MailFooter.php",["app_name"=>ParamManager::getInstance()->app_name]))->draw();
                    
                    $message=(new KAppTemplateForPHPFileComponent("/view/mail/MailResetPassword.php",
                            ["url"=>$url, 
                             "footer"=>$footer])
                            )->draw();
                    
                    $mail = new KMail();
                    $send_email=$mail->sendNormalEmail($forgot_password,$titre_email,$message);                     
                    break;
                }
                     
                // Whatever the user exists or not we say it's OK
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_RESEND_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_RESEND_TXT"),
                        KNotify::$TYPE_SUCCESS));

            }
            else
            {
                // invalid address
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_RESEND_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_FORGOT_PASSWORD_RESEND_TXT_ERROR"),
                        KNotify::$TYPE_DANGER));
            }
        }
        else
        {
            $username="";
            $password="";
            if(KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_USERNAME,KInput::$VARIABLE_STRING,$username)
                && KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_PASSWORD,KInput::$VARIABLE_STRING,$password)
                    && $username!="" && $password!="" )
            {
                // On a User et password il faut tester la connexion
                
                $user= new Kapp_Users();
                if($user->connectUser($username,$password))
                {
                    SessionMemory::getInstance()->putUser($user);
                    LanguageManager::getInstance()->setLanguage($user->getLanguage());
                    //$this->addComponent(new KNotify("Connexion test","Redirection!",KNotify::$TYPE_SUCCESS));     
                    //
                    // REDIRECTION
                    KRoute::redirectRoute(KRoutesItems::$HOME);
                }
                else
                {
                    if($user->tooManyAttempts())
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_CONNECT_ERROR_TITLE"),
                            LanguageManager::_("CONNEXION_MSG_CONNECT_ERROR_MSG_3"),
                            KNotify::$TYPE_DANGER));                          
                    }
                    else
                    {
                        $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_CONNECT_ERROR_TITLE"),
                            LanguageManager::_("CONNEXION_MSG_CONNECT_ERROR_MSG_1"),
                            KNotify::$TYPE_DANGER));                
                    }
                }
                

            }
            else if ( KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_USERNAME,KInput::$VARIABLE_STRING,$username)
                    &&KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_PASSWORD,KInput::$VARIABLE_STRING,$password))
            {
                $this->addComponent(new KNotify(LanguageManager::_("CONNEXION_MSG_CONNECT_ERROR_TITLE"),
                        LanguageManager::_("CONNEXION_MSG_CONNECT_ERROR_MSG_2"),
                        KNotify::$TYPE_DANGER));
            }
        }
        return true;
    }
}

