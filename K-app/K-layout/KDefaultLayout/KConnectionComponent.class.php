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
class KConnectionComponent extends KComponent
{
    public static string $INPUT_USERNAME_FORGOT="username_forgot";
    public static string $INPUT_USERNAME="username";
    public static string $INPUT_PASSWORD="password";
    
    public static string $INPUT_FORGOT_PASSWORD="forgot_password";
    public static string $INPUT_PANEL_FORGOT_PASSWORD="forgot_password_panel";
    function __construct()
    {        
        parent::__construct();
        $this->setName("KConnectionComponent");
        $this->setId();   
        
        $username="";
        $password="";
        $forgot_password="";
        if(!KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_USERNAME,KInput::$VARIABLE_STRING,$username))
        {
            KInput::checkInput(KInput::$INPUT_GET,KConnectionComponent::$INPUT_USERNAME_FORGOT,KInput::$VARIABLE_STRING,$username);
        }
        KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_PASSWORD,KInput::$VARIABLE_STRING,$password);
        KInput::checkInput(KInput::$INPUT_POST,KConnectionComponent::$INPUT_FORGOT_PASSWORD,KInput::$VARIABLE_STRING,$forgot_password);
       
        
        $url=new KURL();
        $url->removeArg(self::$INPUT_PANEL_FORGOT_PASSWORD);
        $url->removeArg(KForgotPasswordController::$GET_INPUT_MODIFICATION_PASSWORD);
        $html='
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>'.ParamManager::getInstance()->site_title.'</h1>
    </div>
    <div class="login-box">
        <form class="login-form" action="'.$url->printURL().'" method="post">
            <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>'.LanguageManager::_("CONNEXION_SIGN_IN_BOX_TITLE ").'</h3>
            <div class="form-group">
                <label class="control-label">'.LanguageManager::_("CONNEXION_INPUT_USERNAME").'</label>
                <input class="form-control" type="text" placeholder="Email" name="'.self::$INPUT_USERNAME.'" autofocus value="'.FormComponent::inputString($username).'">
            </div>
            <div class="form-group">
                <label class="control-label">'.LanguageManager::_("CONNEXION_INPUT_PASSWORD").'</label>
                <input class="form-control" type="password" name="'.self::$INPUT_PASSWORD.'" placeholder="Password" value="'.FormComponent::inputString($password).'">
            </div>
            <div class="form-group">
                <div class="utility">
                    <p class="semibold-text mb-2"><a href="#" data-toggle="flip">'.LanguageManager::_("CONNEXION_FORGOT_PASSWORD").'</a></p>
                </div>
            </div>
            <div class="form-group btn-container">
                <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-sign-in fa-lg fa-fw"></i>'.LanguageManager::_("CONNEXION_SIGN_IN").'</button>
            </div>
            <div class="form-group">
                <div class="utility">
                    <p class="mt-2 mb-2">
                    <a class="" href="'.ParamManager::getInstance()->server_url.'">'.LanguageManager::_("CONNEXION_BACK_TO_SITE").'</a>
                    </p>
                </div>
            </div>            
        </form>
';
        $url->add(self::$INPUT_PANEL_FORGOT_PASSWORD,"1");
        $html.='
        <form class="forget-form" action="'.$url->printURL().'" method="post">
            <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>'.LanguageManager::_("CONNEXION_FORGOT_PASSWORD_LABEL").'</h3>
            <div class="form-group">
                <label class="control-label">'.LanguageManager::_("CONNEXION_INPUT_EMAIL").'</label>
                <input class="form-control" type="text" placeholder="Email" name="'.self::$INPUT_FORGOT_PASSWORD.'" value="'.FormComponent::inputString($forgot_password).'">
            </div>
            <div class="form-group btn-container">
                <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i>'.LanguageManager::_("CONNEXION_RESET").'</button>
            </div>
            <div class="form-group mt-3">
                <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i>'.LanguageManager::_("CONNEXION_BACK_TO_LOGIN").'</a></p>
            </div>
        </form>
    </div>
</section>  
<script>
';
        $forgot_password_panel=0;
        if(KInput::checkInput(KInput::$INPUT_GET,KConnectionComponent::$INPUT_PANEL_FORGOT_PASSWORD,KInput::$VARIABLE_INT,$forgot_password_panel)
                &&$forgot_password_panel==1 )
        {
            $html.='
    $(".login-box").toggleClass("flipped");            
';
        }
        
        $html.='

$(".login-content [data-toggle=\"flip\"]").click(function() 
{
    $(".login-box").toggleClass("flipped");
    return false;
});
</script>
';
        $this->addHTML($html);
        
        if(StyleManager::getInstance()->isActivated())
        {       
            $css=" 
.material-half-bg .cover
{ 
    background-color: ".StyleManager::getInstance()->main_colour.";
}

.btn-primary 
{ 
    background-color: ".StyleManager::getInstance()->main_colour."; border-color: ".StyleManager::getInstance()->colour_2." 
}

.btn-primary:hover 
{ 
    color: ".StyleManager::getInstance()->font_colour_1.";background-color: ".StyleManager::getInstance()->main_colour.";
}

.btn-primary::selection 
{ 
    color: ".StyleManager::getInstance()->font_colour_1.";background-color: ".StyleManager::getInstance()->colour_2.";
}

a 
{
    color: ".StyleManager::getInstance()->link_colour_1.";
    text-decoration: none;
    background-color: transparent;
    -webkit-text-decoration-skip: objects;
}

a:hover 
{
    color: ".StyleManager::getInstance()->link_colour_1_hover.";
    text-decoration: none;
    background-color: transparent;
    -webkit-text-decoration-skip: objects;
}

";
            $this->addCssText($css);
            
        }
    }
}
