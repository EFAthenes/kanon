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
class KTemplateForPHPFileComponent extends PHPFileComponent
{
    public function __construct(string $path="not_set",?array $data=null,?string $defaultRootPath=null)
    {
        parent::__construct($path,$data,$defaultRootPath);
        $template=TemplateManager::getInstance()->getActiveTemplateName();
        if(!empty($template))
        {
            $new_path=TemplateManager::getInstance()->getTemplateDirFullPath().KFile::separator().$path;
            //KDebugger::getInstance()->dump($new_path,"init22");
            if($this->setPhpFile($new_path))
            {
                $this->setIsInit(true);
                return;          
            }
        }
        $this->init();
    }
}