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
class FooterComponent extends KComponent
{
    function __construct()
    {        
        parent::__construct();
        $this->setName("footer");
        $this->setNone();   
        
        KTimer::getInstance()->stop();
        $this->addHTML('          
<div class="efa-footer-bar">
  <div class="footer content clearfix">
    <ul id="footer-list">   
    <li>
        <a href="http://www.efa.gr/" target="_blank">
            <img src="images/LOGOefa.jpg" alt="" />
        </a>
    </li>
    <li>
        <a href="http://www.enseignementsup-recherche.gouv.fr/pid20001/accueil.html" target="_blank">
            <img src="images/logoMinister.gif" alt=""/>
        </a>
    </li>
    <li>
        <a href="http://www.carnets.efa.gr" target="_blank">
            Les Carnets numériques
        </a>
    </li>
    <!--
    <li>
        <a href="http://www.catalogue.efa.gr/" target="_blank">
            <img src="images/bibliotheque.png" alt="" />
        </a>
    </li>     
    <li>
        <a href="http://www.cefael.efa.gr/" target="_blank">
            <img src="images/cefael.png" alt="" />
        </a>
    </li>     
    <li>
        <a href="http://www.archimage.efa.gr/" target="_blank">
            <img src="images/archimage.png" alt="" />
        </a>
    </li> 
    <li>
        <a href="http://www.carnets.efa.gr/" target="_blank">
            <img src="images/carnets.png" alt="" />
        </a>
    </li> 
    -->
    <li>
        <a href="http://www.partages.efa.gr/" target="_blank">
            Les Partages
        </a>
    </li>     
    <li>
        <a href="http://www.telechargement.efa.gr/" target="_blank">
            Les Téléchargements
        </a>
    </li>     
    <li>
        <a href="http://www.email.efa.gr/" target="_blank">
            Le Webmail EFA
        </a>
    </li>      
    <li><span dir="ltr">&copy; '.date("Y").' EfA</span></li>
    <li>  </li>
    <li> affiché en '.KTimer::getInstance()->totalTimeToString().' </li>    
    </ul>
  </div>
</div>
<div id="refresh">
</div>
');
        
//    <li><a href="">Conditions d\'utilisation</a></li>
//    <li><a href="">Règles de confidentialité</a></li>        
        $this->makeOwnDiv(true);
        
        $user=SessionMemory::getInstance()->getUser();
        if($user!=null)
        {
            $js='
var auto_refresh = setInterval(
function()
{
    $("#refresh").load("./action/stay_connected/updateRefresh.php");
}, 30000);
';
            $this->addJSText($js);
        }
        else
        {
            $js="
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25855504-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();	               
";
             $this->addJSText($js);
        }
        
    }
    private function addDebugOptions() : void
    {
        $comp1= new SessionComponent("session_visu");
        $this->addComponent($comp1);
        $comp2= new PostGetComponent("post_visu");
        $this->addComponent($comp2);
    }  
//    private function addTranslateComponent()
//    {
//        $component= new ArchimageTranslate();
//        $this->addComponent($component);
//    }
    function draw() : string
    {
        $user=SessionMemory::getInstance()->getUser();
        if($user!=NULL && $user->isUserAdmin() && $user->getEmail()==ParamManager::getInstance()->admin_email)
        {
            $this->addDebugOptions();
        }
        return parent::draw();
    }
}