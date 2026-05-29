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
class KParamManagerViewer extends KController
{
    public function execute(): bool
    {
        KApp::getInstance()->getLayout()->setTitle("Params Viewer");
        $title = new KTitleLayoutAdmin("Params Viewer", "fa-solid fa-sliders");
        KApp::getInstance()->getLayout()->addComponent(KAdminLayout::$HEADER, $title);   
        
        $tile1=new TileComponent();
        
        $map=ParamManager::getInstance()->getMap();        
        $lines=[];
        if(!is_null($map))
        {
            foreach ($map as $key => $val)
            {
                $line=[$key,$val];
                $lines[]=$line;
            }
        }
        $columns=["Key","Value"];      
        $tab= new DataTableSimpleTableComponent("param_tab", $columns,$lines);
        $tile1->addComponent(new TitleComponent("Dynamic", true));
        $tile1->addComponent($tab);
        
        
        $map2=ParamManager::getInstance()->getAttributes();
        $lines2=[];
        foreach ($map2 as $key2 => $val2)
        {
            $line2=[$key2,$val2];
            $lines2[]=$line2;
        }
               
        
        $tab2= new DataTableSimpleTableComponent("param_tab2", $columns,$lines2);
        $tile1->addComponent(new TitleComponent("Static", true));
        $tile1->addComponent($tab2);        

        $this->addComponent($tile1);                
        return true;
    }
}