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
class KCache
{
    protected string $last_cached_key="";
    protected string $path_cache="";
    private static ?self $instance =null;
    public static string $FOLDER_CACHE="cache";
    private string $ext=".kache";
    private int $cache_type=1;
    private mixed $redis=null;
    private string $redisVersion="";
    private string $errorString="";
    
    public const int FILE_CACHE=1;
    public const int REDIS_CACHE=2;
    
    public function __construct(?string $path="")
    {
        $path_init=false;
        // Specific Cache File Folder Code initialization
        if(!is_null($path) && $path!="")
        {
            $kPath=new KFile($path);
            if($kPath->exists()&&$kPath->isDirectory())
            {
                $this->path_cache=$path;
                $path_init=true;
            }
        }
        if(!$path_init)
        {
            $this->initDefaultAppFolder(); 
        }
        if(!empty(ParamManager::getInstance()->redis_hostname))
        {        
            $this->initByParamRedis();
        }
    }
    
    private function initDefaultAppFolder() : void
    {  
        //DEFAULT Folder cache
        $this->path_cache=ParamManager::getInstance()->app_folder.KFile::separator().self::$FOLDER_CACHE;
        $kPath=new KFile($this->path_cache);
        if(!$kPath->exists())
        {
            $kPath->mkdir();
        }        
    }  
    
    private function initByParamRedis() : void
    {
        if($this->isRedisExtensionInstalled())
        {
            // ext installed
            if($this->initRedisConnection(
                    ParamManager::getInstance()->redis_hostname, 
                    $this->paramToInt(ParamManager::getInstance()->redis_port), 
                    ParamManager::getInstance()->redis_username, 
                    ParamManager::getInstance()->redis_password, 
                    $this->paramToInt(ParamManager::getInstance()->redis_dbindex), 
                    $this->paramToFloat(ParamManager::getInstance()->redis_timeout), 
                    $this->paramToFloat(ParamManager::getInstance()->redis_read_timeout), 
                    $this->paramToInt(ParamManager::getInstance()->redis_retry_interval)))
            {
                $this->cache_type=self::REDIS_CACHE;
            }
        }        
    }
    
    private function isRedisCache() : bool
    {
        //echo $this->cache_type . "<br />";
        if( $this->cache_type==self::REDIS_CACHE)
        {
            return true;
        }
        return false;
    }
    
    private function paramToFloat(string $s) : float
    {
        $f=floatval($s);
        if("".$f==$s)
        {
            return $f;
        }
        return 0.0;
    }
    
    private function paramToInt(string $s) : int
    {
        $i= intval($s);
        if("".$i==$s)
        {
            return $i;
        }
        return 0;        
    }    
    
    public function isRedisExtensionInstalled() : bool
    {
        $this->redisVersion="".phpversion('redis');
        if(!empty($this->redisVersion))
        {
            return true;
        }           
        return false;
    }
    
    public function initRedisConnection(string $host='127.0.0.1',int $port=6379,string $username="",string $password="",int $dbindex=0,float $timeout=0.0,float $read_timeout=0.0,int $retry_interval=0) : bool
    {
        $this->redis = new Redis();
        //Connecting to Redis
        //$this->redis->connect($host,$port,$timeout,'');
        $connect=$this->redis->connect($host, $port, $timeout, '', $retry_interval, $read_timeout);
        
        if(!$connect)
        {
            $this->errorString.="REDIS connection error";
            return false;
        }
        
        if(!empty($password)&&empty($username))
        {
            if(!$this->redis->auth($password))
            {
                $this->errorString.="REDIS wrong password";
                return false;                
            }
        }
        elseif(!empty($password)&&!empty($username))
        {
            if(!$this->redis->auth([$username,$password]))
            {
                $this->errorString.="REDIS wrong username//password";
                return false;                
            }
        }

        if ($this->redis->ping()) 
        {
            if($this->redis->select($dbindex))
            {
                return true;
            }
            else
            {
                $this->errorString.="REDIS didn't change DB index";
                return false;   
            }            
        }   
        else
        {
            $this->errorString.="REDIS didn't reply to ping";
            return false;   
        }
    }
    
    
    public function getPath_cache() : string
    {
        return $this->path_cache;
    }
    
    public function getLastCacheKey() : string
    {
        return $this->last_cached_key;
    }

        
    public static function getInstance() : KCache
    {
        if(is_null(KCache::$instance))
        {
            KCache::$instance=new KCache();
        }
        return KCache::$instance;
    }    
    
    //##########################################################################
    
    public function isValueCached(string $key_name) : bool
    {
        $this->last_cached_key=$key_name;
        
        if($this->isRedisCache())
        {
            if($this->redis->exists($this->last_cached_key)>0)
            {
                return true;
            }
        }
        else
        {
            $file=new KFile($this->getCachedFileFullPath());
            //echo $file->getPath() . "<br />";
            if($file->exists()&&$file->isFile())
            {
                return true;
            }
        }
        return false;     
    }
       
    /**
     * 
     * @param array<mixed,mixed> $arrayTemp
     * @param string|null $key_name
     * @return bool
     */
    public function makeCacheFromArray(array $arrayTemp,?string $key_name=null) : bool
    {
        //Replace key
        if(!is_null($key_name)&& strlen($key_name)>0)
        {
            $this->last_cached_key=$key_name;
        }
        
        if($this->isRedisCache())
        {                  
            return $this->cacheOnRedis($arrayTemp); 
        }  
        return $this->cacheOnFile($arrayTemp); 
    }
    
    public function makeCacheFromInt(int $nb,?string $key_name=null) : bool
    {
        if(!is_null($key_name)&& strlen($key_name)>0)
        {
            $this->last_cached_key=$key_name;
        }
        
        if($this->isRedisCache())
        {                  
            return $this->cacheOnRedis($nb); 
        }  
        else
        {
            return $this->cacheOnFile($nb); 
        }
    }   
    
