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
 * Description of ShowAllUsers
 *
 * @author Mateo
 */
class KShowAllUsers extends KController
{
    private ?KTitleLayoutAdmin $title=null;
    private string $url_modify_table="";

    public function getTitle(): ?KTitleLayoutAdmin
    {
        return $this->title;
    }

    public function setTitle(KTitleLayoutAdmin $title): void
    {
        $this->title=$title;
    }

    function setUrl_modify_table(string $url_modify_table) : void
    {
        $this->url_modify_table=$url_modify_table;
    }

    function getUrl_modify_table() : string
    {
        return $this->url_modify_table;
    }

    public function init(): void
    {
        $this->url_modify_table=RoutesItems::$KEDIT_USERS;

        $title=new KTitleLayoutAdmin(LanguageManager::_("KAPP_USER_LIST_TITLE"),"fa fa-address-card ");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        $this->setTitle($title);
        $title->setKurl(KRoute::makeKURL(RoutesItems::$KSHOW_USERS));

        $buttonConnections= new KTitleButton(LanguageManager::_("KAPP_CONNECTIONS_LIST_TITLE"), KTitleButton::$TYPE_INFO,"fa-solid fa-user-clock");
        $buttonConnections->setActionRouteItem(KRoutesItems::$KLIST_CONNECTIONS);      
        $title->addKTitleButton($buttonConnections);
    }

