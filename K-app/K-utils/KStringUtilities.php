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
function noSpace(?string $string) : string
{
    if(is_null($string))
    {
        return "";
    }
    return str_replace(" ","",$string);
}

function isName(?string $name) : string
{
    if(is_null($name))
    {
        return "";
    }    
    $name=str_replace(" ","",$name);
    $name=ucfirst($name);
    return $name;
}

function noSpaceNoPoints(?string $string) : string
{
    if(is_null($string))
    {
        return "";
    }     
    $string=str_replace(" ","",$string);
    $string=str_replace(":","-",$string);
    return $string;
}

function removeNotRequiredCharsHTML(?string $string) : string
{
    if(is_null($string))
    {
        return "";
    }     
    /*
    " " (ASCII 32 (0x20)), an ordinary space. 
 "\t" (ASCII 9 (0x09)), a tab. 
 "\n" (ASCII 10 (0x0A)), a new line (line feed). 
 "\r" (ASCII 13 (0x0D)), a carriage return. 
 "\0" (ASCII 0 (0x00)), the NUL-byte. 
 "\x0B" (ASCII 11 (0x0B)), a vertical tab.
 "ASCII 16 ,DLE.
     */
    
    $string=trim($string);   
    $string=str_replace("\t"," ",$string);
    $string=str_replace("\n"," ",$string);
    $string=str_replace("\r"," ",$string);
    $string=str_replace("\0","",$string);
    $string=str_replace("'","’",$string);
    $string=str_replace("</p>",",",$string);
    $string=str_replace("<p>","",$string);
    $string=str_replace(chr(16),"",$string);
    $string=str_replace(chr(11)," ",$string);
    $string=html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
    $string=strip_tags($string,'<p><a><span><br /></p></a></span>');
    if($string[strlen($string)-1]==",")
    {
        $string[strlen($string)-1]=".";
    }
    return $string;
}

function isIp(mixed $ip) : bool
{
    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
    {
        return true;
    }
    return false;
}

function isPrivateIp(mixed $ip) : bool 
{
    if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
    {
        return true;
    }
    return false;
}

function checkIsUTF8(?string $string) : string
{
    mb_internal_encoding('UTF-8'); // always needed before mb_ functions, check note below
    if (mb_strlen($string) != strlen($string)) 
    {       
        $string=mb_convert_encoding($string, 'UTF-8', mb_list_encodings());
        $string=strval($string);
    }
    return $string;
}
function isYear(mixed $input) : bool
{
    $input = (int)$input;
    if($input>1000 && $input<2100)
    {
        return true;
    }
    return false;
}

//##############################################################################
function replaceSpecialCharsByHTML(?string $string) : bool
{
    if(preg_replace_callback("/(?!\w)\p{L}/u", "convertItSpecialChar", $string))
    {
        return true;
    }
    return false;
}
/**
 * 
 * @param array<int,string> $m
 * @return string
 */
function convertItSpecialChar(array $m) : string
{
    return "&#x" . bin2hex(mb_convert_encoding( $m[0] , "UCS-2BE", "UTF-8")) . ";";
}

function convertStringToHexBinary(?string $string): string
{
    if(is_null($string))
    {
        return "";
    }
    $hex='';
    for ($i=0; $i < strlen($string); $i++)
    {
        $hex .= "\x".dechex(ord($string[$i]));
    }
    return $hex;
}

function convertDoubleQuotes(?string $string): string
{
    if(is_null($string))
    {
        return "";
    }    
    return str_replace('"', '&quot;', $string);
}
function unconvertDoubleQuotes(?string $string): string
{
    if(is_null($string))
    {
        return "";
    }    
    return str_replace('&quot;','"', $string);
}
//
//function in_array_like($referencia,$array)
//{ 
//   foreach ($array as $ref)
//    {
//        if(strstr($referencia, $ref))
//        {
//            return true;
//        }
//    }
//    return false;
//}

//######### DATE INVERT ###############
function frenchDate(?string $date,string$separatorIn="-",string $separatorOut="-") : string
{
    if(is_null($date)||empty($separatorIn))
    {
        return "";
    }       
    $tab=explode($separatorIn, $date);
    if(count($tab)==3)
    {
        return $tab[2].$separatorOut.$tab[1].$separatorOut.$tab[0];
    }
    else
    {
        return "00".$separatorOut."00".$separatorOut."0000";
    }
}

