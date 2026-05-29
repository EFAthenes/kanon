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
class ShowListOfTablesNames extends KComponent
{
    public function __construct()
    {        
        parent::__construct();

        $grid2=new TileGridComponent();
        $arrayColumn=Sql::getInstance()->getAllTablesNamesInArray();
        if(count($arrayColumn))
        {
            $grid2->addColComponent(new TitleComponent(LanguageManager::_("SHOW_ALL_TABLES_IN_DB_TITLE")));
            $arrayData=[];
            $i=1;
            /* @var $sqlField SqlField */
            foreach ($arrayColumn as $tablename)
            {
                $arrayline=[];
                $arrayline[]=$i;
                $arrayline[]=new HTMLComponent("<b>".$tablename."</b>");
                $arrayData[]=$arrayline;
                $i++;
            }
            $arrayCol=["Nb","Name"];
            $grid2->addColComponent((new DataTableSimpleTableComponent("struct_table",$arrayCol,$arrayData))->setDefaultNumberOfResults(50)->setButton_bar(true));
            
            $this->addComponent($grid2);
        }        
    }
}
