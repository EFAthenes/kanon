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
 * Description of KShowAllUsersConnections
 *
 * @author Louis Mulot
 */
class KShowAllUsersConnections extends KController
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

    public function init(): void
    {
        $this->url_modify_table=RoutesItems::$KEDIT_USERS;

        $title=new KTitleLayoutAdmin(LanguageManager::_("KAPP_CONNECTIONS_LIST_TITLE"),"fa-solid fa-user-clock");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER,$title);
        $this->setTitle($title);
        $title->setKurl(KRoute::makeKURL(RoutesItems::$KLIST_CONNECTIONS));

        $buttonListUsers= new KTitleButton(LanguageManager::_("KAPP_USER_LIST_TITLE"), KTitleButton::$TYPE_INFO,"fa fa-address-card ");
        $buttonListUsers->setActionRouteItem(KRoutesItems::$KSHOW_USERS);      
        $title->addKTitleButton($buttonListUsers);
    }

    public function execute(): bool
    {
        
        $dbUsers= new DbList(Kapp_Users::class);
        $usersId=$dbUsers->getAllById();
        
        
        $db= new DbList(Kapp_Users_Connections::class);
        $all=$db->getAll();
        $lines=[];
        /* @var $connection Kapp_Users_Connections */
        foreach ($all as $connection)
        {
            $label_user=$connection->getFk_id_kapp_users();
            $user=$usersId->get($connection->getFk_id_kapp_users());
            /* @var  $user Kapp_Users */
            if(!is_null($user))
            {
                $label_user=$user->getEmail();
            }
            $line=[$connection->getDate_created(),$label_user,$connection->getIp()];
            $lines[]=$line;
        }
        
        $columns=["Date","User","Ip"];      
        $tab= new DataTableSimpleTableComponent("param_tab", $columns,$lines);
        $tab->setOrderByColumn(0,true);
        $tile1=new TileComponent();
        $tile1->addComponent(new TitleComponent(LanguageManager::_("KAPP_CONNECTIONS_LIST_TITLE_TILE"), true));
        $tile1->addComponent($tab);
        
        $this->addComponent($tile1);
        
        
        return true;
    }

    public function getUrl_modify_table(): string
    {
        return $this->url_modify_table;
    }

}