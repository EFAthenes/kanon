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
/*
 * @author Hajer Klai
 * @author Mulot Louis
 */

class KDeleteTemplate extends KAbstractTemplateController
{

    public function execute(): bool
    {
        $this->makeTitle(Ki18::_("KTEMPLATE_TITLE_DELETE"),"fa-solid fa-trash",RoutesItems::$KTEMPLATE_PAGE);

        // Traitement de la suppression
        $comp= new TileComponent();
        $this->addComponent($comp);
        
        $templateFileName = '';

        if (KInput::checkInputGet('template_name', KInput::$VARIABLE_STRING, $templateFileName))
        {

            $dir = new KFile(KApp::getInstance()->getTemplateFolder() . KFile::separator() . $templateFileName);

            if(!$dir->exists())
            {
                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_DELETE_DOESNT_EXIST_ERROR"), $templateFileName, KAlertComponent::$TYPE_ERROR));                
            }
            else if ($dir->delete())
            {

                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_SUCCESS_DELETED_FILE"), $templateFileName, KAlertComponent::$TYPE_SUCCESS));
            }
            else
            {

                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_DELETE_ERROR"), $templateFileName, KAlertComponent::$TYPE_ERROR));
            }
        }


        return true;
    }
}
