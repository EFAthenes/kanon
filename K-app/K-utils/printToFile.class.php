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
class printToFile
{
    function __construct(string $text,string $file="/tmp/log.txt")
    {
        if(file_exists($file))
        {
            //chmod($file,0777);
            unlink($file);
        }

        $fp = fopen($file, "w");
        if($fp!==false)
        {
            fwrite($fp, $text);
            fclose($fp);
            chmod($file,0777);
        }
    }


    function  __destruct()
    {

    }
}
