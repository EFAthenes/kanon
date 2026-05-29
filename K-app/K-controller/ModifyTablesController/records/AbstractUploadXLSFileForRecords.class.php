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
abstract class AbstractUploadXLSFileForRecords extends AbstractTableController
{
    protected static string $GET_INPUT_UPLOAD_FILE="get_input_upload_file";
    protected static int $NUMBER_OF_FILES=1;
    /**
     * 
     * @var array<int,KFile>
     */
    protected array $arrayFilesUploaded=[];
    
    /**
     * 
     * @return array<int,KFile>
     */
    public function getArrayFilesUploaded() : array
    {
        return $this->arrayFilesUploaded;
    }
 
    public function manageUploadedFiles() : bool
    {      
        $upload_new_file=0;
        if(!KInput::checkInput(KInput::$INPUT_GET, self::$GET_INPUT_UPLOAD_FILE,KInput::$VARIABLE_INT,$upload_new_file) || $upload_new_file!=1)
        {
            return false;
        }
        
        $getToken="";
        if(!KInput::checkInputGet(KUploadFiles::UPLOAD_TOKEN, KInput::$VARIABLE_STRING, $getToken))
        {
            $message=new KAlertComponent("Error","The token is not present.", KAlertComponent::$TYPE_ERROR);
            $this->addComponent($message);
            return false;            
        }
        
        $token=SessionMemory::getInstance()->get("UPLOAD_TOKEN");         
        $tmp=ParamManager::getInstance()->get("TMP_UPLOAD_DIR");
        if(empty($tmp))
        {
            $tmp=KApp::getInstance()->getCacheFolder()."/tmp/";
        }

        $directory_temp_upload = new KFile($tmp);

        if(!$directory_temp_upload->exists())
        {
            $directory_temp_upload=new KFile("/tmp");
            if(!$directory_temp_upload->exists())
            {
                $message=new KAlertComponent("Error","The folder of the Token doesn't exist!<br />=>".$directory_temp_upload->getPath(), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return false;  
            }
        }

        $directory_temp_upload_uniqid=new KFile($directory_temp_upload->getPath().KFile::separator().$token);

        if(!$directory_temp_upload_uniqid->exists() || !$directory_temp_upload_uniqid->isDirectory())
        {
            $message=new KAlertComponent("Error","The folder with the uploaded files doesn't exist!<br />=>".$directory_temp_upload_uniqid->getPath(), KAlertComponent::$TYPE_ERROR);
            $this->addComponent($message);
            return false;
        }       
        else
        {
            $arrayFile=$directory_temp_upload_uniqid->listFilesToArray();
            if(is_null($arrayFile))
            {
                $message=new KAlertComponent("Erreur","Aucun fichier n'a pu être récupéré!<br />=>".$directory_temp_upload_uniqid->getPath(), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);   
                return false;
            }           
            else if(count($arrayFile)!=self::$NUMBER_OF_FILES)
            {
                $message=new KAlertComponent("Erreur","Plusieurs fichiers ont été chargés alors que un seul est attendu.<br />=>".$directory_temp_upload_uniqid->getPath(), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);   
                return false;
            }
            else
            {  
                $this->arrayFilesUploaded=$arrayFile;
                return true;
            }
        }          
    }
}