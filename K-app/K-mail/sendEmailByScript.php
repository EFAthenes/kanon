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
require_once __DIR__.'/../include.php';
if(isset($argv) && count($argv)==6)
{  
    $folderApp=KManageDb::getAppFolder();

    $folder=new KFile(__DIR__."/../../".$folderApp."/");

    echo "\n\n".$folder->getPath()."\n\n";
    
    if($folderApp==""||!$folder->exists()|| !$folder->isDirectory() )
    {
        echo -1;
    }
    
    $include=new KIncludeDirectory($folder->getPath()."/config/");
    $include->includeAllDirectory();
    
    $mail=new KMail();

    if($mail->sendAndStore($argv[1],$argv[2],$argv[3],stripslashes($argv[4]),stripslashes($argv[5])))
    {
        echo 1;
        exit;
    }
    echo 0;
    exit;
}
echo -1;
exit;
?>