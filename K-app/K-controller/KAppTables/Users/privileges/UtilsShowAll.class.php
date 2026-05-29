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
 * Description of UtilsShowAll
 *
 * @author Hippolyte
 */
abstract class UtilsShowAll extends PrivilegesUtilsKController
{
    
    /**
     * URL de la table de modifications.
     * @var string
     */
    protected string $url_modify_table="";
    
    private KTitleLayoutAdmin $title;

    /**
     * Met à jour le KComponent du title.
     * @param KTitleLayoutAdmin $title
     * @return void
     */
    protected function setTitle(KTitleLayoutAdmin $title): void
    {
        $this->title=$title;
    }

    /**
     * Retourne title si existant, null sinon.
     * @return KTitleLayoutAdmin|null
     */
    protected function getTitle(): ?KTitleLayoutAdmin
    {
        return $this->title;
    }

    /**
     * Met à jour l'URL de la table de modifications.
     * @param string $url_modify_table
     * @return void
     */
    protected function setUrl_modify_table(string $url_modify_table): void
    {
        $this->url_modify_table=$url_modify_table;
    }

    /**
     * Retourne l'URL de la table de modifications.
     * @return string 
     */
    protected function getUrl_modify_table(): string
    {
        return $this->url_modify_table;
    }

    /**
     * Affiche une notification en bandeau selon la réussite ou non de la 
     * suppression d'un élément de la table.
     * @param int $check_delete {1 : suppression réussie ; 2 : échec}
     * @return void
     */
    protected function displayDeleteAlertComponent(int $check_delete): void
    {
        if($check_delete==1)
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_TITLE"),
                            LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_1")));
        }
        else if($check_delete==2)
        {
            $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                            LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_9"),
                            KAlertComponent::$TYPE_ERROR));
        }
    }
    
    /**
     * Affiche un message d'erreur pour indiquer à l'utilisateur que la table est vide.
     * @param string $tablename Nom de la table
     * @return void
     */
    protected function displayErrorMessageTableNotRecognized(string $tablename): void
    {
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_3")
                .' : '.$tablename.'//'.KObject::makeClassNameFromTableName($tablename)),"-12");
        $this->addComponent($grid);
    }    

    /**
     * Affiche un message d'erreur pour indiquer à l'utilisateur que la table est vide.
     * @param string $tablename Nom de la table
     * @return void
     */
    protected function displayErrorMessageEmptyTable(string $tablename): void
    {
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_4")
                .' : '.$tablename.'//'.KObject::makeClassNameFromTableName($tablename)),"-12");
        $this->addComponent($grid);
    }

    /**
     * Ajoute un bouton "Ajouter un élément" dans la bar de titre.
     * @param string $url URL de création d'un élément
     * @return void
     */
    protected function addButtonAddOnTitleBar(string $url): void
    {
        $addButton=new KTitleButton(LanguageManager::_("SHOW_TABLE_CONTENT_BUTTON_ADD"),
                KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
        $addButton->setActionURL($url);
        $this->getTitle()->addKTitleButton($addButton);
    }
}