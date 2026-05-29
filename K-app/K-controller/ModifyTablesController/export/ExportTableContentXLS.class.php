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
class ExportTableContentXLS extends AbstractTableController
{
    public function execute(): bool
    {
        if(!$this->checkTableName())
        {
            return true;
        }
        $tablename=$this->getTablename();
        $objectName=$this->getObjectName();

        $dbList=new DbList($objectName);
        $dbList->setCache(true);
        $list=$dbList->getAll();   
        
        $array=[];
        /* @var $tempObject KObject */
        $tempObject = new $objectName();
        $array[]=$tempObject->fieldsLabelToArray(false);
        /* @var $kobject KObject */
        foreach ($list as $kobject)
        {
            $array[]=$kobject->fieldsToArray();
        }
        //$this->addString(print_r($array, true));
        
        require_once __ROOT__.'/K-lib/K-excel/KExcel.class.php';
        $excel=new KExcel();
        $destination="/tmp/export_".$tablename."_".KRandom::makeRandom();
        if($excel->makeFileFromDoubleArray($destination, KExcel::$EXCEL7, $array))
        {
            $file= new KFile($excel->getOutputFile());
            KRoute::forceDownloadKFile($file, true, true);
        }   
        return true;
    }
}