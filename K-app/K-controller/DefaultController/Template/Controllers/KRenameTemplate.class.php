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

class KRenameTemplate extends KAbstractTemplateController
{

    public function execute(): bool
    {
        $this->makeTitle(Ki18::_("KTEMPLATE_TITLE_RENAME"), "fa-solid fa-pen", RoutesItems::$KTEMPLATE_PAGE);

        $comp = new TileComponent();
        $this->addComponent($comp);

        $templateName = '';
        if (!KInput::checkInputGet('template_name', KInput::$VARIABLE_STRING, $templateName))
        {
            $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_NO_TEMPLATE_SELECTED"), '', KAlertComponent::$TYPE_ERROR));
            return true;
        }

        $newTemplateName = '';

        if (KInput::checkInputPost('new_template_name', KInput::$VARIABLE_STRING, $newTemplateName))
        {
            if (empty($newTemplateName))
            {

                //verification
                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_NEW_NAME"), '', KAlertComponent::$TYPE_ERROR));
            }
            elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $newTemplateName))
            {

                $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_FILTER_NAME"), '', KAlertComponent::$TYPE_ERROR));
            }
            else
            {

                $oldDir = new KFile(KApp::getInstance()->getTemplateFolder() . KFile::separator() . $templateName);
                $newDirPath = KApp::getInstance()->getTemplateFolder() . KFile::separator() . $newTemplateName;

                if (!$oldDir->exists())
                {

                    $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_NO_EXISTING"), $templateName, KAlertComponent::$TYPE_ERROR));
                }
                elseif (file_exists($newDirPath))
                {

                    $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_EXISTED_NAME_TEMPLATE"), $newTemplateName, KAlertComponent::$TYPE_WARNING));
                }
                elseif ($oldDir->renameTo($newDirPath))
                {

                    //afficher le nom actuel
                    $comp->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_SUCCESS_RENAME"), $newTemplateName, KAlertComponent::$TYPE_SUCCESS));

                    $templateName = $newTemplateName;

                    return true;
                }
            }
        }

        $form = new FormComponent("", "form_template");
        $inputText = new InputStringComponent(
                $templateName,
                'new_template_name',
                LanguageManager::_("KTEMPLATE_NEW_COPY_NAME"),
                LanguageManager::_("KTEMPLATE_FILTER_NAME"),
                false, false,
                2, 10);
        $form->addComponent($inputText);

        $valider = new ButtonComponent(LanguageManager::_("KTEMPLATE_CONFIRM_BUTTON"), ButtonComponent::$TYPE_SUCCESS, "");
        $valider->setSubmitForm("form_template");
        $form->addComponent($valider);

        $comp->addComponent($form);

        return true;
    }
}
