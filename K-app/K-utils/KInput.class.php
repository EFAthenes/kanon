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
class KInput
{
    public static string $INPUT_GET="GET";
    public static string $INPUT_POST="POST";
    public static string $INPUT_COOKIE="COOKIE";
    public static string $INPUT_SERVER="SERVER";
    public static string $INPUT_ENV="ENV";
    private static string $kerror="";
    private static bool $case_sensitive=true;
    private static bool $amp_start_replace=false;
    
    
    public static string $VARIABLE_INT="INT";
    public static string $VARIABLE_FLOAT="FLOAT";
    public static string $VARIABLE_BOOL="BOOL";
    public static string $VARIABLE_STRING="STRING";
    public static string $VARIABLE_LONG_TEXT="LONG_TEXT";
    public static string $VARIABLE_ARRAY="ARRAY";
    
    //private static array $type_array=array("GET","POST","COOKIE","SERVER","ENV");
    private function __construct()
    {
        
    }
    
    public static function setCaseNotSensitive() : void
    {
        self::$case_sensitive=false;
    }
    
    public static function checkInputPostGet(string $key_name,string $variable_type,mixed &$dest_variable) : bool
    {
        if(
            !self::checkInput(self::$INPUT_GET,$key_name,$variable_type,$dest_variable)
                &&
            !self::checkInput(self::$INPUT_POST,$key_name,$variable_type,$dest_variable)    
            )
        {
            return false;
        }
        return true;
    }
    
    public static function checkInputGet(string $key_name,string $variable_type,mixed &$dest_variable) : bool
    {
        return self::checkInput(self::$INPUT_GET,$key_name,$variable_type,$dest_variable);
    }
    
    public static function checkInputPost(string $key_name,string $variable_type,mixed &$dest_variable) : bool
    {
        return self::checkInput(self::$INPUT_POST,$key_name,$variable_type,$dest_variable);
    }

    public static function checkInput(string $type_input,string $key_name,string $variable_type,mixed &$dest_variable) : bool
    {
        //KDebugger::getInstance()->dump($variable_type,$key_name);
        $input=self::getInputType($type_input);
        if(!is_null($input))
        {
            if(count($input))
            {
                if(!self::$case_sensitive)
                {
                    $key_name=strtolower($key_name);
                }
                //echo "TEST => ".$key_name."<br />";
                if(isset($input[$key_name]))
                {
                    //echo "VALUE => ".$type_input."//".$key_name."//".$input[$key_name]."<br />";
                    $field=null;
                    if($variable_type==KInput::$VARIABLE_INT)
                    {
                        $field=KField::factoryKField(KField::$INTEGER);
                    }
                    else if($variable_type==KInput::$VARIABLE_FLOAT)
                    {
                        $field=KField::factoryKField(KField::$FLOAT);
                    }
                    else if($variable_type==KInput::$VARIABLE_BOOL)
                    {
                        $field=KField::factoryKField(KField::$BOOL);
                    } 
                    else if($variable_type==KInput::$VARIABLE_STRING)
                    {
                        $field=KField::factoryKField(KField::$VARCHAR);
                    }
                    else if($variable_type==KInput::$VARIABLE_LONG_TEXT)
                    {
                        $field=KField::factoryKField(KField::$TEXT);
                    }                     
                    else if($variable_type==KInput::$VARIABLE_ARRAY)
                    {
                        $field=KField::factoryKField(KField::$VARCHAR);
                        $value=$input[$key_name];
                        if(is_array($value))
                        {
                            $dest_variable=$value;
                            return true;
                        }
                    }                    
                    
                    if($field==null)
                    {
                        self::$kerror="TYPE INPUT ".$type_input." exists // Key exits => ".$key_name." // VARIABLE TYPE is not a ".$variable_type."!";
                    }
                    else
                    {
                        $value=$input[$key_name];
                        //KDebugger::getInstance()->dump($value,$key_name);
                        if($field->set($value))
                        {
                            $dest_variable=$field->get();
                            return true;
                        }
                        else
                        {
                            self::$kerror=$field->getKerror();
                        }
                    }
                }
                else
                {
                    self::$kerror="TYPE INPUT ".$type_input." exists // Key doesn't => ".$key_name."!";
                }
            }
            else
            {
                self::$kerror="TYPE INPUT ".$type_input." is not defined!";
            }
        }
        else
        {
            self::$kerror="TYPE INPUT ".$type_input." doesn't exist!";
        }
        return false;
    }
    /**
     * 
     * @param string $type_input
     * @param string $key_name
     * @param string $variable_type
     * @param array<mixed,mixed> $array
     * @return bool
     */
    public static function checkInputArray(string $type_input,string $key_name,string $variable_type,array &$array) : bool
    {
        $input=self::getInputType($type_input);
        if(!is_null($input))
        {
            if(count($input)==0)
            {
                $array=[];
            }
            else
            {
                //echo "checkInputArray TEST => ".$type_input." ".$key_name."<br />";
                //var_dump($_POST);
                if(isset($input[$key_name])&&is_array($input[$key_name]))
                {
                    //$array=[];
                    foreach($input[$key_name] as $key=> $value)
                    {
                        $field=null;
                        if($variable_type==KInput::$VARIABLE_INT)
                        {
                            $field=KField::factoryKField(KField::$INTEGER);
                        }
                        else if($variable_type==KInput::$VARIABLE_FLOAT)
                        {
                            $field=KField::factoryKField(KField::$FLOAT);
                        }
                        else if($variable_type==KInput::$VARIABLE_BOOL)
                        {
                            $field=KField::factoryKField(KField::$BOOL);
                        } 
                        else if($variable_type==KInput::$VARIABLE_STRING)
                        {
                            $field=KField::factoryKField(KField::$VARCHAR);
                        }  
                        
                        if($field==null)
                        {
                            self::$kerror="TYPE INPUT ".$type_input." exists // Key exits => ".$key_name." // VARIABLE TYPE is not a ".$variable_type."!";
                        }
                        else
                        {
                            if($field->set($value))
                            {
                                $array[$key]=$field->get();
                            }
                            else
                            {
                                self::$kerror=$field->getKerror();
                                return false;
                            }                    
                        }
                    }
                    
//                    if(isArrayAssoc($array))
//                    {
//                    }
                    return true;
                }
                else
                {
                    self::$kerror="TYPE INPUT ".$type_input." exists // Key doesn't => ".$key_name."!";
                }
            }
//            else
//            {
//                self::$kerror="TYPE INPUT ".$type_input." is not defined!";
//            }
        }
        else
        {
            self::$kerror="TYPE INPUT ".$type_input." doesn't exist!";
        }
        return false;
    }    
     
