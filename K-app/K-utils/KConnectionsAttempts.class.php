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
class KConnectionsAttempts
{
    protected HashMap $mapIp;
    protected HashMap $mapEmail;
    protected string $separator=";";
    protected int $max_nb_attempt=5;
    protected int $max_attempt_by_minute=5;
    
    public function __construct(string $separator="")
    {
        $this->mapIp=new HashMap();
        $this->mapEmail=new HashMap(); 
        
        if(!empty($separator))
        {
            $this->separator=$separator;
        }
        
        $max_nb_attempt=intval(ParamManager::getInstance()->max_nb_attempt_failed);
        if($max_nb_attempt>0)
        {
            $this->setMax_nb_attempt($max_nb_attempt);
        }
        
        $max_attempt_by_minute=intval(ParamManager::getInstance()->max_attempt_failed_by_minute);
        if($max_attempt_by_minute>0)
        {
            $this->setMax_attempt_by_minute($max_attempt_by_minute);
        }        
        
    } 
    public function addConnection(string $connectionLine) : bool
    {
        $connection=explode($this->separator, $connectionLine);
     
        if(count($connection) >= 3)
        {
            $ip=trim($connection[1]);
            $email=trim($connection[0]);
            
            $arrayIp=$this->mapIp->get($ip);
            
            if(is_null($arrayIp))
            {
                $this->mapIp->put($ip,[new ConnectionAttempt($connection[0],$connection[1],$connection[2])]);
            }
            else
            {
                $arrayIp[]=new ConnectionAttempt($connection[0],$connection[1],$connection[2]);
                $this->mapIp->putOrReplace($ip, $arrayIp);
            }
            
            
            $arrayEmail=$this->mapEmail->get(trim($connection[0]));
            
            if(is_null($arrayEmail))
            {
                $this->mapEmail->put($email,[new ConnectionAttempt($connection[0],$connection[1],$connection[2])]);
            }
            else
            {
                $arrayEmail[]=new ConnectionAttempt($connection[0],$connection[1],$connection[2]);
                $this->mapEmail->putOrReplace($email, $arrayEmail);
            }            
            
            return true;
        }
        return false;
    }
    public function canConnectByIp(string $ip) : bool
    {
        $attempts = $this->mapIp->get(trim($ip));        
        if(!is_array($attempts))
        {
            return true;
        }
        return !$this->hasTooManyRecentAttempts($attempts);
    }
    public function canConnectByEmail(string $email) : bool
    {
        $attempts = $this->mapEmail->get(trim($email));        
        if(!is_array($attempts))
        {
            return true;
        }
        return !$this->hasTooManyRecentAttempts($attempts);
    }   
    
    /**
     * 
     * @param array<int,ConnectionAttempt> $attempts
     * @return bool
     */
    protected function hasTooManyRecentAttempts(array $attempts): bool
    {
        $maxAttempts = $this->getMax_nb_attempt();
        $windowSeconds = $this->getMax_attempt_by_minute() * 60;
        $now = time();
        $count = 0;

        foreach ($attempts as $attempt)
        {
            KDebugger::getInstance()->dump($attempt,"Cehcking");
            $timestamp = strtotime(date('Y-m-d') . ' ' . $attempt->date);

            if ($timestamp === false)
            {
                continue;
            }

            if (($now - $timestamp) <= $windowSeconds)
            {
                $count++;

                if ($count >= $maxAttempts)
                {
                    return true;
                }
            }
        }

        return false;
    }
    
    public function getMax_nb_attempt(): int
    {
        return $this->max_nb_attempt;
    }

    public function getMax_attempt_by_minute(): int
    {
        return $this->max_attempt_by_minute;
    }

    public function setMax_nb_attempt(int $max_nb_attempt): void
    {
        $this->max_nb_attempt = $max_nb_attempt;
    }

    public function setMax_attempt_by_minute(int $max_attempt_by_minute): void
    {
        $this->max_attempt_by_minute = $max_attempt_by_minute;
    }


}

class ConnectionAttempt
{
    public string $email="";
    public string $date="";
    public string $ip="";
    public function __construct(string $email,string $ip,string $date)
    {
        $this->email=trim($email);
        $this->date=trim($date);
        $this->ip=trim($ip);
    }    
}
