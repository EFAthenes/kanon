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
class KAdminLayoutHeaderLanguageSwitcher extends KAdminLayoutHeaderMenuItem
{
    function __construct()
    {     
        parent::__construct("");
        $this->replaceLabelByComponent(new HTMLComponent('<div class="k_lang_selected k_lang_'.LanguageManager::getInstance()->getLanguage().'" >&nbsp;</div>'));
        
        
        $langs=LanguageManager::getInstance()->getArrayOfLanguages();
        foreach($langs as $lang)
        {
            $menuSubSubItem=new KAdminLayoutHeaderMenuItem($lang);
            $menuSubSubItem->replaceLabelByComponent(new HTMLComponent('<div onclick="KChangeLanguage(\''.$lang.'\')" class="dropdown-item"> <div class=" k_lang_proposed k_lang_'.$lang.'">  <div> '.LanguageManager::getInstance()->getLabelByLang($lang).' </div></div></div>'));
            $this->addMenuItem($menuSubSubItem);
        }
        
        $url=KRoute::makeActionKURL(RoutesItems::$KCHANGE_LANGUAGE,["lang"=>""])->printURLWithoutAmp();

        $js='
function KChangeLanguage(lang)
{
    let url=window.location.href;
    $.ajax({
        type: "GET",
        url: "'.$url.'"+lang,
        success: function(result) 
        {
           //console.log(result);
           if(result==1)
           {
                window.location.href = url;
           }
           else
           {
                alert("Language intitialization error =>"+result);
           }
        },
        error: function(result) {
            alert("error");
        }
    });
}
';
        $this->addJSText($js);  
    }
}