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
class UploadPondFiles extends KController
{
    public function execute(): bool
    {
        //var_dump($_FILES);
        
        $appFolder=ParamManager::getInstance()->app_folder;
        
        $folderTmp=new KFile($appFolder.KFile::separator()."tmp");
        $folderTmp->mkdir();
        
        $errorString="";
        
        if($folderTmp->exists()&&$folderTmp->isDirectory())
        {         
            if(count($_FILES) /*&&isset($_FILES['filepond']) */)
            {
                $this->clearOldDirectories($folderTmp);

                $folderTmpUpload=$this->makeDirectory($folderTmp);
            
                if($folderTmpUpload->mkdir())
                {
                    foreach($_FILES as $fileUploaded)
                    {
                        //echo $fileUploaded."<br />";
                        //$_FILES['filepond'];
                        $errorString.="DEBUG :". print_r($_FILES,true)."<br />";

                        if(isset($fileUploaded['tmp_name'])&&is_array($fileUploaded['tmp_name']) &&count($fileUploaded['tmp_name'])>0)
                        {
                            $count=0;
                            foreach($fileUploaded['tmp_name'] as $file)
                            {
                                if(move_uploaded_file($file, $folderTmpUpload->getPath().KFile::separator().$fileUploaded['name'][$count]))
                                {
                                    $this->addString($folderTmpUpload->getName());
                                    return true;
                                }
                                else
                                {
                                    $errorString.="CANNOT MOVE FILE :". print_r($fileUploaded,true);
                                }                                
                                $count++;
                            }
                        }
                        else
                        {
                            $errorString.="NO FILES UPLOADED";
                        }
                    }
                }
                else
                {
                    $errorString.="CANNOT MAKE TEMP DIRECTORY";
                }
            }
            else
            {
                $errorString.="NO FILES UPLOADED";
            }
        }
        else
        {
            $errorString.="UPLOAD FOLDER NOT WRITABLE";
        }
        $this->addString("ERROR_WHILE_UPLOADING => ".$errorString);
        return true;
    }
    
    
    private function makeDirectory(KFile $folder) : ?KFile
    {   
        $i=0;
        $max=999;
        while($i<$max)
        {
            $rand=KRandom::makeRandom();
            $date = new DateTime();
            $random_dir=date_format($date, 'Y-m-d_H-i-s')."_".$rand;
            $folderTmpUpload=new KFile($folder->getPath().KFile::separator().$random_dir);
            if(!$folderTmpUpload->exists())
            {
                return $folderTmpUpload;
            }
            $i++;
        }
        return null;
    }
    
    private function clearOldDirectories(KFile $folder) : bool
    {
        $list=$folder->listFilesToList();
        /* @var  $folderCheck KFile */
        foreach($list as $folderCheck)
        {
            $date = new DateTime();
            $today=date_format($date, 'Y-m-d');
            if(substr($folderCheck->getName(), 0, strlen($today)) != $today)
            {
                return $folderCheck->delete();
            }
        }
        return false;
    }
}
