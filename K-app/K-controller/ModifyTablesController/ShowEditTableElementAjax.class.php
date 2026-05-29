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
class ShowEditTableElementAjax extends KController
{
    private ?ArrayList $field_names = null;
    private ?HashMap $fields = null;
    private string $PARAM_DRAW = "draw";
    public static string $PARAM_DB_FIELDS = "db_fields";
    public static string $PARAM_CLASS_NAME = "class_name";
    public static string $PARAM_MAX_RESULT = "max_result";
     public static string $PARAM_ADDITIONNAL = "param_add";
    // Used for modifications history
    public static string $PARAM_USER_ID = "user";
    public static string $PARAM_DATE_START = "debut";
    public static string $PARAM_DATE_STOP = "fin";
    public static string $PARAM_MODIF_TYPE = "modif";
    public static string $PARAM_CIBLE = "cible";
    private string $PARAM_START = "start";
    private string $PARAM_LENGTH = "length";
    private string $PARAM_SEARCH = "search";
    private string $PARAM_SEARCH_VALUE = "value";
    private bool $debug = false;
    private string $debug_string="";
    private ?KObject $object= null;
    private int $draw=0;
    private int $max_results=0;
    /**
     * 
     * @var array<string,array<int,mixed>>
     */
    private array $listFkFields=[];

    
    private function getValueField(KObject $kobject,string $fieldName) : string
    {
        $value="";
        if($kobject->getFieldGeo()==$fieldName)
        {
            if(!empty($kobject->getFieldValue($fieldName)))
            {
                $value="YES";
            }
            else
            {
                $value="NO";
            }
        }
        else
        {
            $value=$kobject->getFieldValue($fieldName);
            if(array_key_exists($fieldName,$this->listFkFields))
            {
                $map=$this->listFkFields[$fieldName];
                if(array_key_exists($value,$map))
                {
                    $value=$map[$value];
                }
            }
        }
        return "".$value;  
    }
    
    private function checkInit() : bool
    {
        if(!KInput::checkInput(KInput::$INPUT_POST, $this->PARAM_DRAW, KInput::$VARIABLE_INT, $this->draw))
        {
            return false;
        }
        if($this->draw<1)
        {
            return false;
        }
        if(!KInput::checkInput(KInput::$INPUT_GET, self::$PARAM_MAX_RESULT, KInput::$VARIABLE_INT, $this->max_results))
        {
            return false;
        }
        if(!$this->testIfClassNameIsKObject())
        {
            return false;
        }       
        return true;
    }
    