function getYearOfDate(?string $date,string $separator="-") : string
{
    if(is_null($date)||empty($separator))
    {
        return "";
    }       
    $tab=explode($separator, $date);
    if(count($tab)==3)
    {
        return $tab[0];
    }
    return $date;
}

function infoDate(?string  $date,string $separatorIn="-",string $separatorOut="-") : string
{
    if(is_null($date)||empty($separatorIn))
    {
        return "";
    }       
    $tab=explode($separatorIn, $date);
    if(count($tab)==3)
    {
        return $tab[2].$separatorOut.$tab[1].$separatorOut.$tab[0];
    }
    else
    {
        return "0000".$separatorOut."00".$separatorOut."00";
    }
}

function isDateTime(?string $date,string $formatDate='Y-m-d G:i:s') : bool
{
    if(is_null($date))
    {
        return false;
    }     
    if (DateTime::createFromFormat($formatDate, $date) !== FALSE) 
    {
        return true;
    }
    return false;
}

function isDate(?string $date,string $formatDate='Y-m-d') : bool
{
    if(is_null($date))
    {
        return false;
    }    
    if (DateTime::createFromFormat($formatDate, $date) !== FALSE) 
    {
        return true;
    }
    return false;
}

function verifStringIsDate(?string $date,string $formatDate='Y-m-d G:i:s') : bool
{
    if(is_null($date))
    {
        return false;
    }      
    if (DateTime::createFromFormat($formatDate, $date) !== FALSE) 
    {
        return true;
    }
    return false;
}

function isEmail(?string $email) : bool
{
    if(is_null($email))
    {
        return false;
    }      
    return filter_var($email, FILTER_VALIDATE_EMAIL);  
}

function isZip(?string $zip) : bool
{
    if(is_null($zip))
    {
        return false;
    }      
    if (preg_match('/^[0-9]+$/', $zip))
    {
        return true;
    }
    return false;
}

function isOnlyNumeric(mixed $number) : bool
{
    if(is_null($number))
    {
        return false;
    }     
    $pattern = '/^[0-9]+$/';
    if(preg_match($pattern, $number)) 
    {
        return true;
    }
    return false;
}

function isInteger(mixed $number) : bool
{
    if(is_null($number))
    {
        return false;
    }      
    //if (preg_match('/^[-0-9,]+$/', $number))
    if(is_numeric($number))
    {
        return true;
    }
    return false;
}
function isLetter(?string $letter) : bool
{
    if(is_null($letter))
    {
        return false;
    }      
    if(ctype_alpha($letter)&&strlen($letter)==1)
    {
        return true;
    }
    return false;
}

function isSiret(?string $string) : bool
{
    if(is_null($string))
    {
        return false;
    }     
    if (preg_match('/^[0-9]{14}/', $string))
    {
        // verifier si c'est un Siret credible
        return true;
    }
    return false;
}

function isPhone(?string $string) : bool
{
    if(is_null($string))
    {
        return false;
    }     
    $string=str_replace(" ","",$string);
    $string=str_replace("(","",$string);
    $string=str_replace(")","",$string);
    $string=str_replace(".","",$string);

    if(isInteger($string))
    {
        return true;
    }
    return false;
}

function isFloatOrDouble(?string $input) : bool
{
    if(is_null($input))
    {
        return false;
    }     
    
    $float="".floatval($input);
    if($float==$input)
    {
        return true;
    }
    //if(preg_match('^[-+]?([0-9]+(\.[0-9]+)?|\.[0-9]+)$/i', $input))
    else if(isInteger($input))
    {
        return true;
    }
    return false;
}

function isAlphaLower(mixed $input) : bool
{
    return ctype_lower($input);
}

function isPhpVariableNameCompliant(string $var_name) : bool
{
    if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',$var_name) )
    {
        // the string is valid
        return true;
    }    
    return false;
}

function isAlphaNumeric(mixed $input) : bool
{
    if(is_null($input))
    {
        return false;
    }  
    $input=$input."";
    if(preg_match('/^[a-z0-9]*[a-z]*[a-z0-9]*$/i', $input))
    {
        return true;
    }
    return false;
}

