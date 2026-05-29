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
class KAppTemplateForPHPFileComponent extends PHPFileComponent
{
    public function __construct(string $path="not_set",?array $data=null)
    {   
        parent::__construct($path,$data,null);
        
        $template=ParamManager::getInstance()->get("TEMPLATE_STATIC_HTML");
        if(!empty($template))
        {
            $new_path=KFile::separator()."template".KFile::separator().$template.KFile::separator().$path;
            $tempPhpFile=ParamManager::getInstance()->app_folder.$new_path;
            if($this->setPhpFile($tempPhpFile))
            {
                return;          
            }
        }
        if(!$this->init())
        {
            //App view
            $new_path=KFile::separator()."K-view".KFile::separator().$path;
            $tempPhpFile=__ROOT__.KFile::separator().$new_path;
            $this->setPhpFile($tempPhpFile);
        }       
    }
}