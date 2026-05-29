<?php

require_once('./K-app/include.php');
ini_set('memory_limit','16048M');


$folderApp=KManageDb::getAppFolder();

$cli=new KCli();
$cli->setColors(KCli::$F_GREEN);
//echo print_r($cli);
$cli->printString("#############################################################".KCli::br());
$cli->printString("#                          K-PROJECT                        #".KCli::br());
$cli->printString("#############################################################".KCli::br());

$folder=new KFile($folderApp);


if($folderApp==""||!$folder->exists()||!$folder->isDirectory())
{
    $cli->printStringCustom(KCli::br()."Folder App not found => |".$folderApp."|".KCli::br(3),KCli::$F_RED);
    exit();
}

if(count($argv)<2)
{
    $cli->possibleCommandsUsersAndGroups();    
    exit();
}
else
{
    $include=new KIncludeDirectory($folderApp."/config/");
    $include->includeAllDirectory();    
    $include=new KIncludeDirectory($folderApp.KFile::separator()."db".KFile::separator());
    $include->includeAllDirectory();    

    if($argv[1]=="add_user")
    {
        if(count($argv)<5)
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD AN EMAIL | PASSWORD | GROUP_ID".KCli::br(),KCli::$F_RED);
            exit();
        }
        $email=$argv[2];
        $password=$argv[3];
        $groupe_id=$argv[4];
        
        $groupe=new Kapp_Groups();
        if(!$groupe->initById($groupe_id))
        {
            $cli->printStringCustom("ERROR GROUP DOESNT EXIST IN THE DB | id =>".$groupe_id.KCli::br(),KCli::$F_RED);
            exit();              
        }
        
        $user= new Kapp_Users();
        
        if($user->isThisEmailPresentInDb($email))
        {
            $cli->printStringCustom("ERROR EMAIL ALREADY IN THE DB =>".$email.KCli::br(),KCli::$F_RED);
            exit();            
        }
        
        if(!$user->setEmail($email))
        {
            $cli->printStringCustom("ERROR EMAIL IS NOT CORRECT =>".$email.KCli::br(),KCli::$F_RED);
            exit();             
        }
        
        $user->setPasswordHash($password);
 
        if($user->insert())
        {        
            $groupeLink= new Klink_Kapp_Users_Groups();
            $groupeLink->setFk_id_kapp_users($user->getId());
            $groupeLink->setFk_id_kapp_groups($groupe->getId());
            $groupeLink->insert();
            
            //$groupe=new Kapp_Groupes();
            
            $cli->printString("USER INSERT OK => Username : ".$user->getEmail()." | Password : ".$password." ".KCli::br());
            exit(); 
        }
        
        else
        {
            $cli->printStringCustom("ERROR EMAIL IS NOT CORRECT =>".$email.KCli::br(),KCli::$F_RED);
            exit(); 
        }
    }
    else if($argv[1]=="change_user_psswd")
    {
        if(count($argv)<4)
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD AN EMAIL | PASSWORD ".KCli::br(),KCli::$F_RED);
            exit();
        }
        
        $email=$argv[2];
        $password=$argv[3]; 
        
        $user= new Kapp_Users();
        
        if(!$user->setEmail($email))
        {
            $cli->printStringCustom("ERROR EMAIL IS NOT CORRECT =>".$email.KCli::br(),KCli::$F_RED);
            exit();             
        }
        
        if(!$user->initByEmail($email))
        {
            $cli->printStringCustom("ERROR EMAIL NOT IN THE DB =>".$email.KCli::br(),KCli::$F_RED);
            exit();            
        }
        
        
        $user->setPasswordHash($password);
        
        if($user->updateInBd())
        {
            $cli->printString("USER PASSWD UPDATED !!! ".KCli::br());
            exit();
        }
        else
        {
            $cli->printStringCustom("ERROR WITH DB =>".$user->getKerror().KCli::br(),KCli::$F_RED);
            exit();            
        } 
    }
    else if($argv[1]=="add_group")
    {
        if(count($argv)<4)
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD AN LABEL | DESCRIPTION ".KCli::br(),KCli::$F_RED);
            exit();
        }
        $label=$argv[2];
        $description=$argv[3];

        $db=new DbList(Kapp_Groups::class);
        
        if($db->getNb([new QueryField(Kapp_Groups::$LABEL,$label,QueryField::$EQUAL)])>0)
        {
            $cli->printStringCustom("GROUP LABEL ALREADY EXISTS IN DB =>".$label.KCli::br(),KCli::$F_RED);
            exit();              
        }
        
        $group=new Kapp_Groups();
        $group->setLabel($label);
        $group->setDescription($description);
 
        if($group->insert())
        {
            $cli->printString("GROUP INSERT OK => Label : ".$group->getLabel()." | Id : ".$group->getId()." ".KCli::br());
            exit(); 
        }
        else
        {
            $cli->printStringCustom("ERROR WITH DB =>".$group->getKerror().KCli::br(),KCli::$F_RED);
            exit(); 
        }
    }
    else if($argv[1]=="list_users")
    {
        $db=new DbList(Kapp_Users::class);
        $list= $db->getByArray();
        /* @var $user Kapp_Users */
        foreach($list as $user)
        {
            $groupString="";
            $map=$user->getMap_Klink_Kapp_Users_Groups();
            /* @var $user Klink_Kapp_Users_Groups */
            foreach($map as $klink_users_groups)
            {
                if($groupString!="")
                {
                    $groupString.=" - ";
                }
                $groupString.=$klink_users_groups->getFk_id_kapp_groups();
            }
            //$user->get
            //$groupString
            $cli->printStringCustom($cli->strToNbChar($user->getId())."|".$cli->strToNbChar($user->getEmail())."|".$cli->strToNbChar($groupString)."|".$cli->strToNbChar($user->getDate_modified())."|".$cli->strToNbChar($user->getDate_created())."|".KCli::br(),KCli::$F_CYAN);
        }        
    }
    else if($argv[1]=="list_groups")
    {
        $db=new DbList(Kapp_Groups::class);
        $list= $db->getByArray();
        /* @var $group Kapp_Groups */
        foreach($list as $group)
        {
            $cli->printStringCustom($cli->strToNbChar($group->getId())."|".$cli->strToNbChar($group->getLabel())."|".$cli->strToNbChar($group->getDescription())."|".$cli->strToNbChar($group->getDate_modified())."|".$cli->strToNbChar($group->getDate_created())."|".KCli::br(),KCli::$F_CYAN);
        }
    }    
    else if($argv[1]=="test_email")
    {
        if(count($argv)<3)
        {
            $cli->printStringCustom("ERROR YOU SHOULD ADD AN EMAIL ".KCli::br(),KCli::$F_RED);
            exit();
        }  
        $email_to=$argv[2];
        if(!filter_var($email_to, FILTER_VALIDATE_EMAIL))
        {
            $cli->printStringCustom("ERROR YOU SHOULD PUT A CORRECT EMAIL ADRESS =>".$email_to.KCli::br(),KCli::$F_RED);
            exit();            
        }
        
        $mail=new KMail();
        $mail->setActiveLogs(false);
        $mail->setExternalScript(false);
        $mail->setStoreEmail(false);
        $cli->printStringCustom("SENDING ..... ".KCli::br(),KCli::$B_CYAN);
        //Param
        if($mail->sendHTMLEmail("message in <b>html</b>", ParamManager::getInstance()->email_mail,$email_to, "Mail Test from ".ParamManager::getInstance()->server_url))
        {
            $cli->printStringCustom("SENT SUCCESSFULLY!".KCli::br(),KCli::$F_GREEN);
        }
        else
        {
            $cli->printStringCustom("ERROR WITH THE CONFIGURATION => ".$mail->getError().KCli::br(),KCli::$F_RED);
        }
    }    
    else
    {
        $cli->printStringCustom(KCli::br()."Command not FOUND => ".$argv[1].KCli::br(3),KCli::$F_RED); 
        $cli->possibleCommandsUsersAndGroups();        
        exit();
    }  
}