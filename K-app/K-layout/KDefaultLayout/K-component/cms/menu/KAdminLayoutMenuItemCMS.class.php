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
class KAdminLayoutMenuItemCMS extends KAdminLayoutLeftMenuItem
{
    function __construct()
    { 
        parent::__construct(Ki18::_("KAPP_MENU_CMS"),"fa-solid fa-gears");
        $this->addMenuItem(new KAdminLayoutLeftMenuItem("CMS","fa-solid fa-pencil",KRoute::makeURL(KRoutesItems::$KCMS_HOME)));
        $this->addMenuItem(new KAdminLayoutLeftMenuItem("Template","fa-solid fa-sliders",KRoute::makeURL(KRoutesItems::$KTEMPLATE_PAGE)));
        $this->addMenuItem(new KAdminLayoutLeftMenuItem("Language","fa-solid fa-language",KRoute::makeURL(KRoutesItems::$KCHANGE_LANGUAGE_ITEM)));
    }
}
