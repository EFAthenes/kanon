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
class ShowEditTableOptions extends AbstractTableController
{
    /**
     * 
     * @var array<int,string>|null
     */
    private ?array $fieldsReadOnly=null;
    
    public static string $GET_CHECK_POST="check";
    public static string  $GET_CHECK_DELETE="delete";
    
    private bool $show_back_to_all_table_button=true;
    private string  $url_back_button="";
    
    function getShow_back_to_all_table_button() : bool
    {
        return $this->show_back_to_all_table_button;
    }

    function setShow_back_to_all_table_button(bool $show_back_to_all_table_button) : void
    {
        $this->show_back_to_all_table_button=$show_back_to_all_table_button;
    }
    
    function getUrl_back_button() : string
    {
        return $this->url_back_button;
    }
    
    function setUrl_back_button(mixed $url_back_button) :void 
    {
        $this->url_back_button="".$url_back_button;
    }   
    
    public function init() : void
    {
        $this->url_back_button=KRoutesItems::$SHOW_ALL_TABLES;    
    }
    
    /**
     * 
     * @return array<int,string>|null
     */
    public function getFieldsReadOnly() : ?array
    {
        return $this->fieldsReadOnly;
    }

    public function execute(): bool
    {
      
        $this->fieldsReadOnly=[KObject::$ID,KObject::$DATE_CREATED,KObject::$DATE_MODIFIED];

        $title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_TITLE "),"fa fa-pencil-square-o");
        
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        KApp::getInstance()->getLayout()->setTitle($title->getTitle());
      
        
        if(!$this->checkTableName())
        {
            return true;
        }
        $tablename=$this->getTablename();
        
        $title->addToTitle(" : ".$tablename);


        if($this->show_back_to_all_table_button)
        {
            $back_url=KRoute::makeURL($this->url_back_button,[self::$GET_PARAM_TABLENAME=>$tablename]);
            $backButton= new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
            $backButton->setActionURL($back_url);
            $title->addKTitleButton($backButton);
                 
            $show_values_url=KRoute::makeURL(KRoutesItems::$SHOW_TABLE_CONTENT,[self::$GET_PARAM_TABLENAME=>$tablename]);
            $show_values_in_table=new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_TITLE_ACTION_1_LONG"),KTitleButton::$TYPE_SECONDARY,"fa fa-server");
            $show_values_in_table->setActionURL($show_values_url);
            $title->addKTitleButton($show_values_in_table);
        }   
        
        
        //######################################################################
        // Export 
        //######################################################################
        $grid=new TileGridComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_TITLE_1")),"-12");
        $widget_1=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_XLS"), "fa fa-file-excel", WidgetComponent::$TYPE_SUCCESS);
        $widget_1->addComponent(new LinkKURLComponent(KRoute::makeActionKURL(RoutesItems::$EXPORT_TABLE_CONTENT_XLS,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_XLS_URL")));
        $widget_2=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_JSON"), "fa fa-file-code", WidgetComponent::$TYPE_SUCCESS);
        $widget_2->addComponent(new LinkKURLComponent(KRoute::makeActionKURL(RoutesItems::$EXPORT_TABLE_CONTENT_JSON,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_JSON_URL"),true));
        $widget_3=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_XLS_COLS"), "fa fa-file-alt", WidgetComponent::$TYPE_SUCCESS);
        $widget_3->addComponent(new LinkKURLComponent(KRoute::makeActionKURL(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_COLS,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_XLS_COLS_URL")));
        $widget_4=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_XLS_STRUCTURE"), "fa fa-file-alt", WidgetComponent::$TYPE_SUCCESS);
        $widget_4->addComponent(new LinkKURLComponent(KRoute::makeActionKURL(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_STRUCTURE,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EXPORT_XLS_STRUCTURE_URL")));
        
        
        $grid->addColComponent($widget_1,"-12 col-xl-3");
        $grid->addColComponent($widget_2,"-12 col-xl-3");
        $grid->addColComponent($widget_3,"-12 col-xl-3");
        $grid->addColComponent($widget_4,"-12 col-xl-3");
        
        
        //######################################################################
        // IMPORT 
        //######################################################################        
        $grid->addRowComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_TITLE_2")),"-12");        
        $grid->addRowComponent();
        $grid->addColComponent(new HTMLComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_LABEL")),"-12");      
         $grid->addRowComponent();
        $widget_4=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS"), "fa fa-download", WidgetComponent::$TYPE_SUCCESS);       
        $hor=new HorizontalDivComponent();
        $hor->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_IMPORT_RECORDS,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_URL")));
        $hor->addComponent(new LinkKURLComponent(KRoute::makeActionKURL(RoutesItems::$EXPORT_TABLE_CONTENT_XLS_STRUCTURE,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_URL_STRUCTURE")));
        $widget_4->addComponent($hor);
        $grid->addColComponent($widget_4,"-12 col-xl-6");
        
        $widget_5=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS"), "fa fa-file-download", WidgetComponent::$TYPE_INFO);
        $hor2=new HorizontalDivComponent();
        //$hor3->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_DELETE_RECORDS_BY_ID,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_URL")));
        $hor2->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_UPDATE_RECORDS_BY_ID,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_URL")));
        //$hor2->addComponent(new LinkComponent("", LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_URL_STRUCTURE")));
        $widget_5->addComponent($hor2);
        $grid->addColComponent($widget_5,"-12 col-xl-6"); 
        
        $grid->addRowComponent();
        
        $widget_6b=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_2"), "fa fa-cloud-download-alt", WidgetComponent::$TYPE_WARNING);
        $hor31=new HorizontalDivComponent();
        $hor31->addComponent(new LinkComponent("", LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));
        //$hor31->addComponent(new LinkComponent("", LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_URL_STRUCTURE")));
        $widget_6b->addComponent($hor31);        
        $grid->addColComponent($widget_6b,"-12 col-xl-6");         
        
        $widget_6=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_BY_XLS"), "fa fa-trash", WidgetComponent::$TYPE_ERROR);
        $hor3=new HorizontalDivComponent();
        $hor3->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_DELETE_RECORDS_BY_ID,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_URL")));
        //$hor3->addComponent(new LinkComponent("", LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_XLS_URL_STRUCTURE")));
        $widget_6->addComponent($hor3);        
        $grid->addColComponent($widget_6,"-12 col-xl-6"); 
        //######################################################################
        // OPERATION 
        //######################################################################    
        
        //fa-plus-circle fa-minus-circle
        $grid->addRowComponent();
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_TITLE_3")),"-12");
        $grid->addRowComponent();
        $widget_c1=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ADD_COLUMN"), "fa fa-plus-circle ", WidgetComponent::$TYPE_SUCCESS);
        $widget_c1->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_STRUCTURE_TABLE_ADD,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));        
        $grid->addColComponent($widget_c1,"-12 col-xl-3");
        $widget_c3=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_COLUMN"), "fa fa-pencil-alt ", WidgetComponent::$TYPE_INFO);
        $widget_c3->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_STRUCTURE_TABLE_RENAME,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));        
        $grid->addColComponent($widget_c3,"-12 col-xl-3");
        $widget_c2=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_REMOVE_COLUMN"), "fa fa-minus-circle ", WidgetComponent::$TYPE_ERROR);
        $widget_c2->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_STRUCTURE_TABLE_REMOVE,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));        
        $grid->addColComponent($widget_c2,"-12 col-xl-3");   
        $widget_c4=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_IMPORT_COLUMNS"), "fa fa-columns ", WidgetComponent::$TYPE_INFO);
        $widget_c4->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_STRUCTURE_TABLE_UPLOAD_XLS,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_UPDATE_BY_XLS_URL")));        
        $grid->addColComponent($widget_c4,"-12 col-xl-3");        
        
        
        
        $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_TITLE_4")),"-12");
        $grid->addRowComponent();
        
        $widget_4_1=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_RENAME_TABLE"), "fas fa-signature", WidgetComponent::$TYPE_SUCCESS);       
        $widget_4_1->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_RENAME,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));
        $grid->addColComponent($widget_4_1,"-12 col-xl-3");
        
        $widget_4_2=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_DELETE_TABLE"), "far fa-trash-alt", WidgetComponent::$TYPE_ERROR);       
        $widget_4_2->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_DELETE,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));
        $grid->addColComponent($widget_4_2,"-12 col-xl-3");   
        
        $widget_4_3=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_COPY_TABLE"), "far fa-copy", WidgetComponent::$TYPE_SUCCESS);       
        $widget_4_3->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_COPY,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));
        $grid->addColComponent($widget_4_3,"-12 col-xl-3");
        
        $widget_4_4=new WidgetComponent(LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_EMPTY_TABLE"), "fa fa-recycle", WidgetComponent::$TYPE_WARNING);       
        $widget_4_4->addComponent(new LinkKURLComponent(KRoute::makeKURL(RoutesItems::$MODIFY_TABLE_EMPTY,[self::$GET_PARAM_TABLENAME=>$tablename]), LanguageManager::_("SHOW_EDIT_TABLE_OPTIONS_ACCESS_TO_PAGE")));
        $grid->addColComponent($widget_4_4,"-12 col-xl-3");

        
        
        $this->addComponent($grid);
        
        return true;        
    }
}

