<?php
define('USE_CACHE',false);
require_once('../include.php');
KDebugger::getInstance()->enable();

$br="<br />";

$arrayFields=["KFieldBool","KFieldDate","KFieldDateTime","KFieldDouble","KFieldFloat","KFieldGeometry","KFieldInteger","KFieldText","KFieldTime","KFieldTimeStamp","KFieldUnKnown","KFieldVarChar","KFieldYear"];

$arrayValues=[0,1,"0","1",false,true,"null","NULL",null,NULL,14,15,-1,
    "2015-03-01","2015-15-01",15.02,"15.02",-15.02,15.12345678,15.1234567890123456789,
    "2015",2015,"2001-03-10 17:16:18","2001-03-10 25:16:18","03:12:46","25:13:46",'',"",
    '{"type":"Polygon","coordinates":[[[22.500819,38.482181],[22.501334,38.482542],[22.501527,38.482357],[22.501012,38.482021],[22.500819,38.482181]]]}',
    '{',
    ];

foreach ($arrayFields as $field)
{
    /* @var $fieldInstance KField */
    $fieldInstance=new $field();
    
    $fieldInstance->setIs_null(true);
    
    echo "<b>Type => ".$fieldInstance->getType()."</b>".$br;
    
    foreach ($arrayValues as $value)
    {
        if($fieldInstance->set($value))
        {
            echo "<span style='color:green'>".$value."//".$fieldInstance->get()."->".$fieldInstance->getValueInString()."</span>".$br;
        }
        else
        {
            echo "<span style='color:red' >".$value."</span>".$br;
        }
    }
    
    echo "----------------".$br;
    
}
