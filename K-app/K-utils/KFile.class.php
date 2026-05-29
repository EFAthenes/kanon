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
class KFile
{
    private static string $sep_string="/";
    private static bool $sep_init=false;
    private ?string $absolute_path=null;
    private string $error="";
    
    public function __construct(string $path="")
    {
        $this->absolute_path=$path;
    }  

//    public function  __destruct()
//    {
//        $this->absolute_path=null;
//    }

    public function mkdir() : bool
    {
        if(!file_exists($this->absolute_path))
        {
            $parentDir=$this->getParentKFile();
            if(!$parentDir->exists())
            {
                $parentDir->mkdir();
            }
            if(is_writable($parentDir->getPath()))
            {
                //KDebugger::getInstance()->dump($this->absolute_path);
                if(mkdir($this->absolute_path, 0777,true) && $this->setAllAccess())
                {
                    $this->flushCache();
                    return true;
                }
            }
            return false;
        }
        return true;
    }
    
    public function mkdirs() : bool
    {
        if(!file_exists($this->absolute_path))
        {
            if(mkdir($this->absolute_path, 0777,true) && $this->setAllAccess())
            {
                $this->flushCache();
                return true;
            }
        }
        return true;
    }
    
    /**
     * 
     * @param array<int,string> $extensions
     * @return bool
     */
    public function isExtensions(array $extensions) : bool
    {
        $ext=strtolower($this->getExtension());
        foreach ($extensions as $extension)
        {
            if(strcmp($ext, $extension)===0)
            {
                return true;
            }
        }
        return false;
    }    
    public function isExtension(string $extension) : bool
    {
        $ext=$this->getExtension();
        if(strcmp($ext, $extension)===0)
        {
            return true;
        } 
        return false;
    }
    
    public function getExtension() : string
    {
        $ext="";
        if(string_contains(".",$this->getName()))
        {
            $array=explode(".",$this->getName());
            $ext=$array[count($array)-1];      
        }
        return $ext;
    }
    
    public function getNameWithoutExt() : string
    {
        $name="";
        $array=explode(".",$this->getName());
        for($i=0; $i<count($array)-1 ;$i++)
        {
            if($i>0)
            {
                $name.=".";
            }
            $name.=$array[$i];
        } 
        return $name;        
    }
    
    public function getNameWithoutAllExt() : string
    {
        $array=explode(".",$this->getName());
        return $array[0];
    }    
    
    public function getNameWithoutExtWithoutVersion(mixed $separator) : string
    {
        $name=$this->getNameWithoutExt();
        $array=explode("".$separator,$name);
        return $array[0];
    }    
    
    public static function setLocaleUTF8() : void
    {
        setlocale(LC_ALL, 'en_US.UTF8');
    }
    
    public function delete() : bool
    {
        if($this->exists())
        {
            if($this->isFile())
            {
                unlink($this->absolute_path);
            }
            else if($this->isDirectory())
            {
                $this->removeDirectory($this->absolute_path);
            }
        }
        $this->flushCache();
        return !$this->exists();
    }
    
    public function deleteDirIfEmpty() : bool
    {
        $this->flushCache();
        if($this->exists())
        {
            if($this->isDirectory())
            {
                $list=$this->listFilesNameToList();
                if($list==NULL || $list->getSize()==0)
                {
                    $this->delete();
                }
            }
        }   
        return !$this->exists();
    }

    private function removeDirectory(string $dir) : bool
    {
        $status=false;
        if (is_dir($dir))
        {
            $listFiles = scandir($dir);
            foreach ($listFiles as $fileName)
            {
                if ($fileName != "." && $fileName != "..")
                {
                    if (is_dir($dir.DIRECTORY_SEPARATOR.$fileName))
                    {
                        $this->removeDirectory($dir.DIRECTORY_SEPARATOR.$fileName);
                    }
                    elseif(is_file($dir.DIRECTORY_SEPARATOR.$fileName))
                    {
                        unlink($dir.DIRECTORY_SEPARATOR.$fileName);
                    }
                    elseif(is_link($dir.DIRECTORY_SEPARATOR.$fileName))
                    {
                        unlink($dir.DIRECTORY_SEPARATOR.$fileName);
                    }                    
                }
            }
            //reset($listFiles);
            if(rmdir($dir))
            {
                $status=true;
            }
            $this->flushCache();
        }
        return $status;
    }