function isAlphaNumericAndUndescore(mixed $input) : bool
{
    if(is_null($input))
    {
        return false;
    }  
    $input=$input."";
    //'/^[a-z0-9\-_]*[a-z0-9\-_]*[a-z0-9\-_]*$/i'
    if(preg_match('/^[a-zA-Z0-9_]+$/', $input))
    {
        return true;
    }
    return false;
}

function try2ConvertToInt(mixed $value) : ?int
{
    //echo "1 ".$value."<br />";
   //var_dump($value);
    if(is_null($value))
    {
        return null;
    }
    else if(is_array($value))
    {
        return null;
    }
    elseif(is_object($value))
    {
        return null;
    }
    elseif($value=="0")
    {
        return 0;
    }
    else
    {
        $value_2=intval("".$value);
        //echo "2 ".$value."<br />";
        if($value_2.""===$value."")
        {
            //echo "3 => OK <br />";
            $value=(int)$value_2;
            return $value;
        }
    }
    return null;
}

function try2ConvertToFloat(mixed $value) : ? float
{
    if(!is_null($value))
    {
        $value=trim("".$value);
        if(strcmp($value,"0")==0)
        {
            return 0;
        }
        else if(string_contains(".", $value))
        {
            $value=rtrim($value,"0");
            $value=rtrim($value,".");
        }
        
        $value_2=floatval($value);
        if($value_2.""===$value."")
        {
            return $value_2;
        } 
    }
    return null;
}

function string_contains(?string $str_search, ?string  $content,bool $ignorecase=true) : bool
{
    if(empty($str_search))
    {
        return false;
    }
    if(empty($content))
    {
        return false;
    }    
    
    if ($ignorecase)
    {
        $str_search = strtolower($str_search);
        $content = strtolower($content);
    }
    if(strpos($content,$str_search)!==false)
    {
        return true;
    }
    return false;
}


function toUpper(?string $string) : string 
{
    if(is_null($string))
    {  
        return "";
    }  
    $count=strlen($string);
    $newString="";

    for($i=0; $i< $count ; $i++)
    {
        $var=$string[$i];
        $value=ord($var);
        if($value>223)
        {
            $var='&#'.($value-32).";";
        }

        $newString.=$var;
    }
    return strtoupper($newString);
}

function firstToUpper(?string $string) : string 
{
    if(is_null($string))
    {  
        return "";
    }      
    $newString="";
    for($i=0; $i<1 ; $i++)
    {
        $var=$string[$i];
        $value=ord($var);
        if($value >= 224)
        {
            $var='&#'.($value-32).";";
        }
        $newString.=$var;
    }
    return strtoupper($newString);
}


//function makePrinting($word)
//{
//    return ascii2entities($word);
//}

function ascii2entities(?string $string) : string
{
    if(is_null($string))
    {
        return "";
    }
    for($i=128;$i<=255;$i++)
    {
        $entity = htmlentities(chr($i), ENT_QUOTES, 'cp1252');
        $temp = substr($entity, 0, 1);
        $temp .= substr($entity, -1, 1);
        if ($temp != '&;')
        {
            $string = str_replace(chr($i), '', $string);
        }
        else
        {
            $string = str_replace(chr($i), $entity, $string);
        }
    }
    return $string;
}

function ascii2entitiesbis(?string $string) : string
{
    if(is_null($string))
    {
        return "";
    }    
    //$string=str_replace(" ", "\n", $string);
    $string=str_replace(" ", "<br />", $string);

    for($i=128;$i<=255;$i++)
    {
        $entity = htmlentities(chr($i), ENT_QUOTES, 'cp1252');
        $temp = substr($entity, 0, 1);
        $temp .= substr($entity, -1, 1);
        if ($temp != '&;')
        {
            $string = str_replace(chr($i), '', $string);
        }
        else
        {
            $string = str_replace(chr($i), $entity, $string);
        }
    }
    return $string;
}
function removeNonSGMLChars(mixed $output) : string
{
    return preg_replace('/[^(\x20-\x7F)]*/','',"".$output);
}

