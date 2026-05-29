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
class PublicLayout extends KAdminLayout
{
    public static string $HEADER="header";
    public static string $LEFT="left";
    public static string $RIGHT="right";
    public static string $FOOTER="footer";

    public function __construct()
    {
        parent::__construct();
    }

    public function initialize(): void
    {
        $this->setTitle(ParamManager::getInstance()->site_title);
        $this->addMetaProperty("og:site_name",ParamManager::getInstance()->site_title);
        
        $this->setFavIco("img/Kanov_logo2.png");
        
        $this->setHtmlTag(' data-bs-theme="dark" ');

        //Check LAng
        $action=new KChangeLanguageAction();
        $action->execute();

        $this->includeCssFiles();
        $this->includeJsFiles();


        // DEFAULT
        $this->addCSSFileToBuffer(__DIR__."/css/public.css");

        $this->addLayoutItem(self::$HEADER,new PublicNavBar());
        
        $center= new DivIdComponent("container-xxl bd-gutter mt-3 my-md-4 bd-layout");
        $this->addLayoutItem(self::$CENTER,$center);
        $this->setComponentAsMain(self::$CENTER);
        
        $version = ParamManager::getInstance()->get("APP_VERSION");
        $footer= new DivIdComponent("footer");
        $footer->addComponent(new KTemplateForTwigComponent("/view/public/footer/footer.php",["version"=>$version]));
        $this->setFooterComponent($footer);        
    }

    public function terminate(): void
    {

    }
//    
//    public function includeCssFiles(): void
//    {
//        $dir=$this->getKAdminLayoutDir();
//        $this->addCSSFileToBuffer($dir."/bootstrap/bootstrap5/css/bootstrap.min.css");
//        $this->addCSSFileToBuffer(__DIR__."/css/bootstrapKapp.min.css");  
//        $this->addCSSFileToBuffer($dir."/fontawesome7/css/all.css");
//        $this->addCSSFileToBuffer(__DIR__."/css/jquery-ui.min.css");
//        $this->addCSSFileToBuffer(__DIR__."/css/adminlayout.css");
//        $this->addCSSFileToBuffer($dir."/css/sweetalert-bootstrap4.css");
//    }
}