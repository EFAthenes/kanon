<?php

/**
 * Description of KFormGroupComponent
 *
 * @author Mateo
 */
class KFormGroupComponent extends PrivilegesUtilsKComponent
{
    /**
     * 
     * @param string $url_back
     * @param array<string,mixed>|null $id
     */
    function __construct(string $url_back,?array $id=[])
    {
        $url=KRoute::makeURL($url_back,$id);
        parent::__construct($url,'form_edit_group');
        $this->setNone();
    }

    function initForm(Kapp_Groups $groupe) : void
    {
        $row=new RowComponent();
        $this->addComponent($row);
        $component=new DivClassComponent("col-12");
        $row->addComponent($component);
        $tile=new TileComponent();
        $component->addComponent($tile);

        $this->fieldsReadOnly=[Kapp_Groups::$ID,Kapp_Groups::$DATE_CREATED,Kapp_Groups::$DATE_MODIFIED];
        $groupe->initKFields();
        $tile->addComponent($this->makeInput("",$groupe->getInputValueFieldName(Kapp_Groups::$ID),"".$groupe->getId(),LanguageManager::_("KAPP_GROUP_LABEL_ID"),true));
        $tile->addComponent($this->makeInput("",$groupe->getInputValueFieldName(Kapp_Groups::$LABEL),$groupe->getLabel(),LanguageManager::_("KAPP_GROUP_LABEL_LABEL"),false));
        $tile->addComponent(
                new JoditEditorComponent(
                        $groupe->getInputValueFieldName(Kapp_Groups::$DESCRIPTION), 
                        $groupe->getDescription(), 
                        LanguageManager::_("KAPP_GROUP_LABEL_DESCRIPTION"),
                        false,3,6));
                //$this->makeInput("",$groupe->getInputValueFieldName(Kapp_Groups::$DESCRIPTION),$groupe->getDescription(),LanguageManager::_("KAPP_GROUP_LABEL_DESCRIPTION"),false));
        $tile->addComponent($this->makeInput("","",$groupe->getDate_created(),LanguageManager::_("KAPP_GROUP_LABEL_CREA"),true));
        $tile->addComponent($this->makeInput("","",$groupe->getDate_modified(),LanguageManager::_("KAPP_GROUP_LABEL_MODIF"),true));
    }

    protected function makeInput(string $idName,string $name_input,mixed $field_value,string $field_label,bool $readonly=false,int $colPos=3,int $colLength=6): KComponent
    {
        $comp=new InputStringComponent($field_value,$name_input,$field_label,"",false,$readonly,3,6);
        $comp->setAutocomplete(false);
        return $comp;
    }

}