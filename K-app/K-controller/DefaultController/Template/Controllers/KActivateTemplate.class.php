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

class KActivateTemplate extends KAbstractTemplateController
{

    public function execute(): bool
    {
        $this->makeTitle(Ki18::_("KTEMPLATE_TITLE_ACTIVATE"), "fa-solid fa-copy", RoutesItems::$KTEMPLATE_PAGE);
        
        $comp= new TileComponent();
        $this->addComponent($comp);
        
        $templateName = "";

        if (KInput::checkInputGet("appliquer_template", KInput::$VARIABLE_STRING, $templateName))
        {
            KDebugger::_($templateName, 'template_name');

            if (TemplateManager::getInstance()->activateTemplate($templateName))
            {
                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_SUCCESS_ACTIVATION"), '', KAlertComponent::$TYPE_SUCCESS));
                ParamManager::getInstance()->addOrReplace("TEMPLATE_STATIC_HTML", $templateName);
            }
            else
            {
                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_INVALID_FILE"), '', KAlertComponent::$TYPE_ERROR));
            }
        }
        else
        {
            $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_NONE_ACTIVATED"), '', KAlertComponent::$TYPE_WARNING));
        }


        return true;
    }
}
