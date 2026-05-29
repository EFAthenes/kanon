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
 */
class KEditFileTemplate extends KController
{

    public function execute(): bool
    {
        //$this->addComponent(new PostGetComponent());

//        KApp::getInstance()->getLayout()->setTitle(LanguageManager::_("EDIT_FILE_TEMPLATE"));
//        $title = new KTitleLayoutAdmin(LanguageManager::_("EDIT_FILE_TEMPLATE"), "fa-solid fa-pen-to-square");
//        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER, $title);

        $templateName = '';
        $valueSelect = '';

        if (!KInput::checkInputPost('template_name', KInput::$VARIABLE_STRING, $templateName) || empty($templateName))
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NONE_SELECTED_TEMPLATE_NAME"), '', KAlertComponent::$TYPE_ERROR));
            return false;
        }

        if (!KInput::checkInputPost('value_select', KInput::$VARIABLE_STRING, $valueSelect) || empty($valueSelect))
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NONE_FILE_SELECTED"), '', KAlertComponent::$TYPE_ERROR));
            return false;
        }

        //  le chemin absolu vers le fichier
        $templateFolder = new KFile(KApp::getInstance()->getTemplateFolder().KFile::separator().$templateName);
        $fullFilePath = new KFile($templateFolder->getPath().KFile::separator().$valueSelect);

        // Vérifier que le fichier existe

        if (!$fullFilePath->exists() || !$fullFilePath->isFile())
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("NOT_FOUND_TEMPLATE"), $templateName, KAlertComponent::$TYPE_ERROR));
            return false;
        }
        
        // Set save button
        $boutonSauvegarder = new ButtonComponent(LanguageManager::_("SAVE_BUTTON"), ButtonComponent::$TYPE_SUCCESS);
        $boutonSauvegarder->setClickAction("SaveFile();");
        $this->addComponent(new DivIdComponent("result"));
        $this->addComponent($boutonSauvegarder);
        $this->addComponent(new BreakLineComponent(2));

        // Lire le contenu du fichier
        $fileContent = $fullFilePath->toContentString();

        // Ajouter le CodeMirrorEditorComponent
        $editor = new CodeMirrorEditorComponent("file_editor".KRandom::makeRandomUniquId(), $fileContent); // à revoir
        $editor->setHeight(500);
        $this->addComponent($editor);
        $this->addComponent(new BreakLineComponent(4));
        if (str_ends_with($fullFilePath->getPath(), ".css"))
        {
            $editor->setMode(CodeMirrorEditorComponent::CSS);
        }


        $finalroute = KRoute::makeActionFullURLNoAmp(RoutesItems::$KSAVE_FILE_TEMPLATE);
        $script = '     
<script>

    function SaveFile() {
    
        var value_select = $("#template_subfolder").val();
        var file_content = '.$editor->getVarJsName().' .getValue();        
        var template_name = "'.$templateName.'";

        console.log("Valeur sélectionnée :", value_select);
        console.log("Contenu du fichier :", file_content);
        console.log("Template :", template_name);

        if (!value_select) {
            alert("Veuillez sélectionner un fichier.");
            return;
        }

        console.log("URL AJAX appelée : ", "'.$finalroute.'");
        

        $.ajax({
            url: "'.$finalroute.'",
            type: "POST",
            data: {
                template_name: "'.$templateName.'",
                value_select: value_select,
                file_content: file_content

            },
            success: function(data) {
                console.log("Réponse serveur :", data);
                $("#result").html(data);
            },
            error: function(xhr,status, error) {

                console.error("Erreur AJAX :", xhr.status, error);
                console.log("réponse log :", xhr.responseText);
                alert("Une erreur est survenue: " + xhr.responseText);
                //alert("erreur : " + error);


            }
        });
    }
</script> 
    ';

        
        $this->addString($script);

        return true;
    }
}