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
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class KMail
{
    protected string $email_mail = "archimage.commande@efa.gr";
    protected string $server_name="";
    protected string $log_dir="";
    protected string $email_transport="";
//    protected string $email_mail="";
    protected string $email_username="";
    protected string $email_password="";
    protected string $email_server="";
    protected string $email_port="";
    protected string $email_encryption="";
    protected string $email_auth_mode="";
    protected string $email_verify_peer="";
    protected string $email_smtp_server=""; 
    protected bool $debug=false;
    protected bool $activeLogs=true;
    protected bool $sendInExternalScript=true;
    protected bool $storeSentEmail=false;
    protected string $error="";
     
    public function __construct()
    {
        $this->initByParamManager();       
        if(empty($this->server_name))
        {
            $this->setStoreEmail(false);
        }
    }
    
    private function initByParamManager() : void
    { 
        $this->server_name="".ParamManager::getInstance()->server_name;
        $this->log_dir="".ParamManager::getInstance()->log_dir_mail;   
        
        $this->email_mail="".ParamManager::getInstance()->email_mail;
        $this->email_server="".ParamManager::getInstance()->email_server;
        $this->email_transport="".ParamManager::getInstance()->email_transport;
        $this->email_username="".ParamManager::getInstance()->email_username;
        $this->email_password="".ParamManager::getInstance()->email_password;
        
        $this->email_port="".ParamManager::getInstance()->email_port;
        $this->email_encryption="".ParamManager::getInstance()->email_encryption;
        $this->email_auth_mode="".ParamManager::getInstance()->email_auth_mode;
        $this->email_verify_peer="".ParamManager::getInstance()->email_verify_peer;
        $this->email_smtp_server="".ParamManager::getInstance()->email_smtp_server;
    }
    
    /**
     * 
     * @param array<string,string|null> $options
     * @return void
     */
    public function initByArray(array $options) : void
    {
        foreach ($options as $key => $value)
        {
            if(!empty($key)&&!is_null($value))
            {
                if(property_exists(self::class,$key))
                {
                    $this->$key=$value;
                }
            }
        }
    }
   
    
    
    public function getFrom(): string
    {
        return $this->email_mail;
    }

    public function getServer_name(): string
    {
        return $this->server_name;
    }

    public function getLog_dir(): string
    {
        return $this->log_dir;
    }

    public function getEmail_transport(): string
    {
        return $this->email_transport;
    }

    public function getEmail_mail(): string
    {
        return $this->email_mail;
    }

    public function getEmail_username(): string
    {
        return $this->email_username;
    }

    public function getEmail_password(): string
    {
        return $this->email_password;
    }

    public function getEmail_server(): string
    {
        return $this->email_server;
    }

    public function getEmail_port(): string
    {
        return $this->email_port;
    }

    public function getEmail_encryption(): string
    {
        return $this->email_encryption;
    }

    public function getEmail_auth_mode(): string
    {
        return $this->email_auth_mode;
    }

    public function getEmail_verify_peer(): string
    {
        return $this->email_verify_peer;
    }

    public function getEmail_smtp_server(): string
    {
        return $this->email_smtp_server;
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function setFrom(string $from): void
    {
        $this->email_mail = $from;
    }

    public function setServer_name(string $server_name): void
    {
        $this->server_name = $server_name;
    }

    public function setLog_dir(string $log_dir): void
    {
        $this->log_dir = $log_dir;
    }

    public function setEmail_transport(string $email_transport): void
    {
        $this->email_transport = $email_transport;
    }

    public function setEmail_mail(string $email_mail): void
    {
        $this->email_mail = $email_mail;
    }

    public function setEmail_username(string $email_username): void
    {
        $this->email_username = $email_username;
    }

    public function setEmail_password(string $email_password): void
    {
        $this->email_password = $email_password;
    }

    public function setEmail_server(string $email_server): void
    {
        $this->email_server = $email_server;
    }

    public function setEmail_port(string $email_port): void
    {
        $this->email_port = $email_port;
    }

    public function setEmail_encryption(string $email_encryption): void
    {
        $this->email_encryption = $email_encryption;
    }

    public function setEmail_auth_mode(string $email_auth_mode): void
    {
        $this->email_auth_mode = $email_auth_mode;
    }

    public function setEmail_verify_peer(string $email_verify_peer): void
    {
        $this->email_verify_peer = $email_verify_peer;
    }

    public function setEmail_smtp_server(string $email_smtp_server): void
    {
        $this->email_smtp_server = $email_smtp_server;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
    
    public function __destruct()
    {

    }
    
    public function setActiveLogs(bool $activeLogs) : void
    {
        $this->activeLogs=$activeLogs;
    }
    
    public function setExternalScript(bool $sendInExternalScript) : void
    {
        $this->sendInExternalScript=$sendInExternalScript;
    }
    
    public function setStoreEmail(bool $storeSentEmail) : void
    {
        $this->storeSentEmail=$storeSentEmail;
    }      
    
    //##########################################################################    
    
    public function sendEmailNotification(mixed $subject,mixed $notification) : bool
    {
        return $this->sendHTMLEmail("".$notification,$this->email_mail,ParamManager::getInstance()->error_email,"".$subject);
    }    
    
    public function sendEmailErrorPHP(mixed $subject,mixed $error) : bool
    {
        return $this->sendHTMLEmail("".$error,$this->email_mail,ParamManager::getInstance()->error_email,"".$subject);
    }
      
    public function sendNormalEmail(mixed $to,mixed $subject,mixed $message) : bool
    {
        return $this->sendHTMLEmail("".$message,$this->email_mail,"".$to,"".$subject);
    }

    
    public function sendHTMLEmail(string $message,string $from,string $to,string $subject,?string $attachment_name=null) : bool
    {              
        $message_tmp=$message;
        $subject.=" - ".$this->makeDate();
        $boundary = md5(uniqid(microtime(), TRUE));
        //$boundary = uniqid("HTMLEMAIL");
        $headers = "From: ".$from."\r\n" .  "Subject: " . $subject . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative;" .
                "boundary = $boundary\r\n\r\n";
        $headers .= "This is a MIME encoded message.\r\n\r\n";
        $headers .= "--$boundary\r\n" .
                "Content-Type: text/plain; charset=ISO-8859-1\r\n" .
                "Content-Transfer-Encoding: quoted-printable\r\n\r\n";

        $headers .= chunk_split((strip_tags($message)));

        $headers .= "--$boundary\r\n" .
                "Content-Type: text/html; charset=ISO-8859-1\r\n" .
                "Content-Transfer-Encoding: quoted-printable\r\n\r\n";

        $headers .= $message;
        $headers .= "--$boundary\r\n";
        
        if (!empty($attachment_name)&&file_exists($attachment_name)) 
        {
            //echo "FILE !!1";
            $filename = basename($attachment_name);
            $file_type = filetype($attachment_name);
            $file_size = filesize($attachment_name);

            $handle = fopen($attachment_name, 'r') or die('File '.$filename.'can t be open');
            $content = fread($handle, $file_size);
            $content_base64 = chunk_split(base64_encode($content));
            $f = fclose($handle);

            $headers .= '--'.$boundary."\r\n";
            $headers .= 'Content-type:'.$file_type.';name='.$filename."\r\n";
            $headers .= 'Content-transfer-encoding:base64'."\r\n\r\n";
            $headers .= $content_base64."\r\n";
            $headers .= "--".$boundary."--";
        }

        $directory=new KFile(dirname(__FILE__));
        if($this->activeLogs)
        {
            $this->logging($from,$to,$subject,$message);
        }
        if($this->debug)
        {
            echo "php ".$directory->getPath()."/sendEmailByScript.php \"".addslashes($from)."\" \"".addslashes($to)."\" \"".addslashes($headers)."\" \"".addslashes($subject)."\" \"".addslashes($message_tmp)."\" &";
            return true;
        }
         
        if($this->sendInExternalScript)
        {
            //echo "php ".$directory->getPath()."/sendEmailByScript.php \"".addslashes($from)."\" \"".addslashes($to)."\" \"".addslashes($headers)."\" \"".addslashes($subject)."\" \"".addslashes($message_tmp)."\" > ".$this->log_dir.KFile::separator()."MAIL2_".date("Y-m-d").".log"." &";
            //return true;
            shell_exec("php ".$directory->getPath()."/sendEmailByScript.php \"".addslashes($from)."\" \"".addslashes($to)."\" \"".addslashes($headers)."\" \"".addslashes($subject)."\" \"".addslashes($message_tmp)."\" &");       
            return true;
        }
        else
        {
            return $this->sendAndStore($from,$to,$headers,$subject,$message_tmp);
        }
    }
    
   
    public function makePublicFooter() : string
    {
        $footer="\n\n--\nCordialement,\n&#201;COLE FRAN&#199;AISE D'ATH&#200;NES\n".$this->server_name."";
        return ($footer);
    }
//    
//    private function makeFooter() : string
//    {
//        $footer="<br />
//--<br />
//Cordialement, <br />	
//<br />
//&#201;COLE FRAN&#199;AISE D&#39;ATH&#200;NES<br />
//<br />".$this->server_name."
//";
//        return ($footer);
//    }
    
    private function logging(string $from,string $to,string $title,string $subject) : void
    {
        if(is_writable($this->log_dir))
        {
            $fp = fopen($this->log_dir.KFile::separator()."MAIL_".date("Y-m-d").".txt", "a");
            if($fp!=null)
            {
                fwrite($fp,"\n#########################\n"."FROM:".$from."\nTO:".$to."\nTITLE:".$title."\nSUBJECT:\n".$subject."\n#########################\n");
                fclose($fp);        
            }
        }
    }
    private function makeDate() : string
    {
        return date("Y-m-d H:i:s");
    }
    
    public function sendAndStore(string $from,string $to,string $headers,string $subject,string $message) : bool
    {
        require_once __DIR__.'/../K-composer/vendor/autoload.php';
        $status=false;
        $mbox=null;
        try
        {
            if($this->storeSentEmail)
            {
                if($mbox = imap_open(
                        ParamManager::getInstance()->email_server, 
                        ParamManager::getInstance()->email_username, 
                        ParamManager::getInstance()->email_password, 
                        0, 
                        1,
                        array('DISABLE_AUTHENTICATOR' => 'GSSAPI')))
                {     
                    if( imap_append($mbox, ParamManager::getInstance()->email_server,"To: $to\r\n".$headers))
                    {
                        $status=imap_close($mbox);
                    }
                }
            }

            if(!$this->storeSentEmail||$status)
            {
                $this->symfonyMail($from,$to,$subject,$message);
                $status=true;
            }
        }
        catch (Exception $ex)
        {
            echo "Error => ".$ex->getMessage();
            self::setError("Error exception =>".$ex->getMessage());
        }        
        return $status;  
    }
    
    
    private function makeTransportString() : string
    {
        $user=$this->email_mail;
        if($this->email_auth_mode=="login")
        {
            $user=$this->email_username;
        }
        $transport_string=                
                $this->email_transport.
                '://'.
                $user.
                ':'.
                $this->email_password.
                '@'.
                $this->email_smtp_server.
                ':'.
                $this->email_port.
                '?encryption='.$this->email_encryption.
                '&auth_mode='.$this->email_auth_mode.
                '&verify_peer='.$this->email_verify_peer;
        return $transport_string;      
    }

    public function symfonyMail(string $from,string $to,string $subject,string $message) : bool
    { 
        $transport_string=$this->makeTransportString();
        //echo "\n".$transport_string."\n";
        //exit;
//        echo "\n".$from."\n";
//        echo "\n".$to."\n";
        
        $transport = Transport::fromDsn($transport_string);
        //$transport->setAuthenticators([new NtlmAuthenticator()]);
        $mailer = new Mailer($transport);        

        $email = (new Email())
            ->from($from)
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($message)
            ->html($message);

        $mailer->send($email);        
        return true;
    }    
    
//    public static function swiftMail($from,$to,$subject,$message) : bool
//    {  
//        $transport = (
//            new Swift_SmtpTransport(
//                ParamManager::getInstance()->email_smtp_server, 
//                ParamManager::getInstance()->email_port))
//                ->setUsername(ParamManager::getInstance()->email_username)
//            ->setPassword(ParamManager::getInstance()->email_password);    
//
//        $mailer = new Swift_Mailer($transport);
//
//
//        $message_swift = (new Swift_Message($subject))
//        ->setFrom([$from => $from])
//        ->setTo([$to, $to => $to])
//        ->addPart($message, 'text/html');
//
//
//        // Send the message
//        if($mailer->send($message_swift)>0)
//        {
//            return true;
//        }
//        return false;
//    } 
    
    public function setError(string $s) : void
    {
        $this->error=$s;
    }
    public function getError() : string
    {
        return $this->error;
    }    
}
