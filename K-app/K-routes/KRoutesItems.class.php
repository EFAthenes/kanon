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
class KRoutesItems
{
    static string $HOME="home";
    static string $CONNECTION="connection";
    static string $DISCONNECTION="disconnection";
    static string $EDIT_KAPP_USER="edit_kapp_user";
    static string $FORGOT_PASSWORD="forgot_password";
    static string $BASIC_VIEW="basic_view";
    static string $TEST_JSON="test_json";
    static string $STILL_CONNECTED="still_connected";
    static string $CSS_KACHE="css_kache";
    static string $JS_KACHE="js_kache";
    static string $FONT_KACHE="font_kache";
    static string $IMAGE_LOADER="image_loader";
    static string $KCHANGE_LANGUAGE_ITEM="kchange_language_item";
    static string $PHP_INFO="php_info";
    static string $PARAM_INFO="param_info";
    static string $SYSTEM_INFO="system_info";
    static string $LOGS_INFO="logs_info";
    static string $LOGS_CLEAN="logs_clean";
    static string $LOGS_DOWNLOAD="logs_download";
    
    static string $KEDIT_USERS="kedit_users";
    static string $KSHOW_USERS="kshow_users";
    static string $KMANAGE_USERS="kmanage_users";
    static string $KLIST_CONNECTIONS="klist_connections";
    static string $KEDIT_GROUPS="kedit_groups";
    static string $KSHOW_GROUPS="kshow_groups";
    static string $KMANAGE_GROUPS="kmanage_groups";    
    
    static string $SHOW_ALL_TABLES="show_all_tables";
    static string $SHOW_TABLE_CONTENT="show_table_content";
    static string $SHOW_TABLE_EDIT_OPTIONS="show_table_edit_options";
    static string $SHOW_EDIT_TABLE_ELEMENT="show_edit_table_element";
    static string $SHOW_EDIT_TABLE_ELEMENT_AJAX="show_edit_table_element_ajax";
    static string $EXPORT_TABLE_CONTENT_XLS="export_table_content_xls";
    static string $EXPORT_TABLE_CONTENT_XLS_STRUCTURE="export_table_content_xls_structure";   
    static string $EXPORT_TABLE_CONTENT_XLS_COLS="export_table_content_xls_cols";
    static string $EXPORT_TABLE_CONTENT_JSON="export_table_content_json";
    static string $EXPORT_ENTITY_DATA_ITEMS="export_entity_data_items";
    static string $MODIFY_STRUCTURE_TABLE_ADD="modify_structure_table_add";
    static string $MODIFY_STRUCTURE_TABLE_REMOVE="modify_structure_table_remove";
    static string $MODIFY_STRUCTURE_TABLE_RENAME="modify_structure_table_rename";
    static string $MODIFY_STRUCTURE_TABLE_UPLOAD_XLS="modify_structure_table_upload_xls";
    static string $MODIFY_TABLE_RENAME="modify_table_rename";
    static string $MODIFY_TABLE_DELETE="modify_table_delete";
    static string $MODIFY_TABLE_COPY="modify_table_copy";
    static string $MODIFY_TABLE_EMPTY="modify_table_empty";
    static string $MODIFY_TABLE_IMPORT_RECORDS="modify_table_import_records";
    static string $MODIFY_TABLE_UPDATE_RECORDS_BY_ID="modify_table_update_records_by_id";
    static string $MODIFY_TABLE_DELETE_RECORDS_BY_ID="modify_table_delete_records_by_id";
    static string $CREATE_TABLE_IN_DB="create_table_in_db";

    
    static string $KCHANGE_LANGUAGE="kchange_language";
    
    
    static string $UPLOAD_FILES ="upload_files";  
    static string $UPLOAD_FILES_ACTION="upload_files_action";
    
    
    static string $KTEST_KCOMPONENT="ktest_kcomponent";
    
    
    static string $KCMS_HOME="kcms_home";
    static string $KCMS_SHOW_ELEMENTS="kcms_show_elements";
    static string $KCMS_EDIT_ELEMENT="kcms_edit_element";
    static string $KCMS_NAVBAR="kcms_navbar";
    static string $KCMS_MENU="kcms_menu";
    static string $KCMS_CONTENT="kcms_content";
    static string $KCMS_PAGE_LAYOUT="kcms_page_layout";
    
    // Template
    static string $KTEMPLATE_PAGE="ktemplate_page";
    static string $KCREATE_TEMPLATE="kcreate_template";
    static string $KCOPY_TEMPLATE="kcopy_template";
    static string $KDEL_TEMPLATE="kdel_template";
    static string $KRENAME_TEMPLATE="krename_template";
    static string $KACTIVATE_TEMPLATE="kactivate_template";
    static string $KMODIFY_TEMPLATE="kmodify_template";
    static string $KEDIT_FILE_TEMPLATE="kedit_file_template";
    static string $KSAVE_FILE_TEMPLATE="ksave_file_template";
    /**
     * 
     * @var array<string,mixed>
     */
    private array $listOfItems=[];
    protected  function __construct() 
    {
        $class = new ReflectionClass($this);
        $this->listOfItems = $class->getStaticProperties();
    }
    public function __destruct() 
    {
        
    }   
    /**
     * 
     * @return array<string,mixed>
     */
    function getListOfItems() : array
    {
        return $this->listOfItems;
    }
    /**
     * 
     * @param array<string,mixed> $listOfItems
     */
    function setListOfItems(array $listOfItems) : void
    {
        $this->listOfItems=$listOfItems;
    }
    function isItemPresent(string $item) : bool
    {
        if(in_array($item,$this->listOfItems))
        {
            return true;
        }
        return false;
    }
}
