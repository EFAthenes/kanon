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
//function checkMxRecords($email) : int
//{
//    $result=0;
//    if (isEmail($email))
//    {
//        $result=1;
//        /*
//        if(ParamManager::getInstance()->debug)
//        {
//            $result=1;
//        }
//        else
//        {
//            //list($prefix, $domain) = split("@", $email);
//            list($prefix, $domain) = explode("@", $email);
//            $mxhosts=null;
//
//            if ( (getmxrr($domain, $mxhosts)) && $mxhosts!=null && $mxhosts!="0.0.0.0" )
//            {
//                $result=1;
//            }
//            if( $result==0 && @fsockopen($domain, 25, $errno, $errstr, 5))
//            {
//                $result=1;
//            }
//        }
//         * 
//         */
//    }
//    return $result;
//}
//function checkPOST($key, $default = '') 
//{
//    if(isset($_POST[$key])&&!empty($_POST[$key])) 
//    {
//        return $_POST[$key];
//    } 
//    else 
//    {
//        return $default;
//    }
//}
//function isPOST($key) 
//{
//    if(isset($_POST[$key])) 
//    {
//        return true;
//    } 
//    return false;
//}
//function isPOSTAndNotEmpty($key) 
//{
//    if(isset($_POST[$key])&&!empty($_POST[$key])) 
//    {
//        return true;
//    } 
//    return false;
//}
//function checkGet($key, $default = '') 
//{
//    if(isset($_GET[$key])&&!empty($_GET[$key])) 
//    {
//        return $_GET[$key];
//    }
//    else 
//    {
//        return $default;
//    }
//}
function isArrayAssoc(mixed $arr) : bool
{
    if(is_null($arr))
    {
        return false;
    }
    if (!is_array($arr))
    {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}
/**
 * 
 * @param array<int,array<int,mixed>|null> $array
 * @param mixed $index
 * @param bool $strict
 * @param mixed $default
 * @return mixed
 */
function getValueByIndexInDoubleArray(array $array,mixed $index,bool $strict=false,mixed $default=null) :mixed
{
    foreach($array as $in_array)
    {
        if(is_array($in_array))
        {
            if(count($in_array)>1 && ($strict && $index===$in_array[0]) || (!$strict && $index==$in_array[0]))
            {
                return $in_array[1];
            }
        }
    }
    return $default;
}

function isInCLI() : bool
{
    return (php_sapi_name()==='cli');
}