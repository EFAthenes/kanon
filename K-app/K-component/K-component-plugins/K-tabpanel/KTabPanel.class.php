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
 * Description of KTabPanelItem
 *
 * @author Mulot Louis
 */
class KTabPanelItem extends KComponent
{
    private string $title="";
    function __construct(string $title,KComponent $component)
    {
        parent::__construct();
        $this->setTitle($title);
        $this->addComponent($component);
    }
    function  __destruct()
    {

    }
    function getTitle() : string
    {
        return $this->title;
    }
    function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