function detectUTF8(?string $string) : bool
{
    if(!is_null($string)&&preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )+%xs',
    $string))
    {
        return true;
    }
    return false;
}
function convertToUTF8(?string $str): string
{
    if (empty($str))
    {
        return "";
    }
    $enc = mb_detect_encoding($str);

    if ($enc && $enc != 'UTF-8')
    {
        return strval(iconv($enc, 'UTF-8', $str));
    }
    else
    {
        return $str;
    }
}

function getStringBetween(?string $str,?string $from,?string $to) : string
{
    if(empty($str))
    {
        return "";
    }
    else if(empty($from)||empty($to))
    {
        return $str;
    }    
    $sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
    return substr($sub,0,strpos($sub,$to));
}

function stringStartsWith(?string $haystack,?string $needle) :bool
{
    if(empty($needle))
    {
        return true;
    }
    elseif(empty($haystack))
    {
        return false;
    }
    
    // search backwards starting from haystack length characters from the end
    return (strrpos($haystack, $needle, -strlen($haystack)) !== FALSE);
}
function stringEndsWith(?string $haystack, ?string $needle) : bool
{
    if(empty($needle))
    {
        return true;
    }
    elseif(empty($haystack))
    {
        return false;
    }    
    // search forward starting from end minus needle length characters
    return (strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE);
}

function initSelect(string $selectHTML,string $value) : string
{
    return str_replace('value="' . $value . '"','value="' . $value . '" selected',$selectHTML);
}
/**
 * 
 * @param string $selectHTML
 * @param array<int,string> $arrayValue
 * @return string
 */
function initMultipleSelect(string $selectHTML, array $arrayValue) : string
{
    if($arrayValue!=null)
    {
        for ($i = 0; $i < count($arrayValue); $i++)
        {
            $selectHTML=str_replace('value="' . $arrayValue[$i] . '"','value="' . $arrayValue[$i] . '" selected',$selectHTML);
        }
    }
    return $selectHTML;
}

function str_last_replace(string $search,string $replace,string $subject) : string
{
    $pos = strrpos($subject, $search);
    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}
function str_first_replace(string $search,string $replace,string $subject) : string
{
    $pos = strpos($subject,$search);
    if ($pos !== false) 
    {
        $subject = substr_replace($subject,$replace,$pos,strlen($search));
    }    
    return $subject;
}

function replace_accent(string $str) : string 
{ 
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'); 
  return str_replace($a, $b, $str); 
}
/**
 * 
 * @param string $str
 * @param array<int,string> $replace
 * @param string $delimiter
 * @return string
 */
function toURI(string $str,array $replace = [],string $delimiter = '-') : string
{
    if(count($replace))
    {
        $str = str_replace($replace, ' ', $str);
    }

    $clean=replace_accent($str);
    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    return $clean;
}

function frenchToURI(string $str) : string
{
    return toURI($str, array('*', '`'), $delimiter = '-');
}
/**
 * 
 * @return array<int,int|string|true>
 */
function getArrayOfStringBoolTrue() : array
{
    $arrayBool=['true','True','TRUE','yes','Yes','y','Y','1','on','On','ON','t',true,1,'Vrai','VRAI'];
    return $arrayBool;
}
/**
 * 
 * @return array<int,int|string|false|null>
 */
function getArrayOfStringBoolFalse() : array
{
    $arrayBool=['false','False','FALSE','no','No','n','N','0','off','Off','OFF','f',false,0,null,'Faux','FAUX'];
    return $arrayBool;
}

function convertToBool(mixed $in) : ?bool
{
    // if not strict, we only have to check if something is false
    if(in_array($in,getArrayOfStringBoolFalse(),true))
    {
        return false;
    }
    else if(in_array($in,getArrayOfStringBoolTrue(),true))
    {
        return true;
    }
    else
    {
        return null;
    }
}

function convertBoolToString(mixed $in) : string
{
    if($in)
    {
        return "true";
    }
    return "false";
}

function convertBoolToStringNumber(mixed $in) : string
{
    if($in)
    {
        return "1";
    }
    return "0";
}

function isStringNull(mixed $in) : bool
{
    if(in_array($in,array('null','NULL',null,NULL),true))
    {
        return true;
    }
    return false;
}

