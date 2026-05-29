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
class KCMS_Edit_Element extends ShowEditTableElement
{
    public function init() : void
    {
        parent::init();
//        $this->setUrl_back_button(RoutesItems::$ENTITE_A_MODIFIER);
//        $this->setUrl_edit_fk_table_element(RoutesItems::$ELEMENT_ENTITE_MODIFIER);
        $this->setUrl_back_button(RoutesItems::$KCMS_SHOW_ELEMENTS);
        $this->setTrimFields(true);
    }
}