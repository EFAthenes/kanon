<?php
/* NOT USED ANYMORE */
//
//function myErrorHandler($errno, $errstr, $errfile, $errline)
//{
//    $debug=true;
////    if (!(error_reporting() & $errno))
////    {
////        // This error code is not included in error_reporting
////        return false;
////    }
//
//    $errorMessage="";
//    switch ($errno) 
//    {
//        case E_USER_ERROR:
//        {
//            $errorMessage.= "<b>My ERROR</b> [$errno] $errstr<br />\n";
//            $errorMessage.= "  Fatal error on line $errline in file $errfile";
//            $errorMessage.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
//            $errorMessage.= "Aborting...<br />\n";
//            break;
//        }
//        case E_USER_WARNING:
//        {
//            $errorMessage.= "<b>My WARNING</b> [$errno] $errstr<br />\n";
//            $errorMessage.= "Fatal error on line $errline in file $errfile";
//            break;
//        }
//        case E_USER_NOTICE:
//        {
//            $errorMessage.= "<b>My NOTICE</b> [$errno] $errstr<br />\n";
//            $errorMessage.= "  Fatal error on line $errline in file $errfile";
//            break;
//        }
//        default:
//        {
//            $errorMessage.= "Unknown error type: [$errno] $errstr<br />\n";
//            $errorMessage.= "  Fatal error on line $errline in file $errfile";
//            break;
//        }
//    }
//
////    if(!$debug)
////    {
////        $url=new KURL();        
////        $mail= new ArchimageMail();
////        $mail->sendEmailErrorPHP("PHP Error :: ".date("Y-m-d")." // ".date("H-m-s-u"),str_ireplace("\n","<br />", $errorMessage."<br />".$url->printURL()));
////        require_once('../K-utils/printToFile.class.php');
////        $print = new printToFile($errorMessage."<br />".$url->printURL(), "/tmp/PHP_ERROR_".date("Y-m-d")."_".date("H-m-s-u").".txt");        
////        exit();
////    }
////    else
////    {
//    //require_once('../K-page/KPage.class.php');
//    if($debug)
//    {
//        $url=new KURL(); 
//        echo "myErrorHandler:: <br />".$errorMessage."<br />".$url->printURL();
//        //require_once('../K-utils/printToFile.class.php');
//        //$print = new printToFile($errorMessage."<br />".$url->printURL(), "/tmp/PHP_ERROR_".date("Y-m-d")."_".date("H-m-s-u").".txt");
//        exit();        
//    }
//    else
//    {
//        $url=new KURL(); 
////        echo $errorMessage."<br />".$url->printURL();
//        //require_once('../K-utils/printToFile.class.php');
//        
//        $mail=new IntranetMail();
//        $mail->sendEmailErrorPHP("Missions Erreur ".date("Y-m-d")."_".date("H-m-s-u"),$errorMessage."<br />".$url->printURL());
//        $print = new printToFile($errorMessage."<br />".$url->printURL(), "/tmp/PHP_ERROR_".date("Y-m-d")."_".date("H-m-s-u").".txt");
//        exit();
//    }
////    }
//    //echo $errorMessage;
//    //echo "PHP ERROR !!";
//    /* Don't execute PHP internal error handler */
//    return true;
//}
////error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_STRICT | E_ALL );
////$old_error_handler = set_error_handler("myErrorHandler");
////error_reporting(E_ALL);
//ini_set('display_errors', 1);        
//
//
////require('php_error.php');
////    \php_error\reportErrors();