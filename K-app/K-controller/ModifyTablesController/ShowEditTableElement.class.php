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
class ShowEditTableElement extends AbstractTableController
{
    /**
     * 
     * @var array<int,string>|null
     */
    protected ?array $fieldsReadOnly=null;
    protected string $id_form="EDIT_TABLE_ELEMENT";
    
    public static string $GET_PARAM_DUP_ITEM="dup_item";
    public static string $GET_CHECK_POST="check";
    public static string $GET_CHECK_DELETE="delete";
    
    protected bool $show_back_to_all_table_button=true;
    protected string $url_back_button="";
    private string $url_edit_fk_table_element="";
    protected bool $trim=false;
    protected ?KTitleLayoutAdmin $title=null;
    private ?KObject $object =null;
    protected int $new=0;
    protected int $id=0;
    protected int $dup_id=0;
    /**
     * 
     * @var array<string,KComponent>
     */
    protected array $fieldsInputOverride=[];
    /**
     * 
     * @var array<string,string>
     */
    protected array $fieldsLabelsOverride=[];
    /**
     * 
     * @var array<string,KComponent>
     */
    protected array $complimentaryFields=[];
    /**
     * 
     * @var array<int,string>
     */
    protected array $arrayFieldsNameForForm=[];
    private string $mapBounds="";
       
    private const string PATH_IDREF = __DIR__;
    
    /**
     * 
     * @return KObject|null
     */
    protected function getObject() : ?KObject
    {
        return $this->object;
    }
   
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
    
    function setUrl_back_button(string $url_back_button): void
    {
        $this->url_back_button=$url_back_button;
    }  
    
    public function getUrl_edit_fk_table_element() : string
    {
        return $this->url_edit_fk_table_element;
    }

    public function setUrl_edit_fk_table_element(string $url_edit_fk_table_element): void
    {
        $this->url_edit_fk_table_element = $url_edit_fk_table_element;
    }
    
    
    public function init() : void
    {
        //KDebugger::getInstance()->dump($this->getTablename(),"Table");
        $this->url_back_button=KRoutesItems::$SHOW_TABLE_CONTENT;
        $this->url_edit_fk_table_element=KRoutesItems::$SHOW_EDIT_TABLE_ELEMENT;
        $this->fieldsReadOnly=[KObject::$ID,KObject::$DATE_CREATED,KObject::$DATE_MODIFIED];
    }
    
    public function after() : void
    {
        if(class_exists("Auteurs"))
        {
            if($this->getTablename()==Auteurs::$TABLE_NAME)
            {
                KApp::getInstance()->getLayout()->addJsFileToBuffer(self::PATH_IDREF."/idref/idrefMatchAuteur.js");            
            }
        }      
    }  

