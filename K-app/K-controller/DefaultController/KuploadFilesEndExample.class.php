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
class KuploadFilesEndExample extends KController
{
    public function execute(): bool
    {   
        $getToken="";
        if(!KInput::checkInputGet(KUploadFiles::UPLOAD_TOKEN, KInput::$VARIABLE_STRING, $getToken))
        {
            $message=new KAlertComponent("Error","The token is not present.", KAlertComponent::$TYPE_ERROR);
            $this->addComponent($message);
            return true;            
        }
        
        $token=SessionMemory::getInstance()->get("UPLOAD_TOKEN");         
        $directory_temp_upload=new KFile(ParamManager::getInstance()->get("TMP_UPLOAD_DIR"));

        if(!$directory_temp_upload->exists())
        {
            $directory_temp_upload=new KFile("/tmp");
            if(!$directory_temp_upload->exists())
            {
                $message=new KAlertComponent("Error","The folder of the Token doesn't exist!<br />=>".$directory_temp_upload->getPath(), KAlertComponent::$TYPE_ERROR);
                $this->addComponent($message);
                return true;
            }
        }

        $directory_temp_upload_uniqid=new KFile($directory_temp_upload->getPath().KFile::separator().$token);

        if(!$directory_temp_upload_uniqid->exists() || !$directory_temp_upload_uniqid->isDirectory())
        {
            $message=new KAlertComponent("Error","The folder with the uploaded files doesn't exist!<br />=>".$directory_temp_upload_uniqid->getPath(), KAlertComponent::$TYPE_ERROR);
            $this->addComponent($message);
            return true;
        }       
        else
        {
            $html="";
            $arrayFile=$directory_temp_upload_uniqid->listFilesNameToArray();
            foreach($arrayFile as $string)
            {
                $html.="".$directory_temp_upload_uniqid->getPath().KFile::separator().$string."<br />";
            }
            $this->addString($html);
        }     
        return true;         
    }
}