    public function execute(): bool
    {
        $tablename=Kapp_Users::$TABLE_NAME;

        KApp::getInstance()->getLayout()->setTitle($this->getTitle()->getTitle()." ".$tablename);

        $class_name=KObject::makeClassNameFromTableName($tablename);
        
        if(empty($class_name)|| !class_exists($class_name))
        {            
            $grid=new TileGridComponent();
            $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_3").' : '.$tablename.''),"-12");
            $this->addComponent($grid);
            return true;
        }

        $check_delete=0;
        if(KInput::checkInput(KInput::$INPUT_GET,KEditTableUsers::$ACTION_DELETE,KInput::$VARIABLE_INT,$check_delete))
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

        $check_reset=0;
        if(KInput::checkInput(KInput::$INPUT_GET,KEditTableUsers::$ACTION_RESET_PWD,KInput::$VARIABLE_INT,$check_reset))
        {
            if($check_reset==1)
            {
                $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_MODIFY_TITLE"),
                                LanguageManager::_("KAPP_USER_MODIFICATIONS_PASS_OK_MSG")));
            }
            else if($check_reset==2)
            {
                $this->addComponent(new KAlertComponent(LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                                LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_9"),
                                KAlertComponent::$TYPE_ERROR));
            }
        }
        
        $check_imper=0;
        if(KInput::checkInput(KInput::$INPUT_GET,KEditTableUsers::$ACTION_IMPERSONATE,KInput::$VARIABLE_INT,$check_imper))
        {
            if($check_imper==2)
            {
                $this->addComponent(
                        new KAlertComponent(
                                LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_TITLE"),
                                LanguageManager::_("SHOW_EDIT_TABLE_ELEMENT_ERROR_9"),
                                KAlertComponent::$TYPE_ERROR));
            }
        }        

        $dbList=new DbList($class_name);

        $check_groupe=0;
        if(KInput::checkInput(KInput::$INPUT_GET,KEditTableUsers::$CHECK_GROUPE,KInput::$VARIABLE_INT,$check_groupe)&&$check_groupe!=0)
        {
            $klink_list=new DbList(Klink_Kapp_Users_Groups::class);
            $filtrage_grp=new QueryField(Klink_Kapp_Users_Groups::$FK_ID_KAPP_GROUPS,$check_groupe);
            $klist_users=$klink_list->getByArray([$filtrage_grp]);
            if($klist_users->getCount()!=0)
            {
                $users=[];
                foreach($klist_users as $user)
                {
                    $users[]=new QueryField(Kapp_Users::$ID,$user->getFk_id_kapp_users());
                }
                $list_users=$dbList->getByArray([$users]);
            }
            else
            {
                $list_users=[];
            }
        }
        else
        {
            $list_users=$dbList->getAll();
        }

        $max_results=$dbList->getNb();
        $urlCreateUser=KRoute::makeURL(RoutesItems::$KEDIT_USERS,[KEditTableUsers::$GET_NEW=>"1"]);
        if($max_results==0)
        {
            $grid=new TileGridComponent();
            $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_4").' : '.$tablename.'//'.KObject::makeClassNameFromTableName($tablename)),"-12");
            $this->addComponent($grid);

            $addButton=new KTitleButton(LanguageManager::_("SHOW_TABLE_CONTENT_BUTTON_ADD"),KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
            $addButton->setActionURL($urlCreateUser);
            $this->getTitle()->addKTitleButton($addButton);
            return true;
        }

        $kobject=new $class_name();

        $arrayTablesDb_fields_name=$kobject->fieldsLabelToArray();
        $tableSize=count($arrayTablesDb_fields_name);

        $arrayTablesDb_fields_name[]=LanguageManager::_("KAPP_USER_COL_GROUPS_TITLE");
        $arrayTablesDb_fields_name[]=LanguageManager::_("KAPP_USER_COL_DELETE_TITLE");
        $arrayTablesDb_fields_name[]=LanguageManager::_("KAPP_USER_COL_RESET_TITLE");

        $arrayLines=[];

        foreach($list_users as $user)
        {
            $id=$user->getId();
            $email=$user->getEmail();
            $password=$user->getPassword();
            $firstname=$user->getFirst_name();
            $lastname=$user->getLast_name();
            $language=$user->getLanguage();
            $status=$user->getStatus();
            $timezone=$user->getTimeZone();
            $date_created=$user->getDate_created();
            $date_modified=$user->getDate_modified();
            $groups=$this->getUserGroups($id);

            $delete=new ButtonComponent(LanguageManager::_("SHOW_EDIT_TABLE_BUTTON_DELETE"),ButtonComponent::$TYPE_DANGER,"fa fa-trash");
            $delete->setClickAction("addJSActionButton(".$id.", '".KEditTableUsers::$ACTION_DELETE."')");

            $reset=new ButtonComponent(LanguageManager::_("KAPP_USER_BTN_RESET_TITLE"),ButtonComponent::$TYPE_WARNING,"fa fa-key");
            $reset->setClickAction("addJSActionButton(".$id.", '".KEditTableUsers::$ACTION_RESET_PWD."')");

            $arrayLines[]=[$id,$email,$password,$firstname,$lastname,$language,$status,$timezone,$date_created,$date_modified,$groups,$delete,$reset];
        }
        $table=new DataTableSimpleTableComponent('Table_'.$tablename,$arrayTablesDb_fields_name,$arrayLines);
        $table->setButton_bar(true);

        $table->setColumnsNotVisible([$tableSize-8,$tableSize-2,$tableSize-1]);

        $table->setComponentForColumn("Id",new LinkDataTableComponent(
                        KRoute::makeURL($this->url_modify_table,[Kapp_Users::$ID=>""]),"",LanguageManager::_("SHOW_TABLE_CONTENT_LINK_TOOLTIP")));

        $grid=new TileGridComponent();
        $col=new ColComponent("-xl-4 col-lg-5 col-md-6 col-sm-8 col-xs-12");
        $col->addComponent(new KSelectComponent("filtre_groupes",$this->listGroups(),[$check_groupe],false,LanguageManager::_("KAPP_USER_COL_GROUPS_TITLE"),false,2,10));
        $grid->addColComponent($col);
        $grid->addColComponent($table,"-12");
        $this->addComponent($grid);

        $this->addString($this->addScriptGroupList());
        $this->addString(KEditTableUsers::addJSActionButton());
        
//        else
//        {
//            $grid=new TileGridComponent();
//            $grid->addColComponent(new TitleComponent(LanguageManager::_("SHOW_TABLE_CONTENT_ERROR_MSG_4").' : '.$tablename.''),"-12");
//            $this->addComponent($grid);
//        }

        $addButton=new KTitleButton(LanguageManager::_("SHOW_TABLE_CONTENT_BUTTON_ADD"),KTitleButton::$TYPE_PRIMARY,"fa fa-plus-square");
        $addButton->setActionURL($urlCreateUser);
        $this->getTitle()->addKTitleButton($addButton);

        return true;
    }

    private function getUserGroups(int $id): HTMLComponent
    {
        $groups_list=new DbList(Kapp_Groups::class);

        $user=new Kapp_Users();
        $user->initById($id);
        $groupes=$user->getGroupsId();

        $stringHTML=new HTMLComponent("<p>");
        foreach($groupes as $groupe)
        {
            $label=$groups_list->getDistinctField(Kapp_Groups::$LABEL,[new QueryField(Kapp_Groups::$ID,$groupe)]);
            $stringHTML->addHtmlComponent("".$groupe." - ".$label->getLast()."");
            $stringHTML->addHtmlComponent("<br />");
        }
        $stringHTML->addHtmlComponent("</p>");

        return $stringHTML;
    }

    /**
     * 
     * @return array<int,array<int,string>>
     */
    private function listGroups(): array
    {
        $dblist=new DbList(Kapp_Groups::class);
        $groupes=$dblist->getAll();
        $array=[];

        $array[]=[0,"Tous les groupes"];
        foreach($groupes as $groupe)
        {
            $array[]=[$groupe->getId(),$groupe->getLabel()];
        }
        return $array;
    }

    private function addScriptGroupList() : string
    {
        $script='
<script>
$(document).ready(function(){ 
    $("#filtre_groupes").on("select2:select", function(){
        window.location.href = "'.KRoute::makeURL(RoutesItems::$KSHOW_USERS).'&'.KEditTableUsers::$CHECK_GROUPE.'="+$("#filtre_groupes").val();
    });
});
</script>
                ';

        return $script;
    }

}