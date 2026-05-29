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
class ShowTableContent extends AbstractTableController
{ 
    private bool $show_back_to_all_table_button=true;
    private bool $show_edit_table_button=true;
    private bool $show_button_bar=true;
    private bool $show_tablename_title=true;
    private string $url_modify_table="";
    private string $url_back_button="";
    
    
    /**
     *  @var array <int,array<int,string>>
     */
    private array $columnsDef=[]; 
    /**
     * @var array<int,string>
     */
    private array $colsLabelField=[];     
    /**
     * @var array<string,string>
     */
    private array $colsDbField=[];      
    
    
    /**
     * 
     * @var array<int,string>
     */
    private array $hiddenCols=[];
    
    /**
     * 
     * @var array<string,KComponent>
     */
    private array $componentCols=[];
    
    private ?KTitleLayoutAdmin $title=null;
    /**
     * 
     * @var array<string,KField>
     */
    private array $fkFieldslist=[];
    /**
     * 
     * @var array<string,int>
     */
    private array $paramAjax=[];
    public static string $GET_PARAM_NEW_ITEM="new_item";
    
    public const int MAX_FK_ITEMS=50000;

    function setShow_back_to_all_table_button(bool $show_back_to_all_table_button) : void
    {
        $this->show_back_to_all_table_button=$show_back_to_all_table_button;
    }
    
    function setShowEditTableButton(bool $show_edit_table_button) : void
    {
        $this->show_edit_table_button=$show_edit_table_button;
    }
    
    public function getShow_tablename_title(): bool
    {
        return $this->show_tablename_title;
    }

    public function setShow_tablename_title(bool $show_tablename_title): void
    {
        $this->show_tablename_title = $show_tablename_title;
    }  
    
    public function getTitle() : ?KTitleLayoutAdmin
    {
        return $this->title;
    }
    
    public function setAdminTitle(string $label_title,string $icon_title="",string $layoutItem=""): void
    {
        if(empty($layoutItem))
        {
            $layoutItem=KAdminLayout::$HEADER;
        }
        
        $title=new KTitleLayoutAdmin($label_title,$icon_title);
        KApp::getInstance()->getLayout()->addComponent($layoutItem,$title);
        $this->setTitle($title); 
    }    

    public function setTitle(KTitleLayoutAdmin $title): void
    {
        $this->title = $title;
    }
        
    function getUrl_back_button() : string
    {
        return $this->url_back_button;
    }

    function setUrl_back_button(mixed $url_back_button) : void
    {
        $this->url_back_button="".$url_back_button;
    }
    
    function setUrl_modify_table(mixed $url_modify_table) : void
    {
        $this->url_modify_table="".$url_modify_table;
    }
    
    function getUrl_modify_table() : string
    {
        return $this->url_modify_table;
    }
    
    public function setButtonBar(bool $show_button_bar) : void
    {
        $this->show_button_bar=$show_button_bar;
    }
    
    /**
     * 
     * @param array<int,string> $colsName
     * @return void
     */
    public function setHiddenCols(array $colsName) : void
    {
        foreach ($colsName as $colName)
        {
            $this->setHiddenCol($colName);
        }
    }
    
    public function setHiddenCol(string $colName) : void
    {
        $this->hiddenCols[]=$colName;
    } 
    
    public function addColComponent(string $colName, KComponent $component) : void
    {
        $this->componentCols[$colName]=$component;
    }
    
    
    public function init() : void
    {
        $this->url_modify_table=KRoutesItems::$SHOW_EDIT_TABLE_ELEMENT;
        $this->url_back_button=KRoutesItems::$SHOW_ALL_TABLES;    
        
        $title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_TABLE_CONTENT_TITLE"),"fa fa-server ");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        $this->setTitle($title);
    }
    
    
    
