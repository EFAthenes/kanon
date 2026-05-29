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
//declare(strict_types=1);
//class SelectKObjectFieldEnumList
//{
//    private $list=null;
//    private $sql=null;
//    public function __construct($className,$value_field)
//    {
//        $this->list=new ArrayList(); 
//        $this->sql=new Sql();
//        $this->sql->connect_DB();
//        $req = $this->sql->request_SQL("SHOW COLUMNS FROM `".$className."` WHERE Field = '".$value_field."'");
//        if(($result=$this->sql->fetch_array($req))!=NULL)
//        {
//            $enum=str_first_replace("enum(", "",$result["Type"]);
//            $enum=str_last_replace(")", "", $enum);  
//            $enum=str_replace('\'','', $enum); 
//            $array=explode(",", $enum);
//            for ($i = 0; $i < count($array); $i++)
//            {
//                $this->list->add($array[$i]);                
//            }
//        }
//        $this->sql->disconnect_DB();    
//    }
//    
//    public function makeOptions($value)
//    {
//        $html="";
//        for ($j = 0; $j < $this->list->getSize(); $j++)
//        {
//           $html.='<option value="'.convertDoubleQuotes($this->list->get($j)).'">'.$this->list->get($j).'</option>'; 
//        }
//        return initSelect($html, $value);
//    }
//    public function makeOptionsMultiple($value="")
//    {
//        $html="";
//        for ($j = 0; $j < $this->list->getSize(); $j++)
//        {
//           $html.='<option value="'.convertDoubleQuotes($this->list->get($j)).'">'.$this->list->get($j).'</option>'; 
//        }
//        return initMultipleSelect($html, $value);
//    } 
// 
//    public function __destruct()
//    {
//    }
//}