    public function createFile() : bool
    {
        if($this->exists())
        {
            return true;
        }

        $fp = fopen($this->absolute_path, 'w');
        if($fp!=null)
        {
            fclose($fp);
        }
        /** @phpstan-ignore-next-line */
        if($this->exists())
        {
            return chmod($this->absolute_path, 0777);
        }
        return false;
    }
    
    public function emptyDirectory() : bool
    {
        if ($this->exists() && $this->isDirectory())
        {
            $listFiles = scandir($this->absolute_path);
            foreach ($listFiles as $fileName)
            {
                if ($fileName != "." && $fileName != "..")
                {
                    if (is_dir($this->absolute_path.DIRECTORY_SEPARATOR.$fileName))
                    {
                        $this->removeDirectory($this->absolute_path.DIRECTORY_SEPARATOR.$fileName);
                    }
                    elseif(is_file($this->absolute_path.DIRECTORY_SEPARATOR.$fileName))
                    {
                        unlink($this->absolute_path.DIRECTORY_SEPARATOR.$fileName);
                    }
                }
            }
            $this->flushCache();
            return true;
        }
        return false;
    }

    // for a file
    public function renameTo(string $newName) : bool
    {
        if(file_exists($this->absolute_path) && !file_exists($newName))
        {
            if(rename ($this->absolute_path ,$newName))
            {
                return true;
            }
        }
        return false;
    }
    
    public function renameByName(string $name) : bool
    {
        if(file_exists($this->absolute_path))
        {
            $path=$this->getParentName();
            $ext=$this->getExtension();
            $new_path=$path.self::separator().$name.".".$ext;
            if(!file_exists($new_path) && rename ($this->absolute_path ,$new_path) )
            {
                return true;
            }
        }
        return false;        
    }
    
    public function renameFileTo(KFile $file) : bool
    {
        return $this->renameTo($file->getPath());
    }    

    // for a directorys
//    public function moveTo() : bool
//    {
//        return false;
//    }
    public function copyReplaceTo(string $file_copy_path) : bool
    {
        if(!$this->exists())
        {
            $this->error="SOURCE FILE DOESN'T EXISTS! ==> ".$this->absolute_path;
            return false;
        }

        if($this->isDirectory())
        {
            $this->recurse_copy($this->absolute_path,$file_copy_path);
        }
        else if($this->isFile())
        {
            $file = new KFile($file_copy_path);
            $dir = $file->getParentKFile();
            if($dir!=null && $dir->mkdirs() && $dir->setAllAccess())
            {
                copy($this->absolute_path,$file_copy_path);
            }    
        }
        else if($this->isLink())
        {
            $this->error="SOURCE IS A LINK! ==> ".$this->absolute_path;
            return false;            
        }

        if(file_exists($file_copy_path))
        {
            return true;
        }
        return false;
    }
    
