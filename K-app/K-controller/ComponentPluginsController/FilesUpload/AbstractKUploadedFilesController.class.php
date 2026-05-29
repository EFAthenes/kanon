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
abstract class AbstractKUploadedFilesController extends KController
{
    public const int SIMPLE_FILE_UPLOAD=1;
    public const int SEVERAL_FILES_UPLOAD=2;
    public const string PATH_UPLOADED="path_uploaded";
    
    private int $type_number_upload=0;
    /**
     * 
     * @var array<int,KFile>|null
     */
    private ?array $arrayFiles=null;
    private ?KFile $directoryUpload=null;
    private string $token="";
    private string $errorString="";
    private string $path="";
  
    /**
     * 
     * @return array<int,KFile>|null
     */
    public function getArrayFiles(): ?array
    {
        return $this->arrayFiles;
    }

    public function getDirectoryUpload(): ?KFile
    {
        return $this->directoryUpload;
    }
    
    public function getUploadPath(): string
    {
        return $this->path;
    }
    
    public function getUploadPathForOneFile(): string
    {
        $folder= new KFile($this->path);
        if($folder->exists()&&$folder->isDirectory())
        {
            $files=$folder->listFilesToList();
            if($files->getSize()==1)
            {
                return $files->get(0)->getPAth();
            }
        }
        return "";
    }    
    
    public function getToken(): string
    {
        return $this->token;
    }
    
    public function getErrorString() : string
    {
        return $this->errorString;
    }
    
    public function addErrorString(string $error) : void
    {
        $this->errorString.=$error;
    }
    
    public function setErrorString(string $error) : void
    {
        $this->errorString=$error;
    }    
    
    public function setSimpleFile() : void
    {
        $this->type_number_upload=self::SIMPLE_FILE_UPLOAD;
    }
    public function setSeveralFiles() : void
    {
        $this->type_number_upload=self::SEVERAL_FILES_UPLOAD;
    }
    
    private function setDefaultUploadType() : void
    {
        if($this->type_number_upload==0)
        {
            $this->setSeveralFiles();
        }        
    }
    
    private function checkLoadingGet() : bool
    {
        $loading=0;
        if(!KInput::checkInputGet(KUploadFiles::$IMPORT_LOADING, KInput::$VARIABLE_INT, $loading) || $loading != KUploadFiles::$IMPORT_LOADING_TAG)
        {    
             //$this->errorString ="Problem with the loding get value.";
             return false;
        } 
        return true;
    }
    
    
    protected function manageUploadedFiles() : bool
    {    
        $this->setDefaultUploadType();
         
        if(!$this->checkLoadingGet())
        {
            return false;
        }
   
        if(!$this->checkToken())
        {
            return false;
        }
        
        $directory_temp_upload=new KFile(ParamManager::getInstance()->get("TMP_UPLOAD_DIR"));

        if(!$directory_temp_upload->exists())
        {
            $directory_temp_upload=new KFile("/tmp");
            if(!$directory_temp_upload->exists())
            {
                $this->errorString ="The folder of the Token doesn't exist!<br />=>".$directory_temp_upload->getPath();
                return false;
            }
        }

        $this->directoryUpload=new KFile($directory_temp_upload->getPath().KFile::separator().$this->getToken());

        if(!$this->directoryUpload->exists() || !$this->directoryUpload->isDirectory())
        {
            $this->errorString ="The folder with the uploaded files doesn't exist!<br />=>".$this->directoryUpload->getPath();
            return false;
        }       
        else
        {
            $this->arrayFiles=$this->directoryUpload->listFilesToArray();
            if(is_null($this->arrayFiles))
            {
                $this->errorString ="Aucun fichier n'a pu être récupéré!<br />=>".$this->directoryUpload->getPath();  
                return false;
            }
        } 
        
        $this->errorString.="b)";
        
        $this->setPathInSession();
        return true;
    }
    
    private function setPathInSession() : void
    {
         $paths=SessionMemory::getInstance()->get(self::PATH_UPLOADED);
         if(is_null($paths)|| !is_array($paths))
         {
             $paths=[];
         }
         $paths[$this->token]=$this->directoryUpload->getPath();
         SessionMemory::getInstance()->putOrReplace(self::PATH_UPLOADED, $paths);
    }
    
    public static function getPathFromSession(?string $token) : string
    {
        if(empty($token))
        {
            return "";
        }
        $path="";
        $paths=SessionMemory::getInstance()->get(self::PATH_UPLOADED);
        if(!is_null($paths)&&is_array($paths)&& array_key_exists($token, $paths))
        {
            $path= "".$paths[$token];
        }
        return $path;
    }
    
    protected function checkToken(): bool
    {
        $this->token = "";
        if (!KInput::checkInputGet(KUploadFiles::UPLOAD_TOKEN, KInput::$VARIABLE_STRING, $this->token))
        {
            // message error
            $this->errorString = "Token not present, please restart procedure!";
            return false;
        }
        else if ($this->token != SessionMemory::getInstance()->get(KUploadFiles::UPLOAD_TOKEN))
        {
            // message error
            $this->errorString = "Token error number, please restart procedure!";
            return false;
        }
        return true;
    }    
    
    protected function checkPath(): bool
    {
        $this->path = self::getPathFromSession($this->token);
        
        if(empty($this->path))
        {
            $this->errorString = "Path empty !!!";
            return false;            
        }
        
        $folderUpload = new KFile($this->path);
        if (!$folderUpload->exists())
        {
            $this->errorString = "Problem loading files, the path doesn't exists. Please restart the file transfer or check the path access!<br />".$this->path;
            // message error
            return false;
        }
        else if ($folderUpload->isDirectoryEmpty())
        {
            $this->errorString = "Problem loading files, the directory is empty. Please restart the file transfer or check the !<br />".$this->path;
            // message error
            return false;
        }
        return true;
    }    
    
    
    abstract protected function manageUploadResult() :bool;
}