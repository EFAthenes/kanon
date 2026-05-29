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
 * Description of ShowAllGroups
 *
 * @author Mateo
 * @author Hippolyte
 */
class KShowAllGroups extends UtilsShowAll
{

    public function init(): void
    {
        $title=new KTitleLayoutAdmin(LanguageManager::_("KAPP_GROUP_LIST_TITLE"),"fa fa-users ");
        $title->setKurl(KRoute::makeKURL(RoutesItems::$KSHOW_GROUPS));
        $this->url_modify_table=RoutesItems::$KEDIT_GROUPS;
        $this->setTitle($title);
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
    }

    public function execute(): bool
    {
        $tablename=Kapp_Groups::$TABLE_NAME;
        $class_name=KObject::makeClassNameFromTableName($tablename);
        
        if(empty($class_name) || !class_exists($class_name))
        {
            $this->displayErrorMessageTableNotRecognized($tablename);
            return true;            
        }
        
        $check_delete=0;
        KApp::getInstance()->getLayout()->setTitle($this->getTitle()->getTitle()." ".$tablename);

        if(KInput::checkInput(KInput::$INPUT_GET,KEditTableGroups::$ACTION_DELETE,KInput::$VARIABLE_INT,$check_delete))
        {
            $this->displayDeleteAlertComponent($check_delete);
        }
        $dbList=new DbList($class_name);
        $list_groups=$dbList->getAll();
        $urlCreateGroup=KRoute::makeURL(RoutesItems::$KEDIT_GROUPS,[KEditTableGroups::$GET_NEW=>"1"]);

        if($dbList->getNb()==0)
        {
            $this->displayErrorMessageEmptyTable($tablename);
            $this->addButtonAddOnTitleBar($urlCreateGroup);
            return true;
        }
        $kobject=new $class_name();

        $arrayLines=[];
        $arrayTablesDb_fields_name=$kobject->fieldsLabelToArray();
        $tableSize=count($arrayTablesDb_fields_name);
        $arrayTablesDb_fields_name[]=LanguageManager::_("KAPP_GROUP_COL_DELETE_TITLE");
        foreach($list_groups as $group)
        {
            $id=$group->getId();
            $label=$group->getLabel();
            $description=$group->getDescription();
            $date_created=$group->getDate_created();
            $date_modified=$group->getDate_modified();

            $dbUserGroup=new DbList(Klink_Kapp_Users_Groups::class);
            $nb=$dbUserGroup->getNb([QueryField::m(Klink_Kapp_Users_Groups::$FK_ID_KAPP_GROUPS, $id)]);
            $button=null;
            if($nb==0)
            {
                $button=new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_DELETE"),ButtonComponent::$TYPE_DANGER,"fa fa-trash");
                $button->setClickAction("addJSActionButtonDelete(".$id.")");
            }
            else
            {
                $button=new BadgeComponent("".$nb." utilisateur(s)!");
            }
            $arrayLines[]=[$id,$label,$description,$date_created,$date_modified,$button];
        }
        $table=new DataTableSimpleTableComponent('Table_'.$tablename,$arrayTablesDb_fields_name,$arrayLines);
        $table->setButton_bar(true);
        $table->setColumnsNotVisible([$tableSize-2,$tableSize-1]);
        $table->setComponentForColumn("Id",new LinkDataTableComponent(
                        KRoute::makeURL($this->url_modify_table,[Kapp_Groups::$ID=>""]),"",LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")));
        $grid=new TileGridComponent();
        $grid->addColComponent($table,"-12");
        $this->addComponent($grid);
        $this->addString($this->addJSActionButtonDelete(RoutesItems::$KMANAGE_GROUPS,RoutesItems::$KSHOW_GROUPS,KEditTableGroups::$ACTION_DELETE));

        $this->addButtonAddOnTitleBar($urlCreateGroup);
        return true;
    }

}