    public function copyTo(string $file_copy_path,bool $forceReplace=false) : bool
    {
        if(!$this->exists())
        {
            $this->error="SOURCE FILE DOESN'T EXISTS! ==> ".$this->absolute_path;
            return false;
        }

        if(!$forceReplace&&file_exists($file_copy_path))
        {
            $this->error="DESTINATION FILE EXISTS! ==> ".$file_copy_path;
            return false;
        }

        if($this->isDirectory())
        {
            $this->recurse_copy($this->absolute_path,$file_copy_path);
        }
        else if($this->isFile())
        {
            $file = new KFile($file_copy_path);
            $dir = $file->getParentKFile();
            
            if(!$dir->exists()) 
            {
                $dir->mkdirs();
            }    
            else
            {
                $dir->setAllAccess();
            }
            
            if($dir->exists()) 
            {
                return copy($this->absolute_path,$file_copy_path);           
            }
            return false;
            
        }
        else if($this->isLink())
        {
            $this->error="SOURCE IS A LINK! ==> ".$this->absolute_path;
            return false;            
        }


        if(file_exists($file_copy_path))
        {
            return true;
        }
        return false;
    }
    public function copyToKFile(KFile $file,bool $forceReplace=false) : bool
    {
        return $this->copyTo($file->getPath(),$forceReplace);
    }
    
    
    public function safeCopyTo(string $file_copy_path) : bool
    {
        if(!$this->exists())
        {
            $this->error="SOURCE FILE DOESN'T EXISTS! ==> ".$this->absolute_path;
            return false;
        }

        if(file_exists($file_copy_path))
        {
            $file=new KFile($file_copy_path);
            $path=$file->getParentName();
            $name=$file->getNameWithoutExt();
            $ext=$file->getExtension();
            $status=false;
            for($i=1; $i<100 ; $i++)
            {
                if(!file_exists($path."/".$name."_".$i.".".$ext))
                {
                    $file_copy_path=$path."/".$name."_".$i.".".$ext;
                    $status=true;
                    break;
                }
            }
            if(!$status)
            {
                $this->error="DESTINATION FILE EXISTS! ==> ".$file_copy_path;
                return false;                
            }
        }

        if($this->isDirectory())
        {
            $this->recurse_copy($this->absolute_path,$file_copy_path);
        }
        else if($this->isFile())
        {
            $file = new KFile($file_copy_path);
            $dir = $file->getParentKFile();
            if($dir!=null && $dir->mkdirs())
            {
                copy($this->absolute_path,$file_copy_path);
            }    
        }
        else if($this->isLink())
        {
            $this->error="SOURCE IS A LINK! ==> ".$this->absolute_path;
            return false;            
        }


        if(file_exists($file_copy_path))
        {
            @chmod($file_copy_path, 0777);
            return true;
        }
        return false;
    }    
    

    private function recurse_copy(string $src,string $dst) : bool
    {
        $dir = opendir($src);
        if(!file_exists($dst))
        {
            mkdir($dst, 0777,true);
        }
        while(false !== ( $file = readdir($dir)))
        {
            if(( $file != '.' ) && ( $file != '..' ))
            {
                if ( is_dir($src . DIRECTORY_SEPARATOR . $file) )
                {
                    $this->recurse_copy($src .DIRECTORY_SEPARATOR . $file,$dst .DIRECTORY_SEPARATOR . $file);
                }
                elseif(is_file($src . DIRECTORY_SEPARATOR . $file))
                {
                    if(!copy($src . DIRECTORY_SEPARATOR . $file,$dst . DIRECTORY_SEPARATOR . $file))
                    {
                        return false;
                    }
                }
            }
        }
        closedir($dir);
        return true;
    }

    public function length() : string
    {
        if($this->exists())
        {
            return $this->sizeFormat(filesize($this->absolute_path));
        }
        $this->error="SOURCE FILE DOES'NT EXIST! ==> ".$this->absolute_path;
        return "";
    }
    
    public function getFileSize() : int
    {
        $bytestotal = 0;
        if($this->isFile())
        {
            $bytestotal= filesize($this->absolute_path);
        }
        return $bytestotal;
    }
    
    public function getDirectorySize() : string
    {
        return $this->sizeFormat($this->getDirectoryLength());
    }
    