    protected function makeTitle() : void
    {
        $this->title=null;      
        if(KInput::checkInput(KInput::$INPUT_GET,ShowTableContent::$GET_PARAM_NEW_ITEM,KInput::$VARIABLE_INT,$this->new)&&$this->new==1)
        {
            $this->title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_NEW_TITLE"),"fa fa-plus-square");
        }
        else
        {
            $this->title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_TITLE "),"fa fa-pencil-square-o");
            $kurl=new KURL();
            $kurl->removeArg(ShowTableContent::$GET_PARAM_NEW_ITEM);
            $this->title->setKurl($kurl);
        }
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->title);
        KApp::getInstance()->getLayout()->setTitle($this->title->getTitle());    
        $this->addButtonBack();                
    }
    
    protected function addButtonBack() : void
    {
        if($this->show_back_to_all_table_button)
        {
            $back_url=KRoute::makeURL($this->url_back_button,[ShowAllTables::$GET_PARAM_TABLENAME=>$this->getTablename()]);
            $backButton= new KTitleButton(LanguageManager::_("SHOW_ALL_TABLES_BACK"),KTitleButton::$TYPE_INFO,"fa fa-backward");
            $backButton->setActionURL($back_url);
            $this->title->addKTitleButton($backButton);         
        }           
    }
    
    protected function addButtonForNewItem() : void
    {
        $addButton= new KTitleButton(LanguageManager::_("SHOW_TABLE_CONTENT_BUTTON_ADD"),KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
        $addButton->setSubmitForm($this->id_form);
        $this->title->addKTitleButton($addButton);          
    }
    
    protected function modifyTitleForDeleted() : void
    {
        $this->title=new KTitleLayoutAdmin(LanguageManager::_("SHOW_DEL_TABLE_ELEMENT_TITLE "),"fa fa-pencil-square-o");
        $this->title->unactivateURL();
        if($this->getTablename()!="resolver"){
            KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$this->title);
            KApp::getInstance()->getLayout()->setTitle($this->title->getTitle());
        }
          
        $this->addButtonForNewBlankItem();
        $this->addButtonBack();               
    }  
    
    protected function addButtonForNewBlankItem() : void
    {
        $addUrl=new KURL();
        $addUrl->removeArg(KObject::$ID);
        $addUrl->removeArg(self::$GET_CHECK_POST);
        $addUrl->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        $addUrl->removeArg(self::$GET_CHECK_DELETE);
        $addUrl->removeArg(self::$GET_PARAM_DUP_ITEM);
        $addUrl->addOrReplace(ShowTableContent::$GET_PARAM_NEW_ITEM,1);
        $addAnother=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_NEW_TITLE"),KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
        $addAnother->setActionKURL($addUrl);
        $this->title->addKTitleButton($addAnother);
    }
    
    protected function addButtonForEditedItem() : void
    {    
        // not New Element

        $dupUrl=new KURL();
        $dupUrl->removeArg(KObject::$ID);
        $dupUrl->removeArg(self::$GET_CHECK_POST);
        $dupUrl->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        $dupUrl->removeArg(self::$GET_CHECK_DELETE);
        $dupUrl->addOrReplace(self::$GET_PARAM_DUP_ITEM,$this->object->getId());        
        $dupButton= new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_DUPLICATE"),KTitleButton::$TYPE_WARNING,"fa fa-clone");
        $dupButton->setActionKURL($dupUrl);
        $this->title->addKTitleButton($dupButton);         
        
        
        
        $this->addButtonForNewBlankItem();


        $urlDelete=new KURL();
        $urlDelete->removeArg(self::$GET_CHECK_POST); 
        $urlDelete->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        $urlDelete->removeArg(ShowTableContent::$GET_PARAM_NEW_ITEM); 
        $urlDelete->removeArg(self::$GET_CHECK_DELETE);
        $urlDelete->removeArg(self::$GET_PARAM_DUP_ITEM);
        $urlDelete->addOrReplace(self::$GET_CHECK_DELETE,"1"); 
        $urlDelete->addOrReplace(KObject::$ID,"".$this->object->getId());
        $deleteButton=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_DELETE"),KTitleButton::$TYPE_DANGER,"fa fa-trash");
        $deleteButton->setActionURLWithOkPopUp($urlDelete,
                LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_MSG_SURE"),
                LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_MSG_YES"),
                LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_MSG_NO"));
        $this->title->addKTitleButton($deleteButton);

       
        $saveButton=new KTitleButton(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_SAVE"),KTitleButton::$TYPE_SUCCESS,"fa fa-floppy-disk");
        $saveButton->setSubmitForm($this->id_form);
        $this->title->addKTitleButton($saveButton);         
    }
    
    protected function initIdVar() : bool
    {
        $this->id = 0;
        $this->dup_id=0;
        if(!$this->new && KInput::checkInput(KInput::$INPUT_GET, self::$GET_PARAM_DUP_ITEM, KInput::$VARIABLE_INT, $this->dup_id))
        {
            return true;
        }
        else if (!$this->new && !KInput::checkInput(KInput::$INPUT_GET, KObject::$ID, KInput::$VARIABLE_INT, $this->id)) 
        {
            $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_4"));   
            return false;
        }
        return true;
    }
    
    protected function initObject() : bool
    {
        $className=$this->getObjectName();
        if(class_exists($className))
        {
            $this->object=new $className();
            return true;
        }  
        $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_5").$className.LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_6"));   
        return false;
    }
    
    protected function insertIfDup() : bool
    {   
        if($this->dup_id>0)
        {
            if($this->object->initById($this->dup_id))
            {
                $this->object->resetId();
                //KDebugger::getInstance()->dump($this->object);
                if($this->object->insert())
                {
                    $this->new=3;
                    $this->id=$this->object->getId();
                    $this->setSuccessMessage(LanguageManager::_("SHOW_EDIT_TABLE_ADD_ELEMENT_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_3"));
                    return true;
                }
            }
            $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_13").$this->object->getKerror());
            return false;
        }
        return true;
    }
    
    protected function insertIfNew() : bool
    {     
        if($this->new==2)
        {
            $this->object->getAllInputPost($this->trim);
            if($this->object->insert())
            {
                $this->setSuccessMessage(LanguageManager::_("SHOW_EDIT_TABLE_ADD_ELEMENT_TITLE"),LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_3"));
                $kurl=new KURL();
                $kurl->addOrReplace(KObject::$ID,$this->object->getId());
                $kurl->removeArg(self::$GET_CHECK_DELETE);
                $kurl->removeArg(ShowTableContent::$GET_PARAM_NEW_ITEM);
                $this->title->setKurl($kurl);                
                $this->new=3;
                $this->id=$this->object->getId();
                return true;
            }
            else
            {
                $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_12").$this->object->getKerror());   
                $this->title->setTitle(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_NEW_TITLE"));
                $kurl=new KURL();
                $kurl->addOrReplace(ShowTableContent::$GET_PARAM_NEW_ITEM,1);
                $this->title->setKurl($kurl);
                $this->new=1;
                return false;
            }
        }
        return true;
    }
    
    protected function replaceInputForField(string $fieldName, KComponent $comp) : void
    {
        $this->fieldsInputOverride[$fieldName]=$comp;
    }
    
    protected function replaceLabelsForField(string $fieldName,string $newLabel) : void
    {
        $this->fieldsLabelsOverride[$fieldName]=$newLabel;
    }
    
    private function getReplacedLabel(string $fieldName) : ?string
    {
        if(array_key_exists($fieldName, $this->fieldsLabelsOverride))
        {
            return $this->fieldsLabelsOverride[$fieldName];
        }
        return null;
    }
    
    protected function addOtherComponentForField(string $fieldName, KComponent $comp) : void
    {
        $this->complimentaryFields[$fieldName]=$comp;
    }
    
    /**
     * 
     * @param array<int,string> $arrayFieldsNameForForm
     * @return void
     */
    protected function setFieldsForForm(array $arrayFieldsNameForForm) : void
    {
        $this->arrayFieldsNameForForm=$arrayFieldsNameForForm;
    }
    
    protected function getListOfFieldNames() : ArrayList
    {
        if(count($this->arrayFieldsNameForForm)>0)
        {
            $list=$this->object->getListFieldName();
            $newList=new ArrayList();
            foreach ($this->arrayFieldsNameForForm as $fieldName)
            {
                foreach ($list as $fieldNameList)
                {
                    if($fieldNameList==$fieldName)
                    {
                        $newList->add($fieldNameList);
                    }
                }
            }
            return $newList;
        }
        else
        {
            return $this->object->getListFieldName();
        }
    }
    
    protected function makeInputsForFields() : KComponent 
    {
        $div=new DivClassComponent("col-12");
        $list=$this->getListOfFieldNames();
        foreach($list as $field)
        {
            if(array_key_exists($field,$this->fieldsInputOverride))
            {
                $div->addComponent($this->fieldsInputOverride[$field]);
            }
            else
            {
                $div->addComponent(
                        $this->makeInput(
                            $this->object->getInputValueFieldName($field),
                            $this->object->getKField($field),        
                            $this->getReplacedLabel($field)));
            }
        }
        return $div;
    }
    
    protected function makeUrlForForm() : string
    {
        $url=new KURL();
        if($this->new==1)
        {         
            $url->removeArg(KObject::$ID);
            $url->removeArg(self::$GET_CHECK_POST);
            $url->removeArg(HistoryPage::$STRING_SUBMITED_POST);
            $url->removeArg(self::$GET_CHECK_DELETE);
            $url->removeArg(self::$GET_PARAM_DUP_ITEM);
            $url->addOrReplace(ShowTableContent::$GET_PARAM_NEW_ITEM,"2");        
        }    
        else
        {
            $url->addOrReplace(self::$GET_CHECK_POST,"1");
            $url->addOrReplace(KObject::$ID,"".$this->object->getId());
            $url->removeArg(ShowTableContent::$GET_PARAM_NEW_ITEM);
            $url->removeArg(HistoryPage::$STRING_SUBMITED_POST);
            $url->removeArg(self::$GET_PARAM_DUP_ITEM);
            $url->removeArg(self::$GET_CHECK_DELETE);            
        }
        return $url->printURL();
    }
    
    protected function makeForm() : void
    {     
        $tileComp = new TileComponent();  
        $this->object->fillKFields();
        $row=new RowComponent();
        $tile=new DivClassComponent("tile col-12");
        $titre=new TitleComponent($this->getObjectName().' : '.$this->object->getId().'');
        $tile->addComponent($titre); 

        $form=new FormComponent($this->makeUrlForForm(),$this->id_form);
        $form->disableEnterOnInput();

        $comp=$this->makeInputsForFields();
        $tile->addComponent($comp);

        $row->addComponent($tile);
        $form->addComponent($row);
        $this->addComponent($form); 
        // $tileComp->addComponent($form);       
        if($this->new==1)
        {   
             $this->addButtonForNewItem();
            
        }
        else
        {
            $this->addButtonForEditedItem();
        }  
    }
    
    protected function afterInitObject() : void
    {
        
    }
    
    protected function afterUpdatedObject() : void
    {
        
    }
    
    protected function isObjectDeletable() : bool
    {
        return true;
    }
    
    protected function loadObjectFromDb() : bool
    {
        if($this->new!=1)
        {         
            if(!$this->object->initById($this->id))
            {
                $sql=Sql::getInstance();
                $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_7")." '".$this->id."' ".LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_8")."<br />".$this->object->getKerror()."<br /><br />".$sql->getQuery()."<br /><br />");
                return false;               
            }   
        }
        return true;
    }
    
    //
    public function reloadObjectFromDb() : bool
    {
        /* @var $this->object KObject */
        if(!is_null($this->object)&&!empty($this->id))
        {         
            if($this->initObject()&&$this->object->initById($this->id))
            {
                return true;
            }
            $sql=Sql::getInstance();
            $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_7")." '".$this->id."' ".LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_8")."<br />".$this->object->getKerror()."<br /><br />".$sql->getQuery()."<br /><br />");
        }
        return false;
    }
    
    protected function deleteObject() : bool
    {       
        $delete=0;
        if(KInput::checkInput(KInput::$INPUT_GET,self::$GET_CHECK_DELETE,KInput::$VARIABLE_INT,$delete)&&$delete==1)
        {
            if(!$this->isObjectDeletable())
            {
                return true;
            }
            
            //let's delete it 
            if($this->object->deleteAll())
            {
                $this->setSuccessMessage(LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_TITLE"),
                        LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_1"));                
            }
            else
            {
                $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_9"));
            }  
            return false;    
        }
        return true;
    }
    
    public function updateObjectInDb() : bool
    {
        $check=0;
//        KDebugger::getInstance()->dump($this->object,"updateObjectInDb 1");
        if(KInput::checkInput(KInput::$INPUT_GET,self::$GET_CHECK_POST,KInput::$VARIABLE_INT,$check)&&$check==1)
        {
//            KDebugger::getInstance()->dump("Before POST","updateObjectInDb 2");
            if($this->object->getAllInputPost($this->trim))
            {
//                KDebugger::getInstance()->dump($this->object,"updateObjectInDb 3");
//                KDebugger::getInstance()->dump($this->object->getFieldValue('is_private'),"updateObjectInDb is_private");
//                KDebugger::getInstance()->dump($this->object->getKField('is_private'),"updateObjectInDbK is_private");
                if($this->object->updateInBd())
                {
                    //$this->object->initById($this->object->getId());
                    $this->setSuccessMessage(
                            LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_TITLE"),
                            LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_2").$this->object->getModificationsInWebString());
                    //$this->reloadObjectFromDb();
                    
                }
                else
                {
                    $this->setErrorMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_9"));
                    return false;
                }
            }
            else
            {
                $stringError=$this->object->getModificationsErrorInWebString();
                if($stringError!="")
                {    
                    $this->setInfoMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_11").$stringError);
                }
                else
                { 
                    $this->setInfoMessage(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_10"));
                }
            }
        }  
        return true;
    }

    public function execute(): bool
    {  
        if(!$this->checkTableName())
        {
            return true;
        }
        $this->makeTitle();
        //KDebugger::getInstance()->dump($this->getTablename(),"Table");
        
        if(!$this->initIdVar())
        {
            return true;
        } 

        if(!$this->initObject())
        {
            return true;
        }   
        
        if(!$this->insertIfDup())
        {
            return true;
        }
        
        if(!$this->insertIfNew())
        {
            return true;
        }

        if(!$this->loadObjectFromDb())
        {
            return true;
        }
        $this->afterInitObject();
        
        
        if(!$this->deleteObject())
        {
            $this->modifyTitleForDeleted();
            return true;
        }
        
   
        if(!$this->updateObjectInDb())
        {
            return true;
        }
        $this->afterUpdatedObject();
        
        $this->makeForm();
        
        return true;
    }
    
    
    protected function makeInput(string $name_input,KField $field, ?string $label=null) : KComponent
    {      
        
        //KDebugger::getInstance()->dump($field, $name_input);
        
        $comp=new HTMLComponent("---makeInput()---");
        
        $readOnly=false;
        if($this->checkIfEditable($field->getName()))
        {
            $readOnly=true;
        }

        // If a label is not specified, we use the name of the field using ternary operator
        $fieldLabel=($label==null)?$field->getName():$label;

        if($field->getType()==KField::$TEXT)
        {           
            $comp=new TextAreaComponent($this->getFieldValue($field),$name_input,$fieldLabel,null,false,$readOnly,2,6);
        }    
        else if($field->getType()==KField::$VARCHAR)
        {
            $fieldVarChar=KField::specializeVarChar($field);
            $comp=new InputStringComponent($this->getFieldValue($field),$name_input,$fieldLabel.' ('.$fieldVarChar->getLength().')',null,false,$readOnly,2,6);
        }  
        else if($field->getType()==KField::$INTEGER &&$field->getForeign_key())
        {      
            $renderFkField=false;
            $className=KObject::makeClassNameFromTableName($field->getForeign_key_table());  
           
            if(class_exists($className))
            {
               // KDebugger::getInstance()->dump(isset($_POST) ? $_POST: 'no',"Post");
                //KDebugger::getInstance()->dump("Exists", $name_input);
                /* @var $this->object KObject */
                $object = new $className();                
                $theField=$object->getFieldForeignKeyLabel();
                
                $dbListFk=new DbList($className);
                $dbListFk->setCache(true);
                
                //KDebugger::getInstance()->dump($dbListFk->getNb(),"NB");
                
                if($dbListFk->getNb()<ShowTableContent::MAX_FK_ITEMS)
                {
                    $list=$dbListFk->getIdAndFieldValue($theField->getName());
                    $arrayValue=array();
                    if($field->getIs_null())
                    {
                        //$html2.='<option value="NULL">NULL</option>';
                        $arrayValue[]=array("NULL","NULL");
                    }

                    if(!is_null($list))
                    {
                        foreach($list as $item)
                        {
                            if(count($item)>=2)
                            {
                                $selected="";
                                if($item[0]==$this->getFieldValue($field))
                                {
                                    $selected=' selected="selected" ';
                                }
                                $arrayValue[]=array($item[0],$item[1]." (->".$item[0].")");
                            }
                        }
                    }
                    
                    //$comp=new DivClassComponent("");
                    $comp= new KSelectComponent($name_input,$arrayValue,[$this->getFieldValue($field)],false,$fieldLabel,$readOnly,2,6);
                    $button=$this->makeButtonForFkLink($name_input, $field);
                    $button->addClassName("col-1");
                    $comp->addComponent($button);               
                    $renderFkField=true;
                }  
            }
            
            if(!$renderFkField)
            {
                $comp=new InputIntegerComponent($this->getFieldValue($field),$name_input,$fieldLabel,null,false,$readOnly,2,6);
                $comp->addComponent($this->makeButtonForFkLink($name_input, $field));             
            }
        }
        else if($field->getType()==KField::$DOUBLE || $field->getType()==KField::$INTEGER || $field->getType()==KField::$FLOAT)
        {
            $comp=new InputFloatDoubleComponent($this->getFieldValue($field),$name_input,$fieldLabel,null,false,$readOnly,2,6);
        }
        else if($field->getType()==KField::$BOOL)
        {
            //KDebugger::getInstance()->dump($this->getFieldValue($field),"CheckBoxComponent_");
            $comp = new CheckBoxComponent($this->getFieldValue($field), $name_input, $fieldLabel,false,$readOnly,3,6);
            $comp->setTypeFlipping();
        }
        
        else if($field->getType() == KField::$GEOMETRY) 
        {
            $centroid="";
            $json ="";
            $json_center="";
            if(!empty($this->getFieldValue($field)))
            {
                $geometry = new KGeometry(KGeometry::WKTToEWKB($this->getFieldValue($field)));
                $centroid = $geometry->getCentroid();
                $json = $geometry->convertToGeoJsonString();             
                if($centroid) 
                {
                    $json_center = $centroid->convertToGeoJsonString();
                }
            }
            
            $comp=new RowColsComponent();
            $comp->addClassName("form-group");
            
            
            $height=400;
            if(ParamManager::getInstance()->get("LEAFLET_HEIGHT"))
            {
                if(intval(ParamManager::getInstance()->get("LEAFLET_HEIGHT"))>0)
                {
                    $height=intval(ParamManager::getInstance()->get("LEAFLET_HEIGHT"));
                }
            }
            $zoom=6;
            if(ParamManager::getInstance()->get("LEAFLET_DEFAULT_ZOOM"))
            {
                if(intval(ParamManager::getInstance()->get("LEAFLET_DEFAULT_ZOOM"))>0)
                {
                    $zoom=intval(ParamManager::getInstance()->get("LEAFLET_DEFAULT_ZOOM"));
                }
            }
            $max_zoom=20;
            if(ParamManager::getInstance()->get("LEAFLET_MAX_ZOOM"))
            {
                if(intval(ParamManager::getInstance()->get("LEAFLET_MAX_ZOOM"))>0)
                {
                    $max_zoom=intval(ParamManager::getInstance()->get("LEAFLET_MAX_ZOOM"));
                }
            }             
            
            $center_x="37.39";
            $center_y="25.26";
            if(ParamManager::getInstance()->get("LEAFLET_DEFAULT_CENTER_X")
                    &&ParamManager::getInstance()->get("LEAFLET_DEFAULT_CENTER_Y"))
            {
                $tmp_center_x=floatval(ParamManager::getInstance()->get("LEAFLET_DEFAULT_CENTER_X"));
                $tmp_center_y=floatval(ParamManager::getInstance()->get("LEAFLET_DEFAULT_CENTER_Y"));
                if($tmp_center_x!=0&&$tmp_center_y!=0)
                {
                    $center_x=$tmp_center_x;
                    $center_y=$tmp_center_y;
                }
            } 
                      
            
            $comp->addColComponent(new HTMLComponent('<label for="mapTopo">' . KhtmlEntities($fieldLabel) . '</label>'),"-2 label_form");
            $map=new KLeafLetMapDraw(
                        "mapTopo",
                        $name_input,
                        $json,
                        $json_center,
                        "".$height,
                        "".$center_x,
                        "".$center_y,
                        "".$zoom,
                        "".$max_zoom,  
                        null,
                        null,
                        null,
                        false,
                        $this->mapBounds
                        );  
            $comp->addColComponent($map,"-6");

        }
        else
        {
            $comp=new InputStringComponent($this->getFieldValue($field),$name_input,$fieldLabel,null,false,$readOnly,2,6);
        }
        
        if(array_key_exists($field->getName(),$this->complimentaryFields))
        {
            //KDebugger::getInstance()->dump($this->complimentaryFields[$field->getName()],$field->getName());
            //KDebugger::getInstance()->dump($this->complimentaryFields[$field->getName()]);
            $comp->addComponent($this->complimentaryFields[$field->getName()]);    
        }
        
        return $comp;
    }
    
    private function makeButtonForFkLink(string $name_input, KField $field) : ButtonComponent
    {
       $url_fk=KRoute::makeKURL(
            $this->url_edit_fk_table_element, 
            [ShowAllTables::$GET_PARAM_TABLENAME=>$field->getForeign_key_table(),KObject::$ID=>""]);              
        return $this->makeButtonForURL($name_input,$url_fk->printURLWithoutAmp(),ButtonComponent::$TYPE_INFO);
    }
    
    protected function makeButtonForURL(string $name_input,string $url,?string $type=null,string $ico="fa fa-lg fa-share") : ButtonComponent
    {       
        $button=new ButtonComponent("",$type,$ico);
        $button->setClickAction($this->setFunctionNameJsForSelectFkId($name_input));
        $button->addJSText($this->setClickJsForSelectFkId($name_input,$url));
        return $button;
    }
    
    private function setClickJsForSelectFkId(string $name_input,string $url) : string
    {
        return '
function '.$this->setFunctionNameJsForSelectFkId($name_input).'
{
    let id=$("#'.$name_input.'").val();
    if(id!=null)
    {    
        window.open("'.$url.'"+id, "_blank");
    }
}
';
    }
    
    private function setFunctionNameJsForSelectFkId(string $name_input) : string
    {
        return "clickFkIdSelect_".$name_input."()";
    }    
    
    private function getFieldValue(KField $field) : mixed
    {
        if($this->new==1)
        {
            return $field->getDefault();
        }
        else
        {
            return $field->get();
        }
    }
    
    private function checkIfEditable(mixed $name) : bool
    {
        return in_array ($name , $this->fieldsReadOnly);
    }
  
    protected function setTrimFields(bool $trim) : void
    {
        $this->trim=$trim;
    }
    
    protected function setDefaultMapBounds(string $geom) : void
    {
        $coordinates = KGeometry::wktToCoordinates($geom);
        $this->mapBounds=json_encode($coordinates);      
        
    }
    
    protected function setErrorMessage(string $message) : void
    {
        $this->addComponent(
            new KAlertComponent(
                LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                $message,
                KAlertComponent::$TYPE_ERROR));         
    }
    
    protected function setInfoMessage(string $message) : void
    {
        $this->addComponent(
            new KAlertComponent(
                    LanguageManager::_("SHOW_EDIT_TABLE_NO_MODIFY_TITLE"),
                    $message,
                    KAlertComponent::$TYPE_INFO)); 
    }
    
    protected function setSuccessMessage(string $title, string $message): void
    {
        $this->addComponent(
                new KAlertComponent(
                    $title,
                    $message,
                    KAlertComponent::$TYPE_SUCCESS));         
    }
}

