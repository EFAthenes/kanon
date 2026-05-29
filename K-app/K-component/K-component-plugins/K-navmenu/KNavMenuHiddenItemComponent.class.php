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
/**
 * Description of KNavMenuHiddenItemComponent
 *
 * @author Mulot Louis
 */
class KNavMenuHiddenItemComponent extends KComponent
{
    private string $text="";
    private string $link= "";
    private string $icon= "";
    private ?ArrayList $listOfItems=null;
    
    function __construct(string $text,string $link="",string $icon="")
    {
        parent::__construct();
        $this->setName("");
        $this->setNone();
        $this->listOfItems=new ArrayList();
        $this->text=$text;
        $this->link=$link;
        $this->icon=$icon;
        KTitleNavManager::getInstance()->compareUrlHidden($this);
    }
    function getText() : string
    {
        return $this->text;
    }

    function getIcon() : string
    {
        return $this->icon;
    }

    function getLink() : string
    {
        return $this->link;
    }

    function addItem(KNavMenuHiddenItemComponent $item) : void
    {
        $this->listOfItems->add($item);
    }    
}