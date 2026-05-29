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
class KSaveFileTemplate extends KController
{
    public function execute(): bool
    {

        $templateName = '';
        $valueSelect = '';
        $fileContent = '';

        //$this->addComponent(new PostGetComponent());

        if (!KInput::checkInputPost('template_name', KInput::$VARIABLE_STRING, $templateName) || empty($templateName)) {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NONE_SELECTED_TEMPLATE_NAME"), '', KAlertComponent::$TYPE_ERROR));
            return true;
        }

        if (!KInput::checkInputPost('value_select', KInput::$VARIABLE_STRING, $valueSelect) || empty($valueSelect)) {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NONE_SELECTED_TEMPLATE_NAME"), '', KAlertComponent::$TYPE_ERROR));
            return true;
        }

        if (!KInput::checkInputPost('file_content', KInput::$VARIABLE_LONG_TEXT, $fileContent)) {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NONE_CONTENT_FILE"), '', KAlertComponent::$TYPE_ERROR));
            return true;
        }

        // chemin du fichier
        $templateFolder = new KFile(KApp::getInstance()->getTemplateFolder() . KFile::separator() . $templateName);
        $fullFilePath = new KFile($templateFolder->getPath() . KFile::separator() . $valueSelect);

        // Vérifier que le fichier existe
        if (!$fullFilePath->exists() || !$fullFilePath->isFile()) {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NOT_FOUND_TEMPLATE"), $templateName, KAlertComponent::$TYPE_ERROR));
            return true;
        }

        // Sauvegarde du contenu dans le fichier
        if (file_put_contents($fullFilePath->getPath(), $fileContent) === false) {
            $this->addComponent(new KAlertComponent(LanguageManager::_("ERROR_SAVE_FILE"), '', KAlertComponent::$TYPE_ERROR));
            return true;
        }

        // Message de succès 
        $this->addComponent(new KNotify(LanguageManager::_("SUCCESS_SAVE_FILE"),LanguageManager::_("SUCCESS_SAVE_FILE"),KNotify::$TYPE_SUCCESS));
        return true;
    }
}
