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
class KLogsComponent extends KComponent
{
    function __construct()
    {
        parent::__construct();
        $this->setName("LOGS_INFO");
        $this->setId();
        
        $dir=new KFile(ParamManager::getInstance()->log_directory);
        
        if($dir->exists())
        {
            $file=new KFile(ParamManager::getInstance()->log_directory.KFile::separator()."exception.log");
            if($file->exists())
            {
                $table=[];
                while($line=$file->readFileByLine())
                {
                    $row=[];
                    $first=explode("]", $line,2);
                    if(count($first)==2)
                    {
                        $time=str_replace("[","",$first[0]);
                        $row[]=$time;
                        $second=explode(":", $first[1],2);
                        if(count($second)==2)
                        {
                            $row[]=$second[0];
                            $row[]=$second[1];
                            $third=explode("@@",$second[1],2);
                            if(count($third)==2)
                            {
                                $button=new ButtonComponent('Download',ButtonComponent::$TYPE_INFO,"fa-solid fa-download"); 
                                $action=KRoute::makeActionKURL(RoutesItems::$LOGS_DOWNLOAD,[KLogsInfoDownload::$GET_PARAM_LOG=>$time]);
                                $button->setActionKURL($action);
                                $row[]=$button;
                            }
                        }
                    }
                    $table[]=$row;
                }
                
                $columns=["Hour","Type","Message","File"];
                $data=new DataTableSimpleTableComponent("logs_id",$columns, $table);
                $data->setOrderByColumn(0,true);
                $data->setButton_bar(true);
                $this->addComponent($data);
            }
            else
            {
                $this->addComponent(new KAlertComponent("File not found",$dir->getPath()));
            }
        }
        else
        {
            $this->addComponent(new KAlertComponent("Directory not found",$dir->getPath()));
        }
    } 
}