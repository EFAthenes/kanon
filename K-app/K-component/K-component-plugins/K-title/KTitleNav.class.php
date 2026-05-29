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
 * Description of KTitleNav
 *
 * @author Mulot Louis
 */
class KTitleNav extends KComponent
{
    public function __construct(string $title="",string $icon="")
    {        
        parent::__construct();
        $this->setName("title_header");
        $this->setClass();    
        $html="<div class=\"div_1\"><h1>";
        $kurl=new KURL();
        $kurl->removeArg("check");
        $html.="<a href=\"".$kurl->printURLWithoutAmp()."\" title=\"".LanguageManager::getInstance()->get("TITLE_BUTTON_LABEL")."\">";  
        if($icon!="")
        {
            $html.='<i class="'.$icon.' fa-6"></i> ';
        }  
        $html.=$title."</a></h1></div>";   
        $url=HistoryPage::getInstance()->printBack();
        if($url!="")
        {            
            $html.='<div class="div_2"><a href="'.$url.'" title="'.LanguageManager::getInstance()->get("BACK_BUTTON_LABEL").'"\"> <span class="back_button glyphicon glyphicon-share-alt" aria-hidden="true"></span></a></div>';    
        }
        
        $this->addHtml($html);
    }
}
