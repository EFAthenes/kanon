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
class KLogsInfoClean extends KController
{
    public function execute(): bool
    {
        $dir=new KFile(ParamManager::getInstance()->log_directory);
        if($dir->exists())
        {
            $dir->emptyDirectory();               
        }        
        return true;
    }
}