    private function makeRandomName() : string
    {
        $string=$this->path_cache.KFile::separator().KRandom::makeRandomString(80).".random";
        return $string;
    }
    
    public function makeCacheFromString(string $string,?string $key_name=null,bool $encoding=true) : bool
    {
        if(!is_null($key_name)&& strlen($key_name)>0)
        {
            $this->last_cached_key=$key_name;
        }
        //Checl this
        if(!$encoding)
        {
            //FILE_APPEND|
            //$tempName=KRandom::
            $status=false;
            $rand=$this->makeRandomName();
            if(file_put_contents($rand,$string,LOCK_EX))
            {
                $status=rename($rand,$this->getCachedFileFullPath());
            }
            return $status;
        }

        if($this->isRedisCache())
        {                  
            return $this->cacheOnRedis($string); 
        }  
        else
        {
            return $this->cacheOnFile($string); 
        }     
    } 
    
    /**
     * 
     * @return array<mixed,mixed>|null
     */
    public function makeArrayFromCache() : ?array
    {
        $array=$this->unCacheFile();
        if(!is_null($array)&& is_array($array))
        {
            return $array;
        }
        return null;
    }
    
    public function makeIntFromCache() : ?int
    {
        $int=$this->unCacheFile();
        if(!is_null($int)&& isInteger($int))
        {
            return $int;
        }
        return null;        
    }   

    public function makeStringFromCache() : ?string
    {
        $string=$this->unCacheFile();
        if(!is_null($string)&& is_string($string))
        {
            return $string;
        }       
        return null;
    } 
    /*
    private function getCachedFileFullPath() : string
    {
        return $this->path_cache.KFile::separator().$this->last_cached_key.$this->ext;
    }
    */

    private function getCachedFileFullPath() : string
    { 
        $cache_small_path="";
        if(str_contains("-", $this->last_cached_key))
        {
            $paths_1=$this->explodeFirst("-",$this->last_cached_key);
            $paths_2=$this->explodeFirst("_",$paths_1[1]);
            $cache_small_path=$paths_1[0].KFile::separator().$paths_2[0];
        }
        else
        {
            $paths_1=$this->explodeFirst("_",$this->last_cached_key);
            $cache_small_path=$paths_1[0].KFile::separator();
        }
        $cache_full_path=$this->path_cache.KFile::separator().$cache_small_path;
        if (!is_dir($cache_full_path)) 
        {
            mkdir(directory : $cache_full_path,
                  recursive:true);
        }
        return $this->path_cache.KFile::separator().$cache_small_path.KFile::separator().$this->last_cached_key.$this->ext;
    }
    
    /**
     * 
     * @return array<int,string>
     */
    private function explodeFirst(string $delimiter,string $string ) : array
    {
        $pos = strpos($string, $delimiter);
        $result=[];
        if ($pos !== false) 
        {
            $result = [
                substr($string, 0, $pos),
                substr($string, $pos + 1)
            ];
        } 
        else 
        {
            $result = [$string, null];
        }
        return $result;
    }

    private function unCacheFile() : mixed
    {   
        if($this->isRedisCache())
        {                  
            return $this->unSerializeAndDecompress($this->redis->get($this->last_cached_key)); 
        }  
        else
        {
            return $this->unSerializeAndDecompress(file_get_contents($this->getCachedFileFullPath())); 
        }        
    }
    
    private function unSerializeAndDecompress(mixed $mixed) : mixed
    {

        $unSerialized=null;
        if(function_exists("igbinary_serialize"))
        {
            $unSerialized=igbinary_unserialize(gzuncompress(base64_decode($mixed)));
        }
        else
        {
            $unSerialized=unserialize(gzuncompress(base64_decode($mixed)));
        }       
        return $unSerialized;    

    }
    
    private function serializeAndCompress(mixed $mixed) : string
    {
        if(function_exists("igbinary_serialize"))
        {
            $serialized=igbinary_serialize($mixed);
        }
        else
        {
            $serialized=serialize($mixed);
        }       
        return base64_encode(gzcompress($serialized,9));       

    }
    
    private function cacheOnRedis(mixed $mixed) : bool
    {
        return $this->redis->set($this->last_cached_key,$this->serializeAndCompress($mixed));    
    }    
        
    private function cacheOnFile(mixed $mixed) : bool
    {    
        $status=false;
        $rand=$this->makeRandomName();
        if(file_put_contents($rand,$this->serializeAndCompress($mixed),LOCK_EX))
        {
            $status=rename($rand,$this->getCachedFileFullPath());
        }
        return $status;        
//        /FILE_APPEND|
//        if(file_put_contents($this->getCachedFileFullPath(),$this->serializeAndCompress($mixed),LOCK_EX)>0)
//        {
//            return true;
//        }
//        return false;
    }
    
    public function cleanAll() : bool
    {
        $status=true;
        $kPath=new KFile($this->path_cache);
        if($kPath->exists()&&$kPath->isDirectory())
        {
            $kPath->emptyDirectory();
        }  
        
        if($kPath->listFilesToList()->getSize()!=0)
        {
            $this->errorString.="CACHE PATH is not empty => ".$this->path_cache;
            $status=false;               
        }
        
        if($this->isRedisCache())
        { 
            if(!$this->redis->flushDb())
            {
                $this->errorString.="REDIS Db is not empty ";
                $status=false;                   
            }
        }
        return $status;
    }
    
    public function replaceCharForDates(string $string) : string
    {
        return str_replace(":","-",str_replace(" ","_",$string));
    }
}
