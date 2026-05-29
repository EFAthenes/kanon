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
 * Description of ArchimagePublicNavBar
 *
 * @author Mulot Louis
 */
class PublicNavBar extends Ki18Component
{
    function __construct()
    {
        parent::__construct();
        $language=[];
        $currentLang=LanguageManager::getInstance()->getLanguage();
        $langs=LanguageManager::getInstance()->getArrayOfLanguages();
        if(count($langs)>1)
        {
            foreach ($langs as $lang)
            {
                $label=LanguageManager::getInstance()->getLabelByLang($lang);
                $kurl=new KURL();
                $kurl->addOrReplace("lang",$lang);
                $language[$label]=["tag"=>$lang,"url"=>$kurl->printURLWithoutAmp()];
            }
        }        
        $this->addItem("languages",$language);
        $view="/view/public/menu/menubar.php";
        $this->addComponent(new KTemplateForTwigComponent($view, $this->getData()));
    }
}