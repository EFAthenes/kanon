<?php declare(strict_types=1);
// LIST VARIABLE NEEDED BY THE FRAMEWORK

//##############################################################################
//
//              LOCAL PARAM
//
//##############################################################################


ParamManager::getInstance()->add("PERSO","VARIABLE");


//##############################################################################
//
//              ParamManager 
//
//##############################################################################

ParamManager::getInstance()->debug=false;

// DEBUG
//#######################
if(ParamManager::getInstance()->debug)
{
	
}
else
{
	
}

// SITE NAMES
//#######################
ParamManager::getInstance()->app_name="Kanon Framework";
ParamManager::getInstance()->site_title="Kanon Framework";
ParamManager::getInstance()->debug=false;
ParamManager::getInstance()->site_session_name="KANON_SESSION";


// URLS
//#######################
ParamManager::getInstance()->site_root="https://kanon.efa.gr/index.php";
ParamManager::getInstance()->site_action="https://kanon.efa.gr/action.php";
ParamManager::getInstance()->server_url="https://kanon.efa.gr";
ParamManager::getInstance()->server_name="Kanon";


//DATABASE
//#######################
ParamManager::getInstance()->sql_engine=Sql::$NO_DB;
ParamManager::getInstance()->sql_host="localhost";
ParamManager::getInstance()->sql_user="user";
ParamManager::getInstance()->sql_database="database";      
ParamManager::getInstance()->sql_pass="password";


//ANALYTICS
//#######################


// EMAILS
//#######################
ParamManager::getInstance()->error_email="error_email@domain.tld"; 
ParamManager::getInstance()->public_email="public_email@domain.tld";


// EMAIL EXCHANGE PARAM
//#######################
ParamManager::getInstance()->email_server=""; //"{domain.tld:993/imap/ssl/novalidate-cert}"; // using IMAP for storing with self signed
ParamManager::getInstance()->email_smtp_server="domain.tld";
ParamManager::getInstance()->email_transport="smtp"; // smtp gmail null
ParamManager::getInstance()->email_mail="public_email@domain.tld";   
ParamManager::getInstance()->email_username="DOMAIN\public_email";
ParamManager::getInstance()->email_password="password";
ParamManager::getInstance()->email_port="587"; // 25 587 ... 
ParamManager::getInstance()->email_encryption="ssl"; // tls, ssl, or null
ParamManager::getInstance()->email_auth_mode="login"; // plain, login, cram-md5, ntlm or null.
ParamManager::getInstance()->email_verify_peer="true"; // if self sign certificate set false

// LOGS
//#######################
ParamManager::getInstance()->log_dir_mail="/tmp/";
ParamManager::getInstance()->log_directory="/tmp/";

KRoute::overideKrouteIdentifier("r");

ParamManager::getInstance()->add("LANGUAGES","fr,en,el,it,es");//");
ParamManager::getInstance()->add("DEFAULT_LANG","en"); /* auto (default) or set one language  */

ParamManager::getInstance()->add("APP_VERSION","1.0.1");