    public function execute(): bool
    {
        if(!$this->checkTableName())
        {
            return true;
        }
        $tablename=$this->getTablename();        
//        KDebugger::getInstance()->dump($this->getTablename(),"Table");
//        if($this->getTablename()=='resolver'){
//            $this->url_modify_table = RoutesItems::$CREATE_ARK;
//        }
               
        if($this->show_back_to_all_table_button)
        {
            $back_url=KRoute::makeURL($this->url_back_button);
            $backButton= new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
            $backButton->setActionURL($back_url);  
            $this->getTitle()->addKTitleButton($backButton);     
        }
        
        if($this->show_edit_table_button)
        {
            $edit_table_url=KRoute::makeURL(KRoutesItems::$SHOW_TABLE_EDIT_OPTIONS,[self::$GET_PARAM_TABLENAME=>$this->getTablename()]);
            $edit_table=new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_TITLE_ACTION_2_LONG"),KTitleButton::$TYPE_SECONDARY,"fa fa-list");
            $edit_table->setActionURL($edit_table_url);
            $this->getTitle()->addKTitleButton($edit_table);            
        }
        
        
        KApp::getInstance()->getLayout()->setTitle($this->getTitle()->getTitle()." ".$tablename);
        
        $class_name=KObject::makeClassNameFromTableName($tablename);
        
        $dbList=new DbList($class_name);
        $dbList->setCache(false);
        $max_results=$dbList->getNb();
        if($max_results==0)
        {
            //KDebugger::getInstance()->dump($tablename,"Table");
            $grid = new TileGridComponent();
            //.$dbList->getLast_query_NB()
            $grid->addColComponent(new KAlertComponent($tablename,LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_4"), KAlertComponent::$TYPE_INFO),"-12");
            //$grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_4").' : '.$tablename.'//'.KObject::makeClassNameFromTableName($tablename)),"-12");
            $this->addComponent($grid);    
            $addUrl = KRoute::makeURL($this->url_modify_table, [self::$GET_PARAM_TABLENAME => $tablename, self::$GET_PARAM_NEW_ITEM => "1"]);
            $addButton = new KTitleButton(LanguageManager::_("SHOW_TABLE_CONTENT_BUTTON_ADD"), KTitleButton::$TYPE_PRIMARY, "fa fa-plus-square");
            $addButton->setActionURL($addUrl);
            $this->getTitle()->addKTitleButton($addButton);
            return true;
        }
        $kobject=new $class_name();
        $arrayDb=array();

        if($kobject instanceof KObject)
        {
            $arrayDb[]=$kobject->fieldsToArray();
        }
        else
        {
            $kobject=null;
        }
        
        if(!is_null($kobject))
        {
            //$arrayTablesDb_fields_name=$kobject->fieldsLabelToArray(false);
            
            $arrayTablesDb_fields_name=$this->makeColumns($kobject);
            
//            $cols=[];
//            foreach ($arrayTablesDb_fields_name as $col)
//            {
//                array_unshift( $cols, strtoupper($col) );
//            }
//            $arrayTablesDb_fields_name=$cols;
            
            $table=new DataTableAjaxComponent('Table_'.$tablename,$kobject,$arrayTablesDb_fields_name,$max_results);
            $table->setButton_bar($this->show_button_bar);

            $this->configureHiddensCol($table);
            
            $this->setFieldsArray($table);
         
            $this->prepareNotOrderableFields($table,$kobject);
                    
            $this->prepareComponentForCols($table);
             
            $grid = new TileGridComponent();
            if($this->show_tablename_title)
            {   
                $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_MSG_TITLE").' : '.$tablename),"-12");
            }
            $this->prepareFilterableFields($grid,$kobject);
            
            $table->addParamToAjax($this->paramAjax);
            $grid->addColComponent($table,"-12");
            $this->addComponent($grid);
        }
        else
        {
            $grid = new TileGridComponent();
            $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_4").' : '.$tablename.''),"-12");
            $this->addComponent($grid);            
        }   
        
        $addUrl=KRoute::makeURL($this->url_modify_table,[self::$GET_PARAM_TABLENAME => $tablename ,self::$GET_PARAM_NEW_ITEM=>"1"]);
        $addButton= new KTitleButton(LanguageManager::_("SHOW_TABLE_CONTENT_BUTTON_ADD"),KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
        $addButton->setActionURL($addUrl);  
        $this->getTitle()->addKTitleButton($addButton);          
        
        return true;
    }

    /**
     * 
     * @param DataTableAjaxComponent $table
     * @return void
     */
    protected function configureHiddensCol(DataTableAjaxComponent $table) : void
    {
        if(count($this->hiddenCols))
        {
            foreach ($this->hiddenCols as $colName)
            {
                $table->setColumnNotVisibleByName($colName);
            }
        }
        else
        {
            $table->setColumnNotVisibleByName(KObject::$DATE_CREATED);
            $table->setColumnNotVisibleByName(KObject::$DATE_MODIFIED);
        }
    }  
    
    protected function setFieldsArray(DataTableAjaxComponent $table) : void
    {
        if(count($this->colsLabelField)&&count($this->colsDbField))
        {
            $table->setColsLabelField($this->colsLabelField);
            $table->setColsDbField($this->colsDbField);
        }
    }
    
    /**
     * 
     * @param DataTableAjaxComponent $table
     * @return void
     */
    protected function prepareComponentForCols(DataTableAjaxComponent $table) : void
    {
        if(count($this->componentCols))
        {
            foreach ($this->componentCols as $colName => $colComponent)
            {
                $table->setComponentForColumn($colName,$colComponent);
            }            
        }
        else
        {
            $table->setComponentForColumn("id",new LinkDataTableComponent(
                    KRoute::makeURL($this->url_modify_table,[self::$GET_PARAM_TABLENAME => $this->getTablename() ,KObject::$ID=>""]),"",LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")));
        }
    }
    
    
    protected function prepareNotOrderableFields(DataTableAjaxComponent $table, KObject $object) : void
    {
        $this->fkFieldslist=$object->getAllForeignsKeyFields();
        foreach ($this->fkFieldslist as $fieldName=>$field)
        {
            $table->setColumnNotOrderableByName($fieldName);
        }
    }
    
    protected function prepareFilterableFields(TileGridComponent $grid, KObject $object): void
    {
        foreach ($this->fkFieldslist as $fieldName=>$field)
        {
            $className= KObject::makeClassNameFromTableName($field->getForeign_key_table()); 
            $object = new $className();                
            $theField=$object->getFieldForeignKeyLabel();    
            $dbListFk=DbListManager::getDb($className);
            if($dbListFk->getNb()>self::MAX_FK_ITEMS)
            {
                break;
            }
            $list=$dbListFk->getIdAndFieldValue($theField->getName());
            $array=[];
            foreach ($list as $value)
            {
                $array[]=[$value[0],$value[1]];
            }   
            
            $arraySelected=null;
            $id_fk=0;
            if(KInput::checkInputGet($fieldName, KInput::$VARIABLE_INT, $id_fk))
            {
                $arraySelected=[$id_fk];
                $this->paramAjax[$fieldName]=$id_fk;
            } 
            $select2 =new KSelectComponent($fieldName, $array,$arraySelected, false, $fieldName, false, 12, 12);
            $select2->setAllowNoValue(true);
            $select2->addJsOnChange($this->makeJsSelectFk($fieldName));
            $select2->addJsOnEmpty($this->makeJsEmptySelectFk($fieldName));
            $grid->addColComponent($select2,"-4");
        }        
    }
    
    private function makeJsSelectFk(string $fieldName) : string
    {
        $kurl=new KURL();
        $kurl->removeArg($fieldName);
        $js_on_change='window.location.href = "' . $kurl->printURLWithoutAmp() . '&'.$fieldName.'="+$("#'.$fieldName.'").val();';
        return $js_on_change;
    }
    private function makeJsEmptySelectFk(string $fieldName) : string
    {
        $kurl=new KURL();
        $kurl->removeArg($fieldName);
        $js_on_change='window.location.href = "' . $kurl->printURLWithoutAmp().'"';
        return $js_on_change;
    }    

    /**
     * 
     * @param  array <int,array<int,string>> $columnsDef
     * @return void
     */
    public function setColumnsDef(array $columnsDef) : void
    {
        $this->columnsDef=$columnsDef;
    }

    /**
     * 
     * @param KObject $kobject
     * @return array<int,string>
     */
    protected function makeColumns(KObject $kobject) : array
    {
        $arrayTablesDb_fields_name=$kobject->fieldsLabelToArray(false);
        
        if(count($this->columnsDef))
        {
            $newArrayTablesColsField=[];
            $newArrayTablesColsDb=[];
            foreach ($this->columnsDef as $colDef)
            {
                $db_field=$colDef[0];
                $label_field=$colDef[1];
                //KDebugger::_($db_field, $label_field);
                if(in_array($db_field, $arrayTablesDb_fields_name))
                {
                    $newArrayTablesColsField[]=$label_field;
                    $newArrayTablesColsDb[$label_field]=$db_field;
                    //KDebugger::_($newArrayTablesCols,"New Array");
                }
            }
            if(count($newArrayTablesColsField))
            {
                $this->colsLabelField=$newArrayTablesColsField;
                $this->colsDbField=$newArrayTablesColsDb;
                return $this->colsLabelField;
            }
        }
        
        return $arrayTablesDb_fields_name;
    }    
    
}