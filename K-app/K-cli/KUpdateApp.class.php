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
class KUpdateApp
{
    private string $app_directory="";
    
    private string $app_version="";
    
    private string $app_version_separator=";";
    
    private string $app_update_url_versions="";
    
    /**
     * 
     * @var array<int,string>
     */
    private array $arrayVersions=[];
    
    private string $app_update_version="";
    
    private string $app_update_version_url="";
    
    public const string DIR_ACTIONS="actions";
    public const string DIR_SRC="src";
    public const string DIR_SQL="sql";
     
    public const string KAPP_DIR="K-app";
    
    private ?KFile $srcDir=null;
    private ?KFile $sqlDir=null;
    private ?KFile $actionsDir=null;
    
    private bool $debug=false;

    //put your code here
    function __construct(string $folderApp)
    {
        $this->app_directory=$folderApp;
    }

    public function retrieveVersion() : bool
    {
        $file=new KFile($this->getDirectoryOfTheApp()."/version.txt");
        if($file->exists())
        {
            $content=$file->toContentString();
            if(!empty($content))
            {
                if(string_contains($this->app_version_separator, $content))
                {
                    $array=explode($this->app_version_separator, $content);
                    if(count($array)>1)
                    {
                        $this->app_version=$array[0];
                        $this->app_update_url_versions=$array[1];
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function setNewVersion(string $version) : bool
    {
        if($this->retrieveVersion())
        {
            $file=new KFile($this->getDirectoryOfTheApp()."/version.txt");
            if($file->insertStringInFile($version.";".$this->app_update_url_versions))
            {
                $this->app_version=$version;
                return true;
            }
        }
        return false;
    }
    
    public function isMyVersionUpdatable() : bool
    {
        if(count($this->arrayVersions))
        {
            foreach ($this->arrayVersions as $lineVersion)
            {
                //echo print_r($lineVersion);
                $versionsParam=explode(";",$lineVersion);
                if(count($versionsParam)==3)   
                {
                    if(strcmp($versionsParam[0],$this->app_version)==0)
                    {
                        // c'est la version à aller chercher
                        $this->app_update_version=$versionsParam[1];
                        $this->app_update_version_url=$versionsParam[2];
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function getAvailableVersions() : int
    {
        $content=file_get_contents($this->app_update_url_versions);
        if(!empty($content))
        {
            foreach (explode("\n", $content) as $line)
            {
                $this->arrayVersions[]=$line;
            }
        }
        return count($this->arrayVersions);
    }
    
    public function getVersion() : string
    {
        return $this->app_version;
    }
    public function getUpdateVersion() : string
    {       
        return $this->app_update_version;
    }
    public function getUpdateVersionUrl() : string
    {       
        return $this->app_update_version_url;
    }    
    
    /**
     * 
     * @return array<int,string>
     */
    public function getArrayVersions() : array
    {
        return $this->arrayVersions;
    }   
       
    private function getDirectoryOfTheApp() : string
    {
        return $this->getDirectoryOfTheFramework().$this->app_directory;
    }
    
    private function getDirectoryOfTheFramework() : string
    {
        return __DIR__.KFile::separator()."..".KFile::separator()."..".KFile::separator();
    }  
    
    public function getKFileOfTheFramework() : KFile
    {
        $dir=new KFile($this->getDirectoryOfTheFramework());
        $dir->normalizePathName();
        return $dir;
    }
    
    public function getKFileOfTheApplication() : KFile
    {
        $dir=new KFile($this->getDirectoryOfTheApp());
        $dir->normalizePathName();
        return $dir;
    }    
    
    
    public function getUpdateDirPathVersion() : string
    {
        return $this->makeUpdateDirPath().KFile::separator()."v".$this->getUpdateVersion();
    }
    public function makeUpdateDirPath() : string
    {
        if(empty($this->update_dir))
        {
            $this->update_dir=$this->getDirectoryOfTheFramework().KFile::separator()."Z_update_".KRandom::makeRandomString(20);
        }
        return $this->update_dir;
    }
    public function getUpdateDir() : string
    {
        return $this->makeUpdateDirPath();
    }
    private string $update_dir="";
    public function makeUpdateDir() : bool
    {
        $dir=new KFile($this->getUpdateDir());
        $dir->normalizePathName();
        $this->update_dir=$dir->getPath();
        if($dir->exists())
        {
            $dir->delete();
        }
        
        if($dir->mkdir()&&$dir->exists()&&$dir->isDirectory())
        {
            $dir->setAllAccess();
            return true;
        }
        return false;
    }
    
    public function deleteUpdateDir() : bool
    {
        $dir=new KFile($this->update_dir);
        if($dir->exists())
        {
            $dir->delete();
            /** @phpstan-ignore-next-line */
            if(!$dir->exists())
            {
                return true;
            }
        }        
        return false;
    }
    
    private string $zip_package_path="";
    public function downloadUpdatePackage() : bool
    {
        $this->zip_package_path=$this->update_dir.KFile::separator().$this->app_update_version.".zip";
        if(file_put_contents($this->zip_package_path, file_get_contents($this->app_update_version_url)))
        {
            return true;
        }
        return false;
    }
    
    public function unzipPackage() : bool
    {
        $fileZip=new KFile($this->zip_package_path); 
        if($fileZip->unZip($this->update_dir))
        {
            return true;
        }
        return false;
    }
    
    public function checkDirUpdates() : bool
    {
        $dirUpdate=new KFile($this->getUpdateDirPathVersion()); 
        if($dirUpdate->exists())
        {
            $this->srcDir=new KFile($dirUpdate->getPath().KFile::separator().self::DIR_SRC); 
            $this->sqlDir=new KFile($dirUpdate->getPath().KFile::separator().self::DIR_SQL); 
            $this->actionsDir=new KFile($dirUpdate->getPath().KFile::separator().self::DIR_ACTIONS); 
            
            if($this->srcDir->exists()&&$this->srcDir->isDirectory()
                    &&$this->sqlDir->exists()&&$this->sqlDir->isDirectory() 
                    && $this->actionsDir->exists()&&$this->actionsDir->isDirectory())
            {           
                return true;
            }
        }
        return false;        
    }
    
    public function setDebug() : void
    {
        $this->debug = true;
    }
    
    public function getDebug() : bool
    {
        return $this->debug;
    }
    
    public function replaceSrc(): bool
    {
        $debug = $this->getDebug();
        if (!is_null($this->srcDir) && $this->srcDir->exists() && $this->srcDir->isDirectory())
        {
            $frameworkDir = new KFile($this->getDirectoryOfTheFramework());
            $frameworkDir->normalizePathName();
            $list = $this->srcDir->listFilesToList();
            /* @var $kfile KFile */
            foreach ($list as $kfile)
            {
                if ($kfile->isFile())
                {
                    if($debug)
                    {
                        echo "Replace ".$kfile->getPath()." by ".$frameworkDir->getPath().KFile::separator().$kfile->getName()."\n";
                    }
                    else
                    {
                        $kfile->copyTo($frameworkDir->getPath().KFile::separator().$kfile->getName());
                    }
                }
                else if ($kfile->isDirectory())
                {
                    //$kfile->copyTo($file_copy_path);
                    // list directory
                    if ($kfile->getName() == "K-app")
                    {
                        $dir = new KFile($frameworkDir->getPath().KFile::separator().$kfile->getName());
                        if($debug)
                        {
                            echo "delete".$dir->getPath()."\n";
                            echo "replace".$kfile->getPath()."\n";
                        }
                        else
                        {
                            $dir->delete();
                            $kfile->copyReplaceTo($dir->getPath());
                        }                        
                    }
                    else
                    {
                        echo "Check for directory ".$kfile->getPath()."\n";
                        $dir = new KFile($frameworkDir->getPath().KFile::separator().$kfile->getName());
                        $list2 = $kfile->listFilesToList();
                        /* @var $kfile2 KFile */
                        foreach ($list2 as $kfile2)
                        {
                            if ($kfile2->isFile())
                            {
                                //$kfile2->copyTo($dir->getPath().KFile::separator().$kfile->getName());
                                if($debug)
                                {
                                    echo "(2)Replace ".$kfile2->getPath()." on ".$dir->getPath().KFile::separator().$kfile2->getName()."\n";
                                }
                                else
                                {
                                    $kfile2->copyTo($dir->getPath().KFile::separator().$kfile2->getName());
                                }
                            }
                            else if ($kfile2->isDirectory())
                            {
                                $dir2 = new KFile($dir->getPath().KFile::separator().$kfile2->getName());
                                if($debug)
                                {
                                    echo "(2)delete".$dir2->getPath()."\n";
                                    echo "(2)replace".$kfile2->getPath()."\n";
                                }
                                else
                                {
                                    $dir2->delete();
                                    $kfile2->copyReplaceTo($dir2->getPath());                                    
                                }
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

}