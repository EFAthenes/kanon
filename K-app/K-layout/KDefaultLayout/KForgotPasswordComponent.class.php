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
class KForgotPasswordComponent extends KComponent
{
    public static string $INPUT_NEW_PASSWORD_1="password_1";
    public static string $INPUT_NEW_PASSWORD_2="password_2";
    public static string $GET_TOKEN="ktoken";
    public static string $CHECK_FORM="check";
    
    public static string $INPUT_FORGOT_PASSWORD="forgot_password";
    function __construct(string $email)
    {        
        parent::__construct();
        $this->setName("KForgotPasswordComponent");
        $this->setId();   
        
        $password_1="";
        $password_2="";
        
        KInput::checkInput(KInput::$INPUT_POST,self::$INPUT_NEW_PASSWORD_1,KInput::$VARIABLE_STRING,$password_1);
        KInput::checkInput(KInput::$INPUT_POST,self::$INPUT_NEW_PASSWORD_2,KInput::$VARIABLE_STRING,$password_2);
       
        
        $url=new KURL();
        $url->addOrReplace(self::$CHECK_FORM,1);
        $html='
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>'.ParamManager::getInstance()->site_title.'</h1>
    </div>
    <div class="login-box">
        <form class="login-form" action="'.$url->printURL().'" method="post" id="forgot_password_form" autocomplete="off" >
            <h3 class="tip-login-head"><i class="fa-solid fa-lock"></i>'.LanguageManager::_("CONNEXION_FORGOT_PAGE_TITLE_2").'</h3>                
            <div class="tip-login-box">
                Le mot de passe doit contenir au moins 8 caractères dont une majuscule, un nombre et une diacritique.
            </div>
            <input type="text" name="email" value="'.FormComponent::inputString($email).'" style="display: none" />
                
            <div class="form-group">
                <label class="control-label">'.LanguageManager::_("CONNEXION_INPUT_PASSWORD_1").'</label>
                <input autocomplete="off" class="form-control" type="password" placeholder="'.LanguageManager::_("CONNEXION_INPUT_PASSWORD_1_LABEL").'" name="'.self::$INPUT_NEW_PASSWORD_1.'" autofocus value="'.FormComponent::inputString($password_1).'">
            </div>
            <div class="form-group">
                <label class="control-label">'.LanguageManager::_("CONNEXION_INPUT_PASSWORD_2").'</label>
                <input autocomplete="off" class="form-control" type="password" name="'.self::$INPUT_NEW_PASSWORD_2.'" placeholder="'.LanguageManager::_("CONNEXION_INPUT_PASSWORD_2_LABEL").'" value="'.FormComponent::inputString($password_2).'">
            </div>
            <button class="btn btn-primary" type="submit" form="forgot_password_form" ><i class="fa fa-check-square-o" ></i> '.LanguageManager::_("CONNEXION_FORGOT_PAGE_BUTTON").'</button>
        </form>
    </div>
</section>  
';
        $this->addHTML($html);
    }
}
