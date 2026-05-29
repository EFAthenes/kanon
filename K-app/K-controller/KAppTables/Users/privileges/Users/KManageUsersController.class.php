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
 * Description of KManageUsersController.class
 *
 * @author louis.mulot
 */
class KManageUsersController extends KController
{
    public static string $ID_USER="id_user";

    public function execute(): bool
    {
        $user=new Kapp_Users();        
        $id=0;
        $action="";
        if(KInput::checkInputPost(self::$ID_USER,KInput::$VARIABLE_INT,$id)&&$user->initById($id)
                &&KInput::checkInputPost(KEditTableUsers::$ACTION,KInput::$VARIABLE_STRING,$action))
        {
            if($action===KEditTableUsers::$ACTION_DELETE)
            {
                if($user->deleteAll())
                {
//                    $modif=new Modifications();
//                    if(!$modif->modifUser($idUser,Modifications::DELETE_USER,$remarque))
//                    {
//                        $this->addComponent(new KAlertComponent("Erreur","Erreur update Modifications :<br />".$modif->getKerror(),KAlertComponent::$TYPE_ERROR));
//                    }
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else if($action===KEditTableUsers::$ACTION_RESET_PWD)
            {
                //$user->makePassword();
                $password=$user->createPassword();
                $user->setPasswordHash($password);
                $user->makeDateModification();
                if($user->updateInBd())
                {
//                    $modif=new Modifications();
//                    if(!$modif->modifUser($user->getId(),Modifications::RESET_PWD_USER,$user->getEmail()))
//                    {
//                        $this->addComponent(new KAlertComponent("Erreur","Erreur update Modifications :<br />".$modif->getKerror(),KAlertComponent::$TYPE_ERROR));
//                    }
                    //$this->addString($user->toString());
                    //$this->addString("Password ==>".$password);
                    $mail=new KMail();
                    
                    $mail->setExternalScript(false);
                    
                    $mail->sendEmailNotification("Renvoi de mot de passe",$user->getEmail());

                    $footer=(new KTemplateForPHPFileComponent("/view/mail/MailFooter.php",
                            ["server_name" => ParamManager::getInstance()->server_name,]
                            ))->draw();
                    $content=(new KTemplateForPHPFileComponent("/view/mail/MailResendPassword.php",
                            ["email"=>$user->getEmail(),
                             "password"=>$password,
                             "url"=>KRoute::makeFullURLNoAmp(RoutesItems::$CONNECTION,[KConnectionComponent::$INPUT_USERNAME_FORGOT=>$user->getEmail()]), 
                             "footer"=>$footer, 
                             "server_name" => ParamManager::getInstance()->server_name,
                                ])
                            )->draw();

                    $mail->sendNormalEmail($user->getEmail(),"Renvoi de mot de passe",$content);

                    return true;
                }
                else
                {
                    return false;
                }
            }
            else if($action===KEditTableUsers::$ACTION_IMPERSONATE)
            {
                if(ParamManager::getInstance()->impersonate&&$user->initConnection())
                {                  
                    SessionMemory::getInstance()->removeUser();                       
                    SessionMemory::getInstance()->putUser($user);
                    return true;
                } 
                else
                {
                    return false;
                }
            }            
        }
        return true;
    }

}