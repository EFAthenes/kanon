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
class KCopyTemplate extends KAbstractTemplateController
{

    public function execute(): bool
    {
        $this->makeTitle(Ki18::_("KTEMPLATE_TITLE_COPY"), "fa-solid fa-copy", RoutesItems::$KTEMPLATE_PAGE);
   
        $templateFileName = '';
        $newTemplateName = '';
        
        $comp= new TileComponent();
        $this->addComponent($comp);
        $form = new FormComponent("", "form_template");

        if (KInput::checkInputGet('template_name', KInput::$VARIABLE_STRING, $templateFileName))
        {
            $templateFolder = KApp::getInstance()->getTemplateFolder();
            $source = new KFile($templateFolder.KFile::separator().$templateFileName);

            // Si le formulaire a été soumis avec un nouveau nom
            if (KInput::checkInputPost('new_template_name', KInput::$VARIABLE_STRING, $newTemplateName))
            {
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $newTemplateName))
                {
                    $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_FILTER_NAME"), '', KAlertComponent::$TYPE_ERROR));
                }
                else
                {
                    $copyPath = $templateFolder.KFile::separator().$newTemplateName;

                    if ($source->copyTo($copyPath))
                    {
                        $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_SUCCESS_COPY"), $newTemplateName, KAlertComponent::$TYPE_SUCCESS));
                        return true;
                    }
                    else
                    {
                        $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_ERROR_COPY").$source->getError(), '', KAlertComponent::$TYPE_ERROR));
                    }
                }
            }

            // Affichage du formulaire avec champ de nom
            $inputText = new InputStringComponent(
                    $templateFileName.'_copy', 
                    'new_template_name', 
                    Ki18::_("KTEMPLATE_NEW_COPY_NAME"), 
                    Ki18::_("KTEMPLATE_FILTER_NAME"),
                    false,
                    false,
                    2,10);
            $form->addComponent($inputText);
            $comp->addComponent($form);
        }
        else
        {
            $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_NO_COPY_SELECTED"), '', KAlertComponent::$TYPE_WARNING));
        }

        $valider = new ButtonComponent(Ki18::_("KTEMPLATE_CONFIRM_BUTTON"), ButtonComponent::$TYPE_SUCCESS, "");
        $valider->setSubmitForm("form_template");
        $form->addComponent($valider);

        return true;
    }
}