function isStringIntegerNull(mixed $in) : bool
{
    if(in_array($in,array('null','NULL','',null,NULL),true))
    {
        return true;
    }
    return false;
}


function convertStringtoIntegerOrNull(mixed $in) : ?int
{
    if(in_array($in,array('null','NULL','',null,NULL),true))
    {
        return null;
    } 
    return intval($in);
}

function stringContainsLetter(string $string) : bool
{
    if(preg_match('/[a-zA-Z]/', $string))
    {
        return true;
    }
    return false;
}

function stringContainsDigit(string $string) : bool
{
    if(preg_match('/\d/', $string))
    {
        return true;
    }
    return false;
}

function stringContainsSpecial(string $string) : bool
{
    if(preg_match('/[^a-zA-Z\d]/', $string))
    {
        return true;
    }
    return false;
}

function cutPrettyStringOnSpace(string $string,int $length=1000,string $allow_tags="<br /><p>") : string
{
//    if(strlen($string)<$length)
//    {
//        return strip_tags($string,$allow_tags);
//    }
    $string2=strip_tags($string,$allow_tags);
//    $string=substr(strip_tags($string,$allow_tags),0,$length);    
//    $pos = strrpos($string, " ");
//    if ($pos !== false)
//    { 
//        $string=substr ( $string , 0 ,$pos );
//    }
    return cutStringOnSpace($string2,$length);
}

function cutStringOnSpace(string $string,int $length=1000) : string
{
    $string=substr($string,0,$length);    
    $pos = strrpos($string, " ");
    if ($pos !== false)
    { 
        $string=substr ( $string , 0 ,$pos );
    }
    return $string;
}
/**
 * 
 * @param string $string
 * @param array<int,string> $array_do_not_replace
 * @return string
 */
function replace_some_entities(string $string, array $array_do_not_replace=[]) : string
{
    for($i=0; $i<32; $i++)
    {
        if(!in_array($i,$array_do_not_replace))
        {
            $string=str_replace("&#".$i.";","",$string);        
        }
    }
    return $string;
}

//
//function minifyStringCSS($css)
//{
//    return $css;
//    // some of the following functions to minimize the css-output are directly taken
//    // from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
//    // all credits to Christian Schaefer: http://twitter.com/derSchepp
//    //  https://gist.github.com/webgefrickel/3339063
//    // remove comments
//    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
//    // backup values within single or double quotes
//    preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
//    for ($i = 0; $i < count($hit[1]); $i++)
//    {
//        $css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
//    }
//    // remove traling semicolon of selector's last property
//    $css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);
//    // remove any whitespace between semicolon and property-name
//    $css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);
//    // remove any whitespace surrounding property-colon
//    $css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);
//    // remove any whitespace surrounding selector-comma
//    $css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);
//    // remove any whitespace surrounding opening parenthesis
//    $css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);
//    // remove any whitespace between numbers and units
//    $css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);
//    // shorten zero-values
//    $css = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css);
//    // constrain multiple whitespaces
//    $css = preg_replace('/\p{Zs}+/ims', ' ', $css);
//    // remove newlines
//    $css = str_replace(array("\r\n", "\r", "\n"), '', $css);
//    // Restore backupped values within single or double quotes
//    for ($i = 0; $i < count($hit[1]); $i++)
//    {
//        $css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
//    }
//    return $css;
//}
/**
 * 
 * @param array<int,mixed> $arrayStrings
 * @param string $separator
 * @return string
 */
function convertArrayStringsToString(array $arrayStrings,string $separator=" ,") : string
{
    $one_string="";
    foreach ($arrayStrings as $string)
    {
        if($one_string!="")
        {
            $one_string.=$separator;
        }
        $one_string.="".$string;
    }
    return $one_string;
}

function filterCharFilename(string $filename) : string 
{
    // sanitize filename
    $filenameOutput = preg_replace(
        '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'+;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
        '-', $filename);
    
    // [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2

    return $filenameOutput;
}

function isHexaColor(string $color) : bool
{
    
    if(preg_match('/^#[a-f0-9]{6}$/i', $color)) //hex color is valid
    {
        return true;
    }
    return false;
}

