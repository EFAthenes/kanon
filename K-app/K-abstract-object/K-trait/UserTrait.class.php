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
trait UserTrait 
{
    private bool $tooManyAttempts=false;
    /**
     * 
     * @var array<int,int>|null
     */
    private ?array $array_of_groups=null;    
    private ?Kapp_Users_Connections $connection=null;
    
    public function getConnection() : ?Kapp_Users_Connections
    {
        return $this->connection;
    }    

    public function makePassword() : void
    {
        // 2010-02-11 12:23:57    
        $this->setPassword($this->createPassword());
    }

    public function createPassword(): string
    {
        $len=12;
        $base="ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789";
        $max=strlen($base)-1;
        $activatecode="";
        mt_srand((int)((float)microtime()*1000000));
        while(strlen($activatecode)<$len)
        {
            $activatecode.=$base[mt_rand(0,$max)];
        }
        return $activatecode;
    }

    public function makeHash(mixed $input): string
    {
        return password_hash("".$input,PASSWORD_DEFAULT);
    }

    public function getPasswordHash(): string
    {
        return $this->makeHash($this->getPassword());
    }

    public function setPasswordHash(mixed $password) : void
    {
        $this->setPassword($this->makeHash($password));
    }

    public function connectUser(string $email,string $password): bool
    {
        
        if(!$this->isAttemptAllowed($email))
        {
            $this->setKerror("Kapp_Users::connectUser() => FALSE == too many attempts");
            return false;
        }

        $db=new DbList(Kapp_Users::class);
        $query_email=new QueryField(Kapp_Users::$EMAIL,$email,QueryField::$EQUAL);
        $list=$db->getByArray(array($query_email));
        if($list->getSize())
        {
            /* @var $user Kapp_Users */
            foreach($list as $user)
            {
                //echo "EMAIL =>".$email." // ".$password."<br />";
                //echo $user->toWebString();
                if(!empty($password)&&!empty($user->getPassword())&&password_verify($password,$user->getPassword()))
                {
                    if($this->initById($user->getId()))
                    {
                        return $this->initConnection($user);
                    }
                    else
                    {
                        $this->setFailAttemptConnection($email);
                        $this->setKerror("Kapp_Users::connectUser() => FALSE == SQL ERROR :".$this->getKerror());
                        break;
                    }
                }
                else
                {
                    $this->setFailAttemptConnection($email);
                    $this->setKerror("Kapp_Users::connectUser() => FALSE == Password doesnt match :".$password);
                    break;
                }
            }
        }
        else
        {
            $this->setFailAttemptConnection($email);
            $this->setKerror("Kapp_Users::connectUser() => FALSE == NO user with this email :".$email);
        }
        return false;
    }
    
    public function initConnection(?Kapp_Users $user=null) : bool
    {
        if(is_null($user))
        {
            $user=$this;
        }
        $this->connection=new Kapp_Users_Connections();
        $this->connection->setFk_id_kapp_users($user->getId());
        $remote =new KRemoteAddress();
        $this->connection->setIp($remote->getIpAddress());
        if(!$this->connection->insert())
        {
            $this->setKerror("Kapp_Users::connectUser() => insert Connection == SQL ERROR :".$this->connection->getKerror());
            return false;                          
        }
        return true;
    }

    public function isThisEmailPresentInDbNotMine(mixed $email): bool
    {
        $db=new DbList(Kapp_Users::class);

        $query_email=new QueryField(Kapp_Users::$EMAIL,$email,QueryField::$EQUAL);
        $query_not_id=new QueryField(Kapp_Users::$ID,$this->getId(),QueryField::$NOT_EQUAL);

        if($db->getNb(array($query_email,$query_not_id)))
        {
            return true;
        }
        return false;
    }

    public function isThisEmailPresentInDb(mixed $email): bool
    {
        $db=new DbList(Kapp_Users::class);

        $query_email=new QueryField(Kapp_Users::$EMAIL,$email,QueryField::$EQUAL);

        if($db->getNb(array($query_email))>0)
        {
            return true;
        }
        return false;
    }

