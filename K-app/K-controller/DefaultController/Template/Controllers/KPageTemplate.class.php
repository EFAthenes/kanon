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

class KPageTemplate extends KAbstractTemplateController
{

    public function execute(): bool
    {

        KApp::getInstance()->getLayout()->setTitle(Ki18::_("KTEMPLATE_TITLE"));
        $title = new KTitleLayoutAdmin(Ki18::_("KTEMPLATE_TITLE"), "fa-solid fa-image");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER, $title);
        $this->makeTitle(Ki18::_("KTEMPLATE_TITLE"),"fa-solid fa-image");
        
        $dir = new KFile(KApp::getInstance()->getTemplateFolder());

        if(!$dir->exists()||!$dir->isDirectory())
        {
            $this->addComponent(new KAlertComponent("Error with the template.","Folder for the templates doesn't exist.", KAlertComponent::$TYPE_ERROR));
            return true;
        }

        // mettre le fond en blanc // 
        $tile = new TileComponent();
        $this->addComponent($tile);

        $listFileNames = $dir->listFilesNameToList();
        
        //KDebugger::getInstance()->_($listFileNames, "listFileNames");

        // comme ce n'est pas un array , il y'a une étape en +
        if ($listFileNames === null)
        {
            $listFileNames = [];
        }
        elseif ($listFileNames instanceof ArrayList)
        {
            $listFileNames = $listFileNames->toArray();
        }

        // Dossier à exclure
        $excludedFolders = ['i18n'];

        // Filtrage
        $listFileNames = array_filter($listFileNames, function ($fileName) use ($excludedFolders)
        {
            return !in_array($fileName, $excludedFolders);
        });

        //bouton création

        $nouvTemplate = new ButtonComponent(Ki18::_('KTEMPLATE_CREATION'), "btn btn-danger", "fa-solid fa-image");
        $buttonWrapper = new DivClassComponent("mb-4");
        $buttonWrapper->addComponent($nouvTemplate);
        $tile->addComponent($buttonWrapper);

        $nouvTemplate->setActionRouteItem(RoutesItems::$KCREATE_TEMPLATE);

        // outils d'activation et de configuration des templates sur le site web

        $templateConfig = TemplateManager::getInstance()->getConfigTemplate();
        $templateActif = TemplateManager::getInstance()->getActiveTemplateName();

        //mise en place du tableau

        $arrayColumns = [
            Ki18::_("KTEMPLATE_TAB_COL_NAME"), 
            Ki18::_("KTEMPLATE_TAB_COL_ACTIONS"), ""];
        $arrayLines = [];

        
        //KDebugger::getInstance()->_($listFileNames, "listFileNames");
        
        foreach ($listFileNames as $templateFileName)
        {

            //KDebugger::getInstance()->_($templateFileName, "TemplateName ");
            
            $div = new DivClassComponent("action");
            $div->addComponent(new NbspComponent(4));

            $div2 = new DivClassComponent("action");
            $div2->addComponent(new NbspComponent(4));

            // bouton copier
            $copier = new ButtonComponent(Ki18::_("KTEMPLATE_BTN_COPY"), ButtonComponent::$TYPE_SUCCESS, "");
            $div->addComponent($copier);

            // bouton copier qui enclenche le pop up et la redirection
            $copier->setActionURLWithOkPopUp(
                    KRoute::makeKURL(
                            RoutesItems::$KCOPY_TEMPLATE, 
                            ['template_name' => $templateFileName]), 
                    Ki18::_("KTEMPLATE_POP_UP_COPY"), 
                    Ki18::_("KTEMPLATE_ANSWER_YES"), 
                    Ki18::_("KTEMPLATE_ANSWER_NO"));

            $div->addComponent(new NbspComponent(4));

            // bouton supprimer
            $supButton = new ButtonComponent(Ki18::_("KTEMPLATE_BTN_DEL"), ButtonComponent::$TYPE_INFO, "");
            $div->addComponent($supButton);

            // bouton supprimer qui enclenche le pop up et la redirection 
            $supButton->setActionURLWithOkPopUp(
                    KRoute::makeKURL(
                        RoutesItems::$KDEL_TEMPLATE, 
                        ['template_name' => $templateFileName]), 
                    Ki18::_("KTEMPLATE_POP_UP_DELETE"),
                    Ki18::_("KTEMPLATE_ANSWER_YES"), 
                    Ki18::_("KTEMPLATE_ANSWER_NO"));

            // Bouton renommer
            $div->addComponent(new NbspComponent(4));
            $renommer = new ButtonComponent(Ki18::_("KTEMPLATE_BTN_REN"), ButtonComponent::$TYPE_PRIMARY);
            $div->addComponent($renommer);

            // bouton renommer qui enclenche le pop up et la redirection 
            $renommer->setActionURLWithOkPopUp(
                    KRoute::makeKURL(
                            RoutesItems::$KRENAME_TEMPLATE, 
                            ['template_name' => $templateFileName]), 
                        Ki18::_("KTEMPLATE_POP_UP_NAME"), 
                        Ki18::_("KTEMPLATE_ANSWER_YES"), 
                        Ki18::_("KTEMPLATE_ANSWER_NO"));

            // bouton appliquer qui enclenche un pop up avant l'activation 
            $div->addComponent(new NbspComponent(4));

            if ($templateFileName === $templateActif)
            {

                // Template actif -> bouton rouge

                $appliquer2 = new ButtonComponent(Ki18::_("KTEMPLATE_BTN_SUCCESS"), ButtonComponent::$TYPE_DANGER);
                $appliquer2->setDisable(true);
                $div->addComponent($appliquer2);
            }
            else
            {

                // Template non actif -> bouton gris

                $appliquer1 = new ButtonComponent(Ki18::_("KTEMPLATE_BTN_APPLY"), ButtonComponent::$TYPE_SECONDARY);

                $appliquer1->setActionURLWithOkPopUp(
                        KRoute::makeKURL(
                                RoutesItems::$KACTIVATE_TEMPLATE, 
                                ['appliquer_template' => $templateFileName]), 
                            Ki18::_("KTEMPLATE_POP_UP_ACTIVATE"), 
                            Ki18::_("KTEMPLATE_ANSWER_YES"), 
                            Ki18::_("KTEMPLATE_ANSWER_NO"));

                $div->addComponent($appliquer1);
            }

            $div->addComponent(new NbspComponent(4));


            // Bouton modifier 
            $div2->addComponent(new NbspComponent(4));
            $Modifier = new ButtonComponent(Ki18::_("KTEMPLATE_BTN_MODIFY"), ButtonComponent::$TYPE_WARNING);
            $Modifier->setActionURL(
                    KRoute::makeURL(RoutesItems::$KMODIFY_TEMPLATE, 
                            ['template_name' => $templateFileName]));
            $div2->addComponent($Modifier);

            //création du tableau

            $arrayLines[] = [
                $templateFileName,
                $div,
                $div2,
            ];
        }
        
        //KDebugger::getInstance()->_($arrayLines, "Lines ");

        // affichage du tableau
        $comp = new DataTableSimpleTableComponent('template_list', $arrayColumns, $arrayLines);
        $tile->addComponent($comp);

        return true;
    }
}
