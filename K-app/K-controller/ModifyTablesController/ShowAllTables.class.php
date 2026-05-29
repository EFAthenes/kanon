<?php
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
declare(strict_types=1);
class ShowAllTables extends AbstractTableController
{
    public function execute(): bool
    {
        $sql=Sql::getInstance();
        KApp::getInstance()->getLayout()->setTitle(LanguageManager::_("SHOW_ALL_TABLES_TITLE_LABEL")." => ".$sql->getBdd());

        $title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_ALL_TABLES_TITLE"),"fa fa-database");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        //SHOW_ALL_TABLES_BUTTON_NEW_TABLE
        $button_new_table=new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BUTTON_NEW_TABLE"), KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
        $button_new_table->setActionURL(KRoute::makeKURL(RoutesItems::$CREATE_TABLE_IN_DB)->printURLWithoutAmp());
        $title->addKTitleButton($button_new_table);
        
        $results=$sql->getAllTablesNamesInArray();
        
        $arrayTablesDb=array();
        foreach ($results as $table)
        {
            $arrayTablesDb_line=array();
            
            $linkComponent=new LinkDataTableComponent(KRoute::makeURL(RoutesItems::$SHOW_TABLE_CONTENT,array(self::$GET_PARAM_TABLENAME=>$table)),LanguageManager::_("SHOW_ALL_TABLES_TITLE_ACTION_1"));
            $linkComponent2=new LinkDataTableComponent(KRoute::makeURL(RoutesItems::$SHOW_TABLE_EDIT_OPTIONS,array(self::$GET_PARAM_TABLENAME=>$table)),LanguageManager::_("SHOW_ALL_TABLES_TITLE_ACTION_2"));
            $arrayTablesDb_line[]=$linkComponent;
            $arrayTablesDb_line[]=$linkComponent2;
            $arrayTablesDb_line[]=$table;
            $arrayTablesDb_line[]=$sql->num_fields_table($table);
            $arrayTablesDb_line[]=$sql->num_rows_table($table);
            $arrayTablesDb[]=$arrayTablesDb_line;
        }
        
        $arrayTablesDb_fields_name=[LanguageManager::_("SHOW_ALL_TABLES_TITLE_COL1"),
                                    LanguageManager::_("SHOW_ALL_TABLES_TITLE_COL2"),
                                    LanguageManager::_("SHOW_ALL_TABLES_TITLE_COL3"),
                                    LanguageManager::_("SHOW_ALL_TABLES_TITLE_COL4"),
                                    LanguageManager::_("SHOW_ALL_TABLES_TITLE_COL5")];
        
        $table=new DataTableSimpleTableComponent('Table_searchFiche',$arrayTablesDb_fields_name,$arrayTablesDb);
        $table->setButton_bar(true);
        
        $row=new RowComponent();
        $tile=new TileComponent();
        $col= new ColComponent("-lg-12");
        $titre=new TitleComponent(LanguageManager::_("SHOW_ALL_TABLES_TITLE_LABEL")." '' ".$sql->getBdd()." '' :");
        $this->addComponent($row);
        $row->addComponent($col);
        $col->addComponent($tile);
        $tile->addComponent($titre);
        $tile->addComponent($table);

        return true;
    }

}