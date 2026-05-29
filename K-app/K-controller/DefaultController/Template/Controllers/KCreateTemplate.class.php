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

class KCreateTemplate extends KAbstractTemplateController
{

    public function execute(): bool
    {
        $this->makeTitle(Ki18::_("KTEMPLATE_TITLE_CREATION"),"fa-solid fa-plus",RoutesItems::$KTEMPLATE_PAGE);
         

        // Traitement de la création
        $comp= new TileComponent();
        $this->addComponent($comp);

        $templateName = '';

        if (KInput::checkInputPost('nom_template', KInput::$VARIABLE_STRING, $templateName))
        {

            if (empty($templateName))
            {
                $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_EMPTY_NAME"), '', KAlertComponent::$TYPE_ERROR));
            }
            elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $templateName))
            {
                $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_FILTER_NAME"), '', KAlertComponent::$TYPE_ERROR));
            }
            else
            {
                $dir = new KFile(KApp::getInstance()->getTemplateFolder() . KFile::separator() . $templateName);

                if ($dir->exists())
                {

                    $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_EXISTING"), '', KAlertComponent::$TYPE_WARNING));
                }
                else
                {

                    if ($dir->mkdir())
                    {

                        //création des sous dossiers css et views
                        $cssdir = new KFile($dir->getPath() . KFile::separator() . 'css');
                        $viewsdir = new KFile($dir->getPath() . KFile::separator() . 'view');

                        if (!$cssdir->exists())
                        {
                            $cssdir->mkdir();
                        }
                        if (!$viewsdir->exists())
                        {
                            $viewsdir->mkdir();
                        }

                        //creation du fichier public.css
                        $cssfile = new KFile($cssdir->getPath() . KFile::separator() . 'public.css');
                        if (!$cssfile->exists())
                        {
                            $cssfile->createFile();
                        }

                        $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_SUCCESS_TEMPLATE"), '', KAlertComponent::$TYPE_SUCCESS));
                    }
                    else
                    {
                        $comp->addComponent(new KAlertComponent(Ki18::_("KTEMPLATE_ERROR_NAME_TEMPLATE"), '', KAlertComponent::$TYPE_ERROR));
                    }
                }
            }
        }

        // Formulaire de création
        $form = new FormComponent("", "form_template");
        $inputText = new InputStringComponent(
                '', 
                'nom_template', 
                Ki18::_("KTEMPLATE_NAME"), 
                Ki18::_("KTEMPLATE_FILTER_NAME"),
                false,
                false,
                2,10);
        
        $form->addComponent($inputText);

        $valider = new ButtonComponent(Ki18::_("KTEMPLATE_CONFIRM_BUTTON"), ButtonComponent::$TYPE_SUCCESS, "");
        $valider->setSubmitForm("form_template");
        $form->addComponent($valider);
        $comp->addComponent($form);

        return true;
    }
}