    /**
     * 
     * @param string $type_input
     * @param string $containing_string
     * @param array<int,string> $dest_variable
     * @return bool
     */
    public static function checkInputContainingStringToArray(string $type_input,string $containing_string,array &$dest_variable) : bool
    {
        $count=count($dest_variable);
        $input=self::getInputType($type_input);
        if(!is_null($input))
        {
            if(count($input))
            {
                foreach($input as $key => $value) 
                {
                    if (strpos($key, $containing_string) === 0) 
                    {
                        $dest_variable[]=str_replace($containing_string,"",$key);
                    }
                }
                if($count==count($dest_variable))
                {
                    self::$kerror="NO KEY FOUND FOR STRING STARTING : ".$containing_string."!";
                }
                else
                {
                    return true;
                }
            }
            else
            {
                self::$kerror="TYPE INPUT ".$type_input." is not defined!";
            }
        }
        else
        {
            self::$kerror="TYPE INPUT ".$type_input." doesn't exist!";
        }
        return false;
    }    
    /**
     * 
     * @param string $inputType
     * @return array<string,string|array<string,string>>|null
     */
    private static function getInputType(string $inputType) : ?array
    {
        $inputReturnType=null;
        switch($inputType)
        {
            case self::$INPUT_GET:
            {
                self::$amp_start_replace=true;
                $inputReturnType=$_GET;
                break;
            }
            case self::$INPUT_POST:
            {
                $inputReturnType=$_POST;
                break;
            }
            case self::$INPUT_COOKIE:
            {
                $inputReturnType=$_COOKIE;
                break;
            }
            case self::$INPUT_SERVER:
            {
                $inputReturnType=$_SERVER;
                break; 
            }
            case self::$INPUT_ENV:
            {
                $inputReturnType=$_ENV;
                break; 
            }
            default:
                break;
        }
        
        if(!self::$case_sensitive&&!is_null($inputReturnType))
        {
            $inputReturnType = array_change_key_case($inputReturnType, CASE_LOWER);
        }
        
        if(self::$amp_start_replace&&!is_null($inputReturnType))
        {
            $newInputReturnType=[];
            foreach($inputReturnType as $key => $value)
            {
                $key_str= strval($key);
                if(str_starts_with($key_str,"amp;"))
                {
                    $newKey=str_replace("amp;","",$key_str);
                    $newInputReturnType[$newKey]=$value;
                }
                else
                {
                    $newInputReturnType[$key_str]=$value;
                }
            }
            $inputReturnType=$newInputReturnType;
        }        
        return $inputReturnType; 
    }

    /**
     * 
     * @param array<int,string> $list
     * @return bool
     */
    public static function checkInputsPostGetListStrict(array $list):bool
    {
        if(     
            self::checkInputsList(self::$INPUT_POST, $list, true)
            ||
            self::checkInputsList(self::$INPUT_GET, $list, true)    
           )
        {
            return true;
        }
        return false;
    }
    /**
     * 
     * @param array<int,string> $list
     * @return bool
     */
    public static function checkInputsPostGetListNotStrict(array $list):bool
    {
        if(     
            self::checkInputsList(self::$INPUT_POST, $list, false)
            ||
            self::checkInputsList(self::$INPUT_GET, $list, false)    
           )
        {
            return true;
        }
        return false;
    }
    /**
     * 
     * @param string $type_input
     * @param array<int,string> $list
     * @param bool $strict
     * @return bool
     */
    public static function checkInputsList(string $type_input,array $list,bool $strict=false):bool
    {
        if(!self::$case_sensitive)
        {
            $list2=[];
            foreach ($list as $item)
            {
                $list2[]=strtolower($item);
            }
            $list=$list2;
        }
        $input=self::getInputType($type_input);
        if(!is_null($input)&&count($input))
        {
            foreach ($input as $key=>$value)
            {    
                if(!self::$case_sensitive)
                {
                    $key=strtolower($key);
                }
                
                if(($key2 = array_search($key, $list)) !== false)
                {
                    unset($list[$key2]);
                }
                else if($strict)
                {
                    //echo "NOT FOUND => ".$key."||". print_r($list,true);
                    return false;
                }
            }
            if(count($list)==0)
            {
                return true;
            }
        }
        return false;
    }
    
    public static function getKError() : string
    {
        return self::$kerror;
    }

    public static function setKError(string $error) : void
    {
        self::$kerror=$error;
    }
}