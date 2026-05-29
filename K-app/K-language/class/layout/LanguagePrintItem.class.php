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
class LanguagePrintItem extends KComponent
{
    /**
     * 
     * @param string $itemName
     * @param array<string,string> $itemsTranslations
     */
    function __construct(string $itemName, array $itemsTranslations)
    {
        parent::__construct();
        
        $row1= new RowComponent();
        $row1->addComponent(new TitleComponent($itemName,false,4));
        $row1->addComponent(new ButtonComponent(LanguageManager::_("GA_OPE_MODIFY")));//,false,2));
        
        $row2= new RowColsComponent();
        foreach ($itemsTranslations as $key => $value)
        {
            $row2->addColComponent(new HTMLComponent($value));   
        }
        
        $this->addComponent($row1);
        $this->addComponent($row2);
        $this->addComponent(new HrComponent());
    }
}