    public function execute(): bool
    {        
        $html = "";
        if ($this->checkInit())
        {
            $dbList = new DbList($this->object->getClassName());
            $this->object->initKFields();  
            $list = $dbList->getByArray($this->makeQueryArray($this->object), $this->makeSQLOrder($this->object), $this->makeSQLLimit());
            $stringData = '';
            
            $this->prepareLinkedFields();
            $this->checkColumns();

            /* @var $kobject KObject */
            foreach ($list as $kobject)
            {
                $arrayElement = [];
                foreach ($this->fields as $field_name => $field_label)
                {
                    $arrayElement[$field_label] = str_replace(["<script>","</script>"],"",$this->getValueField($kobject,$field_name));
                }
                if ($stringData != "")
                {
                    $stringData .= ',';
                }
                $stringData .= json_encode($arrayElement);
            }

            $last_query = "";
            //$nb_result_total=0;
            //$nb_result = 0;
            
            //$last_query=$dbList->getLast_query();            
            $nb_result = $dbList->getNbLastQuery();
            //$nb_result=80000;
            //$last_query.=" || ".$dbList->getLast_query();       
            //$nb_result_total=$dbList->getNb();

            if($this->debug)
            {
                $last_query = $dbList->getLast_query();
                $this->debug_string.= '"debug":   "'.urlencode($last_query).'",';
            }
            //  "debug":   "'.urlencode($last_query).'" ,  
            $html = '{
  "draw": '.$this->draw.',
'.$this->debug_string.'
  "recordsTotal": '.$this->max_results.',
  "recordsFiltered": '.$nb_result.',         
  "data": [
        '.$stringData.'
    ]
}
';
        }
        $this->addString($html);
        return true;
    }

    private function checkColumns() : void
    {    
        $map= new HashMap();
        if (isset($_POST["columns"]) && is_array($_POST["columns"]))
        { 
            foreach ($_POST["columns"] as $col)
            {
                if(isset($col["name"])&&isset($col["data"]))
                {
                    if($this->fields->get($col["name"]))
                    {
                        $map->put($col["name"], $col["data"]);
                    }
                }
            }
        }
        if($map->isEmpty())
        {
            foreach ($this->field_names as $name)
            {
                $this->fields->put($name,$name);
            }
        }
        else
        {
            $this->fields=$map;
        }
    }
    
    private function makeSQLOrder(KObject $class): ?SqlOrder
    {
        $sqlOrder = null;
        if (isset($_POST["order"]) && is_array($_POST["order"]))
        {
            for ($i = 0; $i < count($_POST["order"]); $i++)
            {
                if (is_array($_POST["order"][$i]) && isset($_POST["order"][$i]["column"]) && isset($_POST["order"][$i]["dir"]))
                {
                    $fieldOrder = $this->field_names->getOrNull($_POST["order"][$i]["column"]);
                    if (!is_null($fieldOrder))
                    {
                        /* @var $kField KField */
                        $kField = $class->getKField($fieldOrder);
                        $orderType = SqlOrder::$ASC;
                        if (strcmp("".$_POST["order"][$i]["dir"], "desc") == 0)
                        {
                            $orderType = SqlOrder::$DSC;
                        }
                        if (is_null($sqlOrder))
                        {
                            $sqlOrder = new SqlOrder($fieldOrder, $orderType,$kField->isGeometric());
                        }
                        else
                        {
                            $sqlOrder->addSqlOrderByInit($fieldOrder, $orderType,$kField->isGeometric());
                        }
                    }
                }
            }
        }
        return $sqlOrder;
    }

    private function makeSQLLimit(): ?SqlLimit
    {
        $sqlLimit = null;
        $length = 0;
        $start = 0;
        if (
                KInput::checkInput(KInput::$INPUT_POST, $this->PARAM_LENGTH, KInput::$VARIABLE_INT, $length) && KInput::checkInput(KInput::$INPUT_POST, $this->PARAM_START, KInput::$VARIABLE_INT, $start)
        )
        {
            $sqlLimit = new SqlLimit($length, $start);
        }
        return $sqlLimit;
    }

    private function testIfClassNameIsKObject() : bool
    {
        $class_name = "";
        if (KInput::checkInput(KInput::$INPUT_GET, self::$PARAM_CLASS_NAME, KInput::$VARIABLE_STRING, $class_name))
        {
            if ($class_name != "" && class_exists($class_name))
            {
                $this->object=null;
                $object = new $class_name();
                if ($object instanceof KObject)
                {
                    $this->object=$object;
                    $this->field_names = $this->object->getListFieldName();
                    $this->fields=new HashMap();
                    foreach ($this->field_names as $fieldName)
                    {
                        $this->fields->put($fieldName,$fieldName);
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 
     * @param KObject $class
     * @return array<int,mixed>|null
     */
    private function makeQueryArray(KObject $class): ?array
    {
        $array = null;
        if (isset($_POST[$this->PARAM_SEARCH]) 
                && is_array($_POST[$this->PARAM_SEARCH]) 
                && isset($_POST[$this->PARAM_SEARCH][$this->PARAM_SEARCH_VALUE]) 
                && $_POST[$this->PARAM_SEARCH][$this->PARAM_SEARCH_VALUE] != ""
        )
        {
            $values = explode(" ", $_POST[$this->PARAM_SEARCH][$this->PARAM_SEARCH_VALUE]);
            $array = [];

            /* @var $kField KField */
            $kField = null;
            

            foreach ($values as $value)
            {
                $arrayOr = [];
                foreach ($this->field_names as $field_name)
                {
                    $kField = $class->getKField($field_name);
                    if ($kField->getType() == KField::$INTEGER && isInteger($value))
                    {
                        $arrayOr[] = new QueryField($field_name, $value, QueryField::$EQUAL);
                    }
                    else if (($kField->getType() == KField::$FLOAT || $kField->getType() == KField::$DOUBLE) && isFloatOrDouble($value))
                    {
                        $arrayOr[] = new QueryField($field_name, $value, QueryField::$EQUAL);
                    }
                    else if ($kField->getType() == KField::$BOOL && !is_null($bool = convertToBool($value)))
                    {
                        $arrayOr[] = new QueryField($field_name, convertBoolToString($bool), QueryField::$EQUAL);
                    }
                    else if ($kField->getType() == KField::$VARCHAR || $kField->getType() == KField::$TEXT)
                    {
                        $arrayOr[] = new QueryField($field_name, $value, QueryField::$LIKE_1);
                    }
                    else if ($kField->getType() == KField::$DATETIME || $kField->getType() == KField::$DATE || $kField->getType() == KField::$TIMESTAMP) // && isDateTime($value))
                    {
                        $values = explode("-", $value);

                        // TEST YEAR
                        if (count($values) == 1 && isOnlyNumeric($values[0]) && $values[0] >= -9999 && $values[0] <= 9999)
                        {
                            $year = $this->makeYear("".$values[0]);
                            $arrayOr[] = [new QueryField($field_name, $year."-01-01", QueryField::$SUPERIOR_OR_EQUAL), new QueryField($field_name, $year."-12-".cal_days_in_month(CAL_GREGORIAN, 12, intval($year)), QueryField::$INFERIOR_OR_EQUAL)];
                        }
                        //TEST YEAR MONTH
                        else if (count($values) == 2 && isOnlyNumeric($values[0]) && $this->testIfMonth("".$values[1]))
                        {
                            $year = $this->makeYear("".$values[0]);
                            $month = "".$values[1];
                            $arrayOr[] = [new QueryField($field_name, $year."-".$month."-01", QueryField::$SUPERIOR_OR_EQUAL), new QueryField($field_name, $year."-".$month."-".cal_days_in_month(CAL_GREGORIAN, intval($month), intval($year)), QueryField::$INFERIOR_OR_EQUAL)];
                        }
                        //TEST YEAR MONTH DAY
                        else if (count($values) == 3 && isOnlyNumeric($values[0]) && $this->testIfMonth("".$values[1]) && $this->testIfDay("".$values[2]))
                        {
                            $year = $this->makeYear("".$values[0]);
                            $month = "".$values[1];
                            $day = "".$values[2];
                            $tomorrow = (DateTime::createFromFormat('Y-m-d', $year."-".$month."-".$day))->modify('+1 day')->format('Y-m-d');

                            $arrayOr[] = [new QueryField($field_name, $year."-".$month."-".$day, QueryField::$SUPERIOR_OR_EQUAL), new QueryField($field_name, $tomorrow, QueryField::$INFERIOR)];
                        }
                        
                        //$arrayOr[]=new QueryField($field_name,$value,QueryField::$LIKE_1);
                    }
                }
                $array[] = $arrayOr;
            }
        }
        
        $addParams=[];
        if (KInput::checkInputGet(self::$PARAM_ADDITIONNAL,KInput::$VARIABLE_STRING , $addParams)) 
        {
            $params=json_decode($addParams);
            //$this->debug_string.='"debug": ['.(print_r($addParams,true)).'],';
            foreach($params as $key => $value)
            {
                $array[]=QueryField::m($key,$value);
            }
        }
        
        
        return $array;
    }

    private function makeYear(mixed $the_year): string
    {
        $year="".$the_year;
        $count = strlen($year);
        for ($i = 0; $i < 4 - $count; $i++)
        {
            $year = "0".$year;
        }
        if($year=="0000")
        {
            $year="0001";
        }
        return $year;
    }

    private function testIfMonth(mixed $the_month): bool
    {
        $month="".$the_month;
        if (strlen($month) == 2)
        {
            if ($month[0] == "0")
            {
                if ($month[1] >= 0 && $month[1] <= 9)
                {
                    return true;
                }
            }
            else if ($month[0] == "1")
            {
                if ($month[1] >= 0 && $month[1] <= 2)
                {
                    return true;
                }
            }
        }
        return false;
    }

    private function testIfDay(mixed $the_day): bool
    {
        $day="".$the_day;
        if (strlen($day) == 2)
        {
            if ($day[0] == "0" || $day[0] == "1" || $day[0] == "2")
            {
                if ($day[1] >= 0 && $day[1] <= 9)
                {
                    return true;
                }
            }
            else if ($day[0] == "3")
            {
                if ($day[1] >= 0 && $day[1] <= 1)
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    private function prepareLinkedFields() : void
    {     
        $this->listFkFields=$this->object->getAllForeignsKeyValue();
    }
}