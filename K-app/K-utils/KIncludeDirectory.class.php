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
class KIncludeDirectory
{
    private string $path_name="";
    private string $output="";
    private bool $makeCache=true;
    function __construct(?string $path=null)
    {
        if(!is_null($path))
        {
            $this->path_name=$path;
        }
    }
    function  __destruct()
    {

    }
    function getPath_name() : string
    {
        return $this->path_name;
    }

    function setPath_name(string $path_name) : void
    {
        $this->path_name=$path_name;
    }
    
    function getOutput() : string
    {
        return $this->output;
    }

    function includeAllDirectory() : void
    {
        $dir= new KFile($this->path_name);
        if(!$dir->exists())
        {
            echo "KIncludeDirectory::includeAllDirectory() <==> DIRECTORY DOESN'T EXIST ==>".$this->path_name;
            exit();
        }
        $this->includeSubFolder($dir);
    }
    
    /**
     * 
     * @param array<int,string>|null $arrayExclude
     * @return void
     */
    function includeAllSubDirectories(?array $arrayExclude=null) : void
    {
        $dir= new KFile($this->path_name);
        if(!$dir->exists())
        {
            echo "KIncludeDirectory::includeAllSubDirectories() <==> DIRECTORY DOESN'T EXIST ==>".$this->path_name;
            exit();
        }
        $kfile=new KFile();

        $array=$dir->listFilesToArray();
        for ($i = 0; $i < count($array); $i++)
        {
            $kfile=$array[$i];
            if($kfile->isDirectory())
            {
                if( is_null($arrayExclude) || !in_array($kfile->getName(),$arrayExclude) )
                {
                    $this->includeSubFolder($kfile);
                }
            }
        }
    }    
    
    private function includeSubFolder(KFile $dir) : void
    {
        $kfile=new KFile();
        if($dir!=null && $dir->exists())
        {
            $array=$dir->listFilesToArray();
            $arrayDir=[];
//            for ($i = 0; $i < count($array); $i++)
            if(!is_null($array))
            {
                foreach($array as $kfile)
                {
                    //$kfile=$array[$i];
                    if($kfile->isFile()&&$kfile->getExtension()=="php")
                    {
                        $kfile->stripDoubleSeparator();
                        require_once($kfile->getPath());
                        if($this->makeCache)
                        {
                            $this->output.="require_once('".$kfile->getPath()."');\n";
                        }
                    }
                    else if($kfile->isDirectory())
                    {
                        if(!stringStartsWith($kfile->getName(),"nocache_"))
                        {
                            $arrayDir[]=$kfile;               
                        }
                    }
                }
                foreach ($arrayDir as $dir)
                {
                    $this->includeSubFolder($dir);
                }
            }
        }         
    }
    public function includeFile() : void
    {
        $dir= new KFile($this->path_name);
        
        if($dir!=null && $dir->exists()&&$dir->isFile()&&$dir->getExtension()=="php")
        {
            require_once($dir->getPath());
            if($this->makeCache)
            {            
                $this->output.="require_once('".$dir->getPath()."');\n";
            }
        }
        else
        {
            echo "KIncludeDirectory::includeFile() <==> PROBLEM WITH FILE ==>".$this->path_name;
            exit();          
        }
    }   
}
