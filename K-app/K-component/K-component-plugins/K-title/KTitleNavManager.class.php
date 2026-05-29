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
class KTitleNavManager
{
    private static ?KTitleNavManager $instance=null;

    public string $title="";
    public string $icon="";
    public string $url="";  
    private bool $has_right=false;

    private function __construct()
    {
        $url=new KURL();
        $this->url=$url->getLastNameInPath().$url->getArgsInString();
    }
    public  function  __destruct()
    {

    }
    public function compareUrl(KNavMenuItemComponent $item) : void
    {    
        //echo $this->url."//".$item->getLink()."<br />";
        if($item->getLink()!=""&&strpos($this->url,$item->getLink())===0)
        {
            $this->title=$item->getText();
            $this->icon=$item->getIcon();
            $this->has_right=true;
            //echo "OK <br />";
        }
    }
     public function compareUrlHidden(KNavMenuHiddenItemComponent $item) : void
    {
        //echo $this->url."//".$item->getLink()."<br />";
        if($item->getLink()!=""&&strpos($this->url,$item->getLink())===0)
        {
            $this->title=$item->getText();
            $this->icon=$item->getIcon();
            $this->has_right=true;
            //echo "OK <br />";
        }
    }
   
    public static function getInstance() : KTitleNavManager
    {
        if(KTitleNavManager::$instance==null)
        {
            KTitleNavManager::$instance=new KTitleNavManager();            
        }
        return KTitleNavManager::$instance;
    }  
    
    function getTitle() : string
    {
        return $this->title;
    }

    function getIcon() : string
    {
        return $this->icon;
    }
    function getHas_right() : bool
    {
        return $this->has_right;
    }
}
