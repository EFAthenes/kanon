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
//
//declare(strict_types=1);
//class SelectObjectListWithRank
//{
//    private ?array $arrayId=null;
//    private ?array $arrayValue=null;
//    
//    public function __construct(string $className,string $id_field,mixed $value_field,int $rank_field,string $father_field,string $order_field)
//    {
//
//        $this->arrayId=array();
//        $this->arrayValue=array();
//        $order="ORDER BY ".$value_field." ASC";
//        $this->getSubLevel($className,$id_field,$value_field,$rank_field,$father_field,0,$order_field,$order, 0);
//        /*
//        $dDList=new DbList($this->className);       
//        $list=$dDList->getByArray($arrayCondition,$order);  
//        
//        $method_id="get".ucfirst($id_field);
//        $method_value="get".ucfirst($value_field);
//               
//        for ($j = 0; $j < $list->getSize(); $j++)
//        {            
//            $object=$list->get($j);
//            if($object!=null)
//            {
//                $this->arrayId[]=$object->$method_id();                
//                $this->arrayValue[]=$object->$method_value();
//            }
//        }
//         * 
//         */
//    }
//    
//    private function getSubLevel($className,$id_field,$value_field,$rank_field,$father_field,$father_value,$order_field,$order,$rankValue)
//    {       
//        $arrayCondition=array(new QueryField($rank_field, $rankValue, QueryField::$EQUAL),new QueryField($father_field, $father_value, QueryField::$EQUAL));
//        $dDList=new DbList($className);       
//        $list=$dDList->getByArray($arrayCondition,$order);  
//        
//        $method_id="get".ucfirst($id_field);
//        $method_value="get".ucfirst($value_field);
//               
//        for ($j = 0; $j < $list->getSize(); $j++)
//        {            
//            $object=$list->get($j);
//            if($object!=null)
//            {
//                $this->arrayId[]=$object->$method_id();                
//                $this->arrayValue[]=$this->makeRankDiv($rankValue).$object->$method_value();
//                
//                $newArray = array();
//                foreach ($arrayCondition as $k => $v) 
//                {
//                    $newArray[$k] = clone $v;
//                }
//                $newOrder="ORDER BY ".$order_field." ASC";
//                $this->getSubLevel($className,$id_field,$value_field,$rank_field,$father_field,$object->$method_id(),$newOrder,$order, $rankValue+1);
//            }
//        }
//    }
//    
//    private function makeRankDiv($rank)
//    {
//        $string="";
//        for ($i = 0; $i < $rank; $i++)
//        {
//            $string.="-";
//        }
//        return $string;
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
//    public function addFirstOption($id,$value)
//    {      
//        array_unshift($this->arrayId,$id);
//        array_unshift($this->arrayValue,$value);
//    }
//
//    public function __destruct()
//    {
//    }
//}