function makeHTMLImageSrc(string $html) : string
{
    $match=[];
    preg_match( '@src="([^"]+)"@' , $html, $match );
    $src = array_pop($match);
    return $src;
}

function isNameAuthorizedForFolderAndFile(string $name) : bool
{
    return (strpbrk($name, "\\/?%*:|\"<>") === FALSE);
}

function stripInvalidXmlCharacters(string $value) :string
{
    $ret = "";
    if(empty($value)) 
    {
        return $ret;
    }

    $length = strlen($value);
    for ($i=0; $i < $length; $i++)
    {
        $current = ord($value[$i]);
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x28)))
            /*
                    && ($current <= 0xD7FF) ) ||
            (($current >= 0xE000) 
                            && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) 
                                    && ($current <= 0x10FFFF))) */
        {
            $ret .= chr($current);
        }
        else
        {
            $ret .= " ";
        }
    }
    return $ret;
}

function KhtmlEntities(mixed $string) : string
{
    if(!is_null($string))
    {
        return htmlentities("".$string);
    }
    return "";
}
/**
 * 
 * @param string|null $separator
 * @param array<int,mixed>|null $array
 * @return string
 */
function implodeHtmlEntities(?string $separator,?array $array) : string
{
    if(is_null($array)||count($array)==0)
    {
        return "";
    }    
    $string="";
    foreach ($array as $value)
    {
        if(!empty($string)&&!empty($separator))
        {
            $string.=$separator;
        }
        $string.=KhtmlEntities($value);
    }
    return $string;
}

function convertStringToBeW3C_Id(mixed $var) : string
{
    $string="".$var;
   //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces to underscore 
    $string = preg_replace("/[\s]/", "_", $string);
    return $string;    
}


//function wktToCoordinates(?string $wkt): array 
//{
//    if (empty($wkt)) 
//    {
//        return [];
//    }
//    $coordinates = explode(',', str_replace(['MULTIPOLYGON','POLYGON', '(', ')'], '', $wkt));
//    $parsedCoordinates = [];
//
//    foreach ($coordinates as $coordinate) 
//    {
//        list($lng, $lat) = explode(' ', trim($coordinate));
//        $parsedCoordinates[] = [(float) $lat, (float) $lng];
//    }
//    return $parsedCoordinates;
//}

function array_depth(mixed $array) : int
{
    if(is_null($array) || !is_array($array))
    {
        return -1;
    }
    $max_indentation = 1;
    $array_str = print_r($array, true);
    $lines = explode("\n", $array_str);

    foreach ($lines as $line) 
    {
        $indentation = (strlen($line) - strlen(ltrim($line))) / 4;

        if ($indentation > $max_indentation) {
            $max_indentation = $indentation;
        }
    }

    return (int) ceil(($max_indentation - 1) / 2) + 1;
}
/**
 * 
 * @param string $separator
 * @param string $string
 * @param int $limit
 * @return array<int,string>
 */
function rexplode(string $separator, string $string,int $limit = PHP_INT_MAX) : array
{
    $arrayReturn=[];     
    if(!empty($separator))
    {
        $array=explode($separator, $string);
        $array=array_reverse($array);
        $count=0;
        foreach ($array as $key => $item)
        {
            if($count<$limit)
            {
                $arrayReturn[$key]=strval($item);
                $count=$key;
            }
            else
            {
                $arrayReturn[$count]=strval($item.$separator.$arrayReturn[$count]);
            }

        } 
    }
    return $arrayReturn;
}

/**
 * 
 * @param array<int,string>|null $arraySeparators
 * @param string $string
 * @return array<int,string>
 */
function kExplode(?array $arraySeparators,mixed $string, int $limit = PHP_INT_MAX) : array
{
    $result=[];
    $the_string="".$string;
    if(is_array($arraySeparators)&&count($arraySeparators)>0&&!empty($arraySeparators[0]))
    {
        $result = explode( $arraySeparators[0], str_replace($arraySeparators, $arraySeparators[0], $the_string),$limit);
    }
    return $result;
}

/**
 * 
 * @param mixed $string
 * @param array<int,string> $acceptedTags
 * @return string
 */
function kPurify(mixed $string,array $acceptedTags=[]) : string
{
    return KPurifier::purify($string,$acceptedTags);
    //return "".$string;
}