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
class KModifyTemplate extends KAbstractTemplateController
{
    public function execute(): bool
    {
        $this->makeTitle(Ki18::_("KTEMPLATE_CONTENT") ,"fa-solid fa-copy", RoutesItems::$KTEMPLATE_PAGE);

        $tuile = new TileComponent();
        $row=new RowColsComponent();
        $tuile->addComponent($row);
        $this->addComponent($tuile);
        KNotify::addJsLib();

        $template_name = '';
        $script = '';
        
        if (KInput::checkInputGet('template_name', KInput::$VARIABLE_STRING, $template_name))
        {
            $this->makeTitle(Ki18::_("KTEMPLATE_CONTENT")." ".$template_name ,"fa-solid fa-copy", RoutesItems::$KTEMPLATE_PAGE);

            

            $templateDir = new KFile(KApp::getInstance()->getTemplateFolder().KFile::separator().$template_name);

            if ($templateDir->exists() && $templateDir->isDirectory())
            {
                $options = $this->ListFiles($templateDir);
                $select = new KSelectComponent("template_subfolder", $options);
                $row->addColComponent($this->makeButton($template_name),1);
                $row->addColComponent($select,11);
            }
            else
            {
                $this->addComponent(new KAlertComponent(LanguageManager::_("KTEMPLATE_NONE_SELECTED_FILE"), '', KAlertComponent::$TYPE_WARNING));
            }

            $this->addComponent(new DivIdComponent("file_content"));
        }



        $editor = new CodeMirrorEditorComponent("file_content", "");

        return true;
    }

    /**
     * 
     * @param KFile $repertoire
     * @param string $chemin
     * @return array <int,array<int,string>>
     */
    private function ListFiles(KFile $repertoire, string $chemin = ''): array
    {
        $fichiers = [];

        if (!$repertoire->exists() || !$repertoire->isDirectory())
        {
            return $fichiers;
        }
        $resultat = array();
        $liste = $repertoire->listFilesToArray();
        if (!is_null($liste))
        {
            foreach ($liste as $file)
            {
                $relativePath = $chemin.$file->getName();

                if ($file->isFile())
                {
                    $resultat[] = [$relativePath, $relativePath];
                }
                elseif ($file->isDirectory())
                {
                    $resultat = array_merge($resultat, $this->ListFiles($file, $relativePath.'/'));
                }
            }
        }
        return $resultat;
    }
    
    
    private function makeButton(string $template_name) : ButtonComponent
    {
        $boutonouvrir = new ButtonComponent((LanguageManager::_("KTEMPLATE_CONFIRM_BUTTON")), ButtonComponent::$TYPE_SECONDARY);
        $boutonouvrir->setClickAction("Openfile();");
        $boutonouvrir->addJSText($this->setJsOpenTemplateFileToEditor($template_name)); 
        return $boutonouvrir;
    }
    
    private function setJsOpenTemplateFileToEditor(string $template_name) : string
    {
        $route = KRoute::makeActionURLNoAmp(RoutesItems::$KEDIT_FILE_TEMPLATE);
        $script= '
function Openfile() {
    var value_select = $("#template_subfolder").val();

    if (!value_select) {
        alert("Veuillez sélectionner un fichier.");
        return;
    }

    $.ajax({
        url: "'.$route.'",
        type: "POST",
        data: {
        template_name: "'.$template_name.'",
        value_select: value_select
    },
    success: function(data) {
        $("#file_content").html(data);
    },
    error: function() {
    alert("Une erreur est survenue lors de l\'ouverture du fichier.");
        }
    });
}';    
        return $script;
    }
}