    public function getDirectoryLength() : int
    {
        $bytestotal = 0;
        if($this->isDirectory())
        {
            $path = realpath($this->absolute_path);
            if(is_string($path) && file_exists($path))
            {
                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object)
                {
                    $bytestotal += $object->getSize();
                }
            }
        }
        return $bytestotal;
    }

    /*
    public function getDirectoryStat() : array
    {
        $bytestotal = 0;
        $nb_dirs = 0;
        $nb_files= 0;
        if($this->isDirectory())
        {
            $path = realpath($this->absolute_path);
            if($path!==false && $path!='' && file_exists($path))
            {
                $count=0;
                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object)
                {
                    if($count <10 && $object->getSize()>0)
                    {
                        KDebugger::getInstance()->dump($object);
                        $count++;
                    }
                    $bytestotal += $object->getSize();
                    //if(is_dir($object))
                    if($object->isDir())
                    {
                        $nb_dirs++;
                    }
                    else
                    {
                        $nb_files++;
                    }
                }
            }
        }
        return [$bytestotal,$nb_dirs,$nb_files];
    }   
    */
    /**
     * 
     * @return array<int,int>
     */
    public function getDirectoryStat() : array
    {
        $bytestotal = 0;
        $nb_dirs = 0;
        $nb_files= 0;        
        if($this->isDirectory())
        {
            $this->getSubDirectoryStat($this,$bytestotal,$nb_dirs,$nb_files);
        }
        return [$bytestotal,$nb_dirs,$nb_files];
    }
    
    public function getSubDirectoryStat(KFile $dir,int&$bytestotal,int &$nb_dirs,int &$nb_files) : void
    {
        $items = $dir->listPathsToList();
        foreach ($items as $item)
        {
            if(is_dir($item))
            {
                $nb_dirs++;
                $dir=new KFile($item);
                $this->getSubDirectoryStat($dir,$bytestotal,$nb_dirs,$nb_files);
            }
            else
            {
                $bytestotal+=filesize($item);
                $nb_files++;
            }
        }
    }    

    public function sizeFormat(mixed $the_bytes) : string
    {
        $bytes=intval($the_bytes);
        if ($bytes < 1024)
        {
            return $bytes.' B';
        }
        elseif ($bytes < 1048576)
        {
            return round($bytes / 1024, 2).' KB';
        }
        elseif ($bytes < 1073741824)
        {
            return round($bytes / 1048576, 2).' MB';
        }
        elseif ($bytes < 1099511627776)
        {
            return round($bytes / 1073741824, 2).' GB';
        }
        else
        {
            return round($bytes / 1099511627776, 2).' TB';
        }
    }
    
    /**
     * 
     * @return array<int,string>|null
     */
    public function listFilesNameToArray() : ?array
    {
        $list=$this->listFilesNameToList();
        if(!is_null($list) && $list->getSize()>0)
        {
            return $list->toArray();
        }
        return null;        
    }
    /**
     * 
     * @return array<int,KFile>|null
     */
    public function listFilesToArray() : ?array
    {
        $list=$this->listFilesToList();
        if($list->getSize()>0)
        {
            return $list->toArray();
        }
        return null;
    }
    public function listFilesNameToList() : ?ArrayList
    {
        if($this->exists() && $this->isDirectory() && $this->canRead())
        {
            $list=new ArrayList();
            $dir = opendir($this->absolute_path);

            if($dir===false)
            {
                return null;
            }

            while(false !== ( $file = readdir($dir)))
            {
                if (( $file != '.' ) && ( $file != '..' ))
                {
                    if ( is_dir($this->absolute_path . DIRECTORY_SEPARATOR . $file) || is_file($this->absolute_path . DIRECTORY_SEPARATOR . $file) )
                    {                       
                        $list->add($file);
                    }
                    else
                    {
                        // DUNNO YET
                    }
                }
            }
            closedir($dir);
            if($list->getSize()>0)
            {
                return $list;
            }
        }
        return null;
    }
    public function listFilesToList() : ArrayList
    {
        $list= new ArrayList();
        if($this->exists() && $this->isDirectory() && $this->canRead())
        {
            $map=new HashMap();
            //$list=new ArrayList();
            $dir = opendir($this->absolute_path);
            
            if($dir===false)
            {
                return $list;
            }
            
            while(false !== ( $file = readdir($dir)))
            {
                if (( $file != '.' ) && ( $file != '..' ))
                {
                    //echo "\n ".$file;
                    if ( is_dir($this->absolute_path . DIRECTORY_SEPARATOR . $file) || is_file($this->absolute_path . DIRECTORY_SEPARATOR . $file) )
                    {
                        $kfile=new KFile($this->absolute_path.DIRECTORY_SEPARATOR.$file);
                        //$list->add($kfile);
                        if(!$map->put($file, $kfile))
                        {
                            echo "KFile :: ERROR FILE ALREADY EXIST";
                            exit();
                        }
                    }
//                    else
//                    {
//                        // DUNNO YET
//                    }
                }
            }
            closedir($dir);
            return $map->toArrayList();                     
//            if($list!=null && $list->getSize()>0)
//            {
//                return $list;
//            }
        }
        return $list;
    }
  
    public function listAllPathsToList() : ArrayList
    {
        $list= new ArrayList();
        if($this->exists() && $this->isDirectory() && $this->canRead())
        {
            $this->listSubPathsToList($list,$this->getPath(),true);
        }
        return $list;
    }    
    
    public function listPathsToList() : ArrayList
    {
        $list= new ArrayList();
        if($this->exists() && $this->isDirectory() && $this->canRead())
        {
            $this->listSubPathsToList($list,$this->getPath(),false);
        }
        return $list;
    }
    private function listSubPathsToList(ArrayList $list,string $path,bool $subPath=false) : void
    {
        $dir = opendir($path);
            
        if($dir===false)
        {
            return ;
        }
        while(false !== ( $file = readdir($dir)))
        {
            if (( $file != '.' ) && ( $file != '..' ))
            {
                if(is_dir($this->absolute_path . DIRECTORY_SEPARATOR . $file))
                {
                    $list->add($this->absolute_path . DIRECTORY_SEPARATOR . $file);
                    if($subPath)
                    {
                        $this->listSubPathsToList($list,$this->absolute_path . DIRECTORY_SEPARATOR . $file);
                    }
                }
                else if(is_file($this->absolute_path . DIRECTORY_SEPARATOR . $file))
                {
                    $list->add($this->absolute_path . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }
    
    public function compareTo(string $path) : bool
    {
        return $this->compareToKFile(new KFile($path));
    }
    
    public function compareToKFile(KFile $file,bool $fast=true) : bool
    {
        if($this->isFile()&&$file->isFile()&&$file->getFileSize()==$this->getFileSize())
        {
            if($fast)
            {
                return true;
            }
            else
            {
                if(sha1_file($file->getPath())==sha1_file($this->getPath()))
                {
                    if(md5_file($file->getPath())==md5_file($this->getPath()))
                    {
                        return true;
                    }
                }
            }
//            if($fast)
//            {
//                return $this->identical($file->getPath(),$this->getPath(),);
//            }
//            else if(sha1_file($file->getPath())==sha1_file($this->getPath()))
//            {
//                if(md5_file($file->getPath())==md5_file($this->getPath()))
//                {
//                    return true;
//                }
//            }
        }
        return false;
    }   
    /*
    private function identical($fileOne, $fileTwo)
    {
        $same = true;
        if (! $fp1 = fopen($fileOne, 'rb')) return false;

        if (! $fp2 = fopen($fileTwo, 'rb'))
        {
            fclose($fp1);
            return false;
        }
        while (! feof($fp1) and ! feof($fp2))
        {
            if (fread($fp1, 4096) !== fread($fp2, 4096))
            {
                $same = false;
                break;
            }
        }

        if (feof($fp1) !== feof($fp2)) $same = false;

        fclose($fp1);
        fclose($fp2);

        return $same;
    }    
    */
    
    public function isFilePresentInDir(string $filename) : bool
    {
        if($this->isDirectory() && $filename!="")
        {
            $file=new KFile($this->getPath().self::separator().$filename);
            if($file->exists()&&$file->isFile())
            {
                return true;
            }
        }
        return false;
    }
    
    
    public function getInfoForUnique() : string
    {
        return "MD5 : ".md5_file($this->getPath())." | SHA1 : ".sha1_file($this->getPath())." | Size : ".$this->getFileSize();
    }

    public function exists() : bool
    {
        if(file_exists($this->absolute_path))
        {
            return true;
        }
        return false;
    }

    // getters
    public function getName() : string
    {
        /*
        return basename($this->absolute_path);
         * 
         */

        $separators = '/';
        if (DIRECTORY_SEPARATOR != '/') {

          // For Windows OS add special separator.
          $separators .= DIRECTORY_SEPARATOR;
        }

        // Remove right-most slashes when $uri points to directory.
        $uri = rtrim($this->absolute_path, $separators);

        // Returns the trailing part of the $uri starting after one of the directory
        // separators.
        $matches=null;
        $filename = preg_match('@[^' . preg_quote($separators, '@') . ']+$@', $uri, $matches) ? $matches[0] : '';

//        // Cuts off a suffix from the filename.
//        if ($suffix) {
//          $filename = preg_replace('@' . preg_quote($suffix, '@') . '$@', '', $filename);
//        }
        return $filename;
      
    }

    public function getParentName() : string
    {
        return dirname($this->absolute_path);
    }
    public function getParentOnlyName() : string
    {
        $dir=$this->getParentKFile();
        return $dir->getName();
    }    
    
    public function getPath() : string
    {
        return $this->absolute_path;
    }
    
    public function stripDoubleSeparator(string $separator="/") : void
    {
        $sep=$separator.$separator;
        $this->absolute_path=str_replace($sep,$separator, $this->absolute_path);
    }    

    public function setPath(string $path) : void
    {
        $this->absolute_path=$path;
    }

    public function isFile() : bool
    {
        return (is_file($this->absolute_path));
    }

    public function isDirectory() : bool
    {
        return (is_dir($this->absolute_path));
    }

    public function isLink() : bool
    {
        return (is_link($this->absolute_path));
    }

    public function isHidden() : bool
    {
        $name=$this->getName();
        if(strlen($name)>0)
        {
            if($name[0]==".")
            {
                return true;
            }
        }
        return false;
    }

    public function setLastModified() : bool
    {
        return false;
    }

    public function getLastModified(string $format ="F d Y H:i:s") : string
    {
        return date($format, filectime($this->absolute_path));
    }
    
    public function getLastModifiedNoSpace() : string
    {
        return date("Y-m-d_H-i-s", filectime($this->absolute_path));
    }

    public function setReadOnly() : bool
    {
        if(@chmod($this->absolute_path,0444))
        {
            return $this->isReadOnly();
        }
        return false;
    }

    public function isReadOnly() : bool
    {
        if ($this->canRead() && !$this->canWrite() )
        {
            return true;
        }
        return false;
    }
    
    public function isReadWrite() : bool
    {
        return $this->canRead() && $this->canWrite();
    }

    public function canRead() : bool
    {
        return is_readable($this->absolute_path);
    }

    public function canWrite() : bool
    {
        return is_writable($this->absolute_path);
    }

    public function setWritable() : bool
    {
        if(!$this->canWrite())
        {
            if(@chmod($this->absolute_path,0666))
            {
                return $this->canWrite();
            }
            return false;
        }
        return true;
    }

    public function setAllAccess() : bool
    {
        if($this->exists())
        {
            $wantedPerms = 0777;
            $actualPerms = fileperms($this->absolute_path);
            if ($actualPerms < $wantedPerms)
            {               
                if(@chmod($this->absolute_path,0777))
                {
                    return $this->canWrite() && $this->canRead();
                }
            }
            else
            {
                return true;
            }
        }
        return false;
    }
    
    public function getPermissions() : string
    {
        if($this->exists())
        {        
            $perms=fileperms($this->absolute_path);
            if($perms!=false)
            {
                return "".$perms;
            }
        }
        return "";
    }

    public function toString(string $delimitor="") : string
    {
        return ($this->absolute_path).$delimitor;
    }
   
    /**
     * 
     * @return array<int,mixed>
     */
    public function toByteArray() : array
    {
        return file($this->absolute_path);
    }

    public function toContentString() : string
    {
        return file_get_contents($this->absolute_path);
    }
    
    private mixed $handler=null;
    public function readFileByLine(bool $restart=false) : ?string
    {
        if(is_null($this->handler) || $restart)
        {
            if($this->isFile()&&$this->getSize()>0)
            {
                $this->handler = @fopen($this->absolute_path, "r");
            }
        }
        
        if($this->handler)
        {
            if( ($line = fgets($this->handler)) !== false)
            {
                return $line;
            }
            else
            {
                fclose($this->handler);
            }
        }
        return null;
    }
    
    /**
     * 
     * @return array<int,string>
     */
    public function readFileToArray() : array
    {
        $array=[];
        while($line=$this->readFileByLine())
        {
            $array[]=$line;
        }
        return $array;
    }
    
    public function toContentKacheString() : string
    {
        return gzuncompress(base64_decode(file_get_contents(($this->absolute_path))));
    }    
    
    public function insertStringInFile(string $string) : bool
    {
        $dir=$this->getParentKFile();
        if($dir->canWrite())
        {
            return (bool)file_put_contents($this->absolute_path, $string);
        }
        return false;
    }
    
    public function appendStringInFile(string $string) : bool
    {
        $dir=$this->getParentKFile();
        if($dir->canWrite())
        {
            return (bool)file_put_contents($this->absolute_path, $string, FILE_APPEND);
        }
        return false;
    }    
    
    public function insertStringInFileKache(string $string) : bool
    {
        return (bool)file_put_contents($this->absolute_path, base64_encode(gzcompress($string,9)));
    }

    public function getError() : string
    {
        return $this->error;
    }

    public function getParentKFile() : KFile
    {
        $dir = new KFile($this->getParentName());
        return $dir;
    }
    
    public function getSize() : int
    {
        if($this->exists())
        {
            return filesize($this->absolute_path);
        }
        return 0;
    }
    
    public function isDirectoryEmpty() : bool
    {
        if($this->exists()&&$this->isDirectory())
        {
            $listFiles=$this->listFilesNameToList();
            if($listFiles==null|| ($listFiles!=null&&$listFiles->getSize()==0))
            {
                return true;
            }
        }
        return false;
    }    
    
    public static function separator() : string
    {
        if(!self::$sep_init)
        {
            self::$sep_init=true;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
            {
                self::$sep_string='\\';
            } 
            else 
            {
                self::$sep_string='/';
            }
        }
        return self::$sep_string;
    }
    
    public function flushCache() : void
    {
        clearstatcache();
    }
    public function analyse(string $delimitor="<br />") : string
    {
        $string="";
        $string.="Filename= ".$this->getName();
        $string.=$delimitor;
        $string.="Path= ".$this->getPath();
        $string.=$delimitor;
        $string.="Extension= ".$this->getExtension();
        $string.=$delimitor;
        $string.="isFile= ".$this->boolToString($this->isFile());
        $string.=$delimitor;
        $string.="isDirectory= ".$this->boolToString($this->isDirectory());
        $string.=$delimitor;
        $string.="isReadOnly= ".$this->boolToString($this->isReadOnly());        
        $string.=$delimitor;
        
        return $string;   
    }
    
    private function boolToString(mixed $bool) : string
    {
        if($bool)
        {
            return "true";
        }
        else
        {
            return "false";
        }
    }
    
    public function toBase64(string $mimeMediaType="") : string
    {
        //data:[<mediatype>][;base64],<data>
        $base64String="";
        if($this->exists()&&$this->isFile())
        {
            if(empty($mimeMediaType))
            {
                $mimeMediaType= mime_content_type($this->getPath());
                //$type = pathinfo($this->absolute_path, PATHINFO_EXTENSION);
            }

            $data = file_get_contents($this->absolute_path);
            $base64String = 'data:'. $mimeMediaType . ';base64,' . base64_encode($data);
        }
        return $base64String;
    }
    
    public function toZip(string $path_zip_file) : bool
    {
        if (!extension_loaded('zip')) 
        {
            return false;
        }
        
        $zip = new ZipArchive();
        if (!$zip->open($path_zip_file, ZIPARCHIVE::CREATE  | ZipArchive::OVERWRITE))
        {
            return false;
        }
        $countFile=0;
        if($this->exists()&&$this->isDirectory())
        {
            $this->addToZip($zip,$this,"",$countFile);
        }
        elseif ($this->exists()&&$this->isFile())
        {
            $zip->addFromString(basename($this->getPath()), file_get_contents($this->getPath()));
        }
        
        $zip->close(); 
        
        $zipFile= new KFile($path_zip_file);
        if($zipFile->exists()&&$zipFile->isFile()&&$zipFile->length()>0)
        {
            return true;
        }
        return false;
    }
    
    private function addToZip(ZipArchive $zip, KFile $dir, string $localPath="",int &$countFile=0) : void
    {
        if($localPath!="")
        {
            $localPath=$localPath.self::separator().$dir->getName(); 
        }
        else
        {
            $localPath=$dir->getName();
        }
        $zip->addEmptyDir($localPath); 

        $listFiles=$dir->listFilesToArray();
        if(is_null($listFiles))
        {
            return;
        }
        /* @var $file KFile */
        foreach ($listFiles as $file)
        {
            if($file->isFile())
            {
                $countFile++;
                $zip->setCompressionIndex($countFile, ZipArchive::CM_STORE);
                $zip->addFile($file->getPath(),$localPath.self::separator().$file->getName());
            }
            else if($file->isDirectory())
            {
                $this->addToZip($zip,$file,$localPath,$countFile);
            }
        }        
    }
    
    public function toZipCLI(string $path_zip_file) : bool
    {
        if($this->exists() &&$this->isDirectory())
        {
            if(self::cmd_exists("zip"))
            {
                //echo "cd \"".$this->getPath()."\" && zip -0 -r \"".$path_zip_file."\" .";
                exec("cd \"".$this->getPath()."\" && zip -0 -r \"".$path_zip_file."\" .");
                $zipFile= new KFile($path_zip_file);
                if($zipFile->exists()&&$zipFile->isFile()&&$zipFile->length()>0)
                {
                    return true;
                }
            }
        }
        return false;        
    }
    
    public static function isZipExtensionInstalled() : bool
    {
        if(self::cmd_exists("zip"))
        {
            return true;
        }
        else if (extension_loaded('zip')) 
        {
            return false;
        }        
        return false;
    }
    
    private static function cmd_exists(string $cmd) : bool
    {
        $test = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "where" : "which";
        return is_executable(trim("".shell_exec("$test $cmd")));
    }       
    public function unZip(string $path_to_unzip) : bool
    {
        if (!extension_loaded('zip')) 
        {
            return false;
        }
        
        $destination=new KFile($path_to_unzip);
        if(!$destination->exists())
        {
            $destination->mkdir();
        }
        
        if(!$destination->exists()||!$destination->isDirectory())
        {
            // cannot make dir
            return false;
        }        
        

        //echo convertBoolToString($this->exists())."".convertBoolToString($this->isFile()).$this->getExtension();
        if($this->exists()&&$this->isFile()&&
                strcasecmp("zip",$this->getExtension())==0)
        {
            $zip = new ZipArchive;
            if ($zip->open($this->absolute_path) === TRUE) 
            {       
                $status=false;
                if($zip->extractTo($destination->getPath()))
                {
                    $status=true;
                }
                $zip->close();  
                return $status;
            }                
        }
        return false;
    }
    
    public function getPathWithout(string $subPath) : string
    {
        return str_replace($subPath,"", $this->getPath());
        ///$this->getPath()
        
    }
    
    public function normalizePathName() : void
    {
        $this->absolute_path=$this->removeDots("".$this->absolute_path);
    }
    
    private function removeDots(string $path) : string
    {
        $root = ($path[0] === '/') ? '/' : '';

        $segments = explode('/', trim($path, '/'));
        $ret = array();
        foreach ($segments as $segment)
        {
            if (($segment == '.') || strlen($segment) === 0)
            {
                continue;
            }
            if ($segment == '..')
            {
                array_pop($ret);
            }
            else
            {
                array_push($ret, $segment);
            }
        }
        return $root.implode('/', $ret);
    }
//    public function waitWhileIsLocked()
//    {
//        
//    }
}