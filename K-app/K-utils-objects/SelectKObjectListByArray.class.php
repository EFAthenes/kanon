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
//class SelectKObjectListByArray
//{
//    private $className=null;  
//    private $arrayId=null;
//    private $arrayValue=null;
//    
//    public function __construct($className,$id_field,$value_field,$order="",$separator=" ")
//    {
//        if(!is_array($value_field)&&count($value_field)>0)
//        {
//            return;
//        }
//        if($order=="")
//        {
//            $order="ORDER BY ".$value_field[0]." ASC";
//        }
//        $this->className=$className;
//        $dDList=new DbList($className);       
//        $list=$dDList->getByArray(null,$order);  
//        
//        $method_id="get".ucfirst($id_field); 
//        $method_value="get".ucfirst($value_field[0]);
//        //echo $method_value;
//        
//        for ($j = 0; $j < $list->getSize(); $j++)
//        {            
//            $object=$list->get($j);
//            if($object!=null)
//            {
//                $this->arrayId[]=$object->$method_id();
//                
//                $string="";
//                for ($k = 0; $k < count($value_field); $k++)
//                {
//                    if($k>0)
//                    {
//                        $string.=$separator;
//                    }
//                    $method_value="get".ucfirst($value_field[$k]);
//                    $string.=$object->$method_value();
//                }
//                $this->arrayValue[]=$string;
//            }
//        }
//    }
//    
//    public function makeOptions($value)
//    {
//        $html="";
//        for ($j = 0; $j < count($this->arrayId); $j++)
//        {
//           $html.='<option value="'.convertDoubleQuotes($this->arrayId[$j]).'">'.$this->arrayValue[$j].'</option>'; 
//        }
//        return initSelect($html, $value);
//    }
//    public function makeOptionsMultiple($value="")
//    {
//        $html="";
//        for ($j = 0; $j < count($this->arrayId); $j++)
//        {
//           $html.='<option value="'.convertDoubleQuotes($this->arrayId[$j]).'">'.$this->arrayValue[$j].'</option>'; 
//        }
//        return initMultipleSelect($html, $value);
//    }    
//    
//    
//    public function __destruct()
//    {
//    }
//}
