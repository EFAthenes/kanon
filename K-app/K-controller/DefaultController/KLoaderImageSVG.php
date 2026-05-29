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
class KLoaderImageSVG extends KController
{
    public function execute(): bool
    {    
        $type="";
        $loader_name="loader-1.svg";
        
        if(KInput::checkInputGet("type",KInput::$VARIABLE_STRING , $type))
        {
            $loader_name_temp="loader-".$type.".svg";
        
            $path=ParamManager::getInstance()->app_folder.KFile::separator()."..".KFile::separator()."K-app".KFile::separator()."K-layout".KFile::separator()."KDefaultLayout".KFile::separator()."img".KFile::separator().$loader_name_temp;
            $file= new KFile($path);
            if($file->exists() && $file->getExtension()=="svg")
            {
                $loader_name=$loader_name_temp;
            }
        }
        
        $path=ParamManager::getInstance()->app_folder.KFile::separator()."..".KFile::separator()."K-app".KFile::separator()."K-layout".KFile::separator()."KDefaultLayout".KFile::separator()."img".KFile::separator().$loader_name;
        $pathImage=new KFile($path);
        if($pathImage->exists())
        {
             echo file_get_contents($pathImage->getPath());

        }       
        return true;
    }
}