    public function setEmail(mixed $email,bool $verification=true): bool
    {
        $the_email=trim($email."");
        if(!filter_var($the_email,FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        return parent::setEmail($the_email,$verification);
    }

    public function initByEmail(): bool
    {
        $status=false;
        $dbList=new DbList(self::class);
        $list=$dbList->getIdsByArray([new QueryField(static::$EMAIL,$this->getEmail())]);
        foreach($list as $id)
        {
            $status=$this->initById($id);
            break;
        }
        return $status;
    }

    /**
     * 
     * @param bool $reload
     * @return array<int,int>
     */
    public function getGroupsId(bool $reload=false): array
    {
        if(!$reload&&!is_null($this->array_of_groups))
        {
            return $this->array_of_groups;
        }
        $user_group=$this->getMap_Klink_Kapp_Users_Groups(new SqlOrder(Klink_Kapp_Users_Groups::$FK_ID_KAPP_GROUPS,SqlOrder::$ASC));
        /* @var $grp Klink_Kapp_Users_Groups */
        $this->array_of_groups=array();
        foreach($user_group as $grp)
        {
            $this->array_of_groups[]=$grp->getFk_id_kapp_groups();
        }
        return $this->array_of_groups;
    }

    public function insertInGroup(int $id_group): bool
    {
        $groupe_user=new Klink_Kapp_Users_Groups();
        $groupe_user->setFk_id_kapp_users($this->getId());
        $groupe_user->setFk_id_kapp_groups($id_group);
        if($groupe_user->insert())
        {
            return true;
        }
        else
        {
            $this->setKerror($groupe_user->getKerror());
            return true;
        }
    }

    public function getGroupIds(): ArrayList
    {
        $dbList=new DbList(Klink_Kapp_Users_Groups::class);
        $list=$dbList->getDistinctField(Klink_Kapp_Users_Groups::$FK_ID_KAPP_GROUPS,[new QueryField(Klink_Kapp_Users_Groups::$FK_ID_KAPP_USERS,$this->getId())]);
        return $list;
    }

    public function isInGroup(mixed $the_id_group): bool
    {
        $db=new DbList(Klink_Kapp_Users_Groups::class);
        
        $id_group=intval($the_id_group);

        if(isInteger($this->getId())&&isInteger($id_group) && $id_group>0&& $this->getId()>0)
        {
            $query_user=new QueryField(Klink_Kapp_Users_Groups::$FK_ID_KAPP_USERS,$this->getId(),QueryField::$EQUAL);
            $query_group=new QueryField(Klink_Kapp_Users_Groups::$FK_ID_KAPP_GROUPS,$id_group,QueryField::$EQUAL);
            if($db->getNb(array($query_user,$query_group))>0)
            {
                return true;
            }
        }
        return false;
    }
    
    public function isConnected() : bool
    {
        return ($this->getId()>0&&!empty($this->getEmail()));
    }

    public static function isUserAdmin(): bool
    {
        if(self::isUserInGroup(Kapp_Groups::ADMIN_GROUP))
        {
            return true;
        }
        return false;
    }

    public static function isUserConnected(): bool
    {
        $user=SessionMemory::getInstance()->getUser();
        if($user->isConnected())
        {
            return true;
        }
        return false;
    }
    
    public static function isUserInGroup(mixed $groupNumber): bool
    {
        $number=intval($groupNumber); 
        if($number<=0)
        {
            return false;
        }
        $user=SessionMemory::getInstance()->getUser();
        if($user->isConnected()&&$user->isInGroup($number))
        {
            return true;         
        }
        return false;
    }
    
    public static function get() : Kapp_Users
    {
        return SessionMemory::getInstance()->getUser();
    }  
    

    /**
     * Renvoie les nom et prénom de l'utilisateur dont l'id est passé en 
     * paramètre sous la forme "Nom, Prénom" (l'email si null)
     * @param int $id
     * @return string
     */
    public static function getNameWithId(int $id): string
    {
        $user=new Kapp_Users();
        if($user->initById($id))
        {
            if(!empty($user->getLast_name())&&!empty($user->getFirst_name()))
            {
                return $user->getLast_name().", ".$user->getFirst_name();
            }
            else
            {
                return $user->getEmail();
            }
        }
        return "No name defined";
    }

    /**
     * Renvoie tous les couples nom/prénom avec l'id comme index
     * [id_user;"Nom, Prénom"]
     * Le passage d'un groupe en paramètre permet de filter par ce dernier
     * @return array<int,array<int,mixed>>
     */
    public static function getAllNameInArray(?int $group=null): array
    {
        $dblist=new DbList(self::class);
        $list=$dblist->getByArray(null,new SqlOrder(self::$LAST_NAME));
        $array=[];
        /* @var $usr Kapp_Users */
        foreach($list as $usr)
        {
            $id=$usr->getId();
            $usrTmp=new Kapp_Users();
            $usrTmp->initById($id);
            if(!is_null($group)&&$usrTmp->isInGroup($group))
            {
                $array[]=[$id,self::getNameWithId($id)];
            }
            else if(is_null($group))
            {
                $array[]=[$id,self::getNameWithId($id)];
            }
        }
        return $array;
    }
    
    public function makeName():string
    {
        return "".$this->getLast_name().", ".$this->getFirst_name();
    }   
    
    
    private const string separator=";";
    private function setFailAttemptConnection(string $email) : bool
    {    
        $remote =new KRemoteAddress();
        $ip=$remote->getIpAddress();    
        $path=KApp::getInstance()->getCacheFolder();
        $filename=$this->getFailConnectionsFilename();    
        $logFail=new KFile($path.KFile::separator().$filename);       
        $line=$email.self::separator.$ip.self::separator.date('H:i:s')."\n";
        $status=$logFail->appendStringInFile($line);        
        return $status;
    }
    
    private function getFailConnectionsFilename() : string
    {
        return "a_fail_connections_".date("Y-m-d").".txt";
    }
    
    public function isAttemptAllowed(string $email) : bool
    {
        $path=KApp::getInstance()->getCacheFolder();
        $filename=$this->getFailConnectionsFilename();    
        $logFail=new KFile($path.KFile::separator().$filename);  
        $remote =new KRemoteAddress();
        $ip=$remote->getIpAddress(); 
        
        $attempts=new KConnectionsAttempts(self::separator);
        
        if($logFail->exists())
        {
            $lines=$logFail->readFileToArray();
            foreach ($lines as $line)
            {
                $attempts->addConnection($line);
            }
            
            if($attempts->canConnectByIp($ip)&&$attempts->canConnectByEmail($email))
            {
                return true;
            }
            $this->tooManyAttempts=true;
            return false;
        }
        return true;
    }
    
    public function tooManyAttempts() : bool
    {
        return $this->tooManyAttempts;
    }
}