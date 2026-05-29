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
class KFilesUploadControllerAction extends KController
{
    public function execute(): bool
    {
        $token = SessionMemory::getInstance()->get("UPLOAD_TOKEN");
        $token_get="";
                
        try
        {
            if(!KInput::checkInputGet("UPLOAD_TOKEN", KInput::$VARIABLE_STRING, $token_get))
            {
                throw new RuntimeException('Invalid Get.');
            }
            
            if($token_get!=$token)
            {
                throw new RuntimeException('Invalid TOKEN.');
            }
            
            if (!isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
            {
                throw new RuntimeException('Invalid parameters.');
//                    $this->addString('Invalid parameters.');
//                    return true; 
            }

            switch ($_FILES['file']['error'])
            {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
//                        $this->addString('No file sent.');
//                        return true; 
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
//                        $this->addString('Exceeded filesize limit.');
//                        return true; 
                default:
//                        $this->addString('Unknown errors.');
//                        return true; 
                //throw new RuntimeException('Unknown errors.');
            }
            
            $directory_temp_upload = new KFile(ParamManager::getInstance()->get("TMP_UPLOAD_DIR"));
            $directory_temp_upload->mkdir();

            if (!$directory_temp_upload->exists())
            {
                throw new RuntimeException('directory upload doesn\'t exist.');
            }

            $directory_temp_upload_uniqid = new KFile($directory_temp_upload->getPath() . KFile::separator() . $token);
            $directory_temp_upload_uniqid->mkdir();

            if (!$directory_temp_upload_uniqid->exists())
            {
                throw new RuntimeException('Cannot make temp upload directory.');
            }

            $filepath = $directory_temp_upload_uniqid->getPath() . KFile::separator() . $_FILES['file']['name'];

            if (!move_uploaded_file(
                            $_FILES['file']['tmp_name'],
                            $filepath
                    ))
            {
//                    $this->addString('Failed to move uploaded file.');
//                    return true; 
                throw new RuntimeException('Failed to move uploaded file.');
            }
            else
            {
                $string = print_r($_FILES, true);
                $file = new KFile($filepath);
                if ($file->exists() && $file->isFile())
                {
                    // All good, send the response
                    echo json_encode([
                        'status' => 'ok',
                        'path' => $filepath,
                        'log' => $string
                    ]);
                }
                else
                {
                    throw new RuntimeException('Error when creating and moving new file!');
                }
            }
        } 
        catch (RuntimeException $e)
        {
            // Something went wrong, send the err message as JSON
            http_response_code(400);

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        return true;
    }
}