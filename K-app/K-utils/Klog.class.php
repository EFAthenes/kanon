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
class Klog
{
    protected string $filename="";
    protected string $path="";
    public function __construct(string $path,string $filename)
    {
        $this->filename=$filename;
        $this->path=$path;
    }
    public function addLineToFile(string  $string) : bool
    {
        $path=new KFile($this->path);
        if($path!=null&&$path->exists()&&$path->isDirectory())
        {
            $file=new KFile($this->path.KFile::separator().$this->filename."_".date("Y-m-d").".txt");
            if($file!=null)
            {
                if(!$file->exists())
                {
                    $newFile = fopen($file->getPath(),'w');
                    if($newFile!=null)
                    {
                        fclose($newFile);
                    }
                }
                if(file_put_contents($file->getPath(),$string."\n",FILE_APPEND|LOCK_EX)===false)
                {
                    return false;
                }
                return true;
            }
        }
        return false;
    }
    public function addLine(string $string) : bool
    {
        $path=new KFile($this->path);
        if($path!=null&&$path->exists()&&$path->isDirectory())
        {
            $file=new KFile($this->path.KFile::separator().$this->filename);
            if($file!=null)
            {
                if(!$file->exists())
                {
                    $newFile = fopen($file->getPath(),'w');
                    if($newFile!=null)
                    {
                        fclose($newFile);
                    }
                }
                if(file_put_contents($file->getPath(),$string."\n",FILE_APPEND|LOCK_EX)===false)
                {
                    return false;
                }
                return true;
            }
        }
        return false;
    }    
}