<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2024 EFA, Ecole française d'athènes, EFAthenes.
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
class KLogsInfoDownload extends KController
{
    public static string $GET_PARAM_LOG ="param_log";
    public function execute(): bool
    {
        $param_log="";
        if(KInput::checkInput(KInput::$INPUT_GET, self::$GET_PARAM_LOG,KInput::$VARIABLE_STRING,$param_log))
        {
            $dir=new KFile(ParamManager::getInstance()->log_directory);
            if($dir->exists())
            {
                $file=new KFile(ParamManager::getInstance()->log_directory.KFile::separator()."exception.log");
                if($file->exists())
                {                
                    while($line=$file->readFileByLine())
                    {
                        $first=explode("]", $line,2);
                        if(count($first)==2)
                        {
                            $time=str_replace("[","",$first[0]);
                            if($time==$param_log)
                            {
                                $third=explode("@@",$first[1],2);
                                if(count($third)==2)
                                {
                                    $filename=trim($third[1]);
                                    $fileDL=new KFile(ParamManager::getInstance()->log_directory.KFile::separator().$filename);
                                    if($fileDL->exists())
                                    {
                                        KRoute::forceDownloadKFile($fileDL, false, true);
                                    }
                                }
                                return true;
                            }
                        }
                    } 
                }
            }
        }
        return true;
    }
}