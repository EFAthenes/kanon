<?php

use Box\Spout\Common\Entity\Cell;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
/*
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
*/
class KFastExcel
{
    private string $author="";
    private string $sheetName="Sheet1";
    private mixed $writer=null;
    private string $destinationFile="";
    
    function getAuthor() : string
    {
        return $this->author;
    }

    function getSheetName() : string
    {
        return $this->sheetName;
    }

    function setAuthor(string $author) : void
    {
        $this->author=$author;
    }

    function setSheetName(string $sheetName) : void
    {
        $this->sheetName=$sheetName;
    }
    
    
    public function openWriter(string $dest) : bool
    {
        require_once __DIR__.'/lib/box/spout/src/Spout/Autoloader/autoload.php';
        $this->writer=WriterEntityFactory::createXLSXWriter();
        $this->destinationFile=$dest;
            $this->writer->openToFile($this->destinationFile);
            return $this->writer->isWriterOpened();
        }
    
    /**
     * 
     * @param array<mixed,mixed> $row
     * @return void
     */
    public function addRow(array $row) :void
    {
        $rowExcel = WriterEntityFactory::createRowFromArray($row,null);
        $this->writer->addRow($rowExcel);
    }
    
    public function closeWriter() : bool
    {
        $this->writer->close();

        return file_exists($this->destinationFile);       
    }

    /**
     * 
     * @param string $dest
     * @param array<mixed,mixed> $content
     * @return bool
     */
    public function makeFileFromDoubleArray(string $dest,array $content) : bool
    {
        require_once __DIR__.'/lib/box/spout/src/Spout/Autoloader/autoload.php';

        $writer=WriterEntityFactory::createXLSXWriter(); // for XLSX files
        //$writer = WriterFactory::create(Type::CSV); // for CSV files
        //$writer = WriterFactory::create(Type::ODS); // for ODS files

        $writer->openToFile($dest); // write data to a file or to a PHP stream
        //$writer->openToBrowser($fileName); // stream data directly to the browser
        //$writer->addRow($singleRow); // add a row at a time
        //$writer->addRows($content); // add multiple rows at a time
        //$sheet = $writer->getCurrentSheet();
        foreach ($content as $lines)
        {
           $cells=[];
           foreach ($lines as $col)
           {
                $cells[]=new Cell($col);
           }
            $writer->addRow(new Row($cells,null));
            //$writer->addRows($arrayTable);
        }        

        $writer->close();

        if(file_exists($dest))
        {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param string $dest
     * @param array<string,array<mixed,mixed>> $content
     * @return bool
     */
    public function makeFileFromTripleArrayWithKey(string $dest,array $content) : bool
    {
        require_once __DIR__.'/lib/box/spout/src/Spout/Autoloader/autoload.php';

        $writer=WriterEntityFactory::createXLSXWriter(); // for XLSX files


        $writer->openToFile($dest);
        
        foreach ($content as $sheetName=>$arrayTable)
        {
            $newSheet = $writer->getCurrentSheet();
            $newSheet->setName($sheetName);
           foreach ($arrayTable as $line)
           {
                $cells=[];
                foreach($line as $col)
                {
                    $cells[]=new Cell($col);
                }

               $writer->addRow(new Row($cells,null));
           }
            //$writer->addRows($arrayTable);
            $newSheet = $writer->addNewSheetAndMakeItCurrent();
        }

        $writer->close();

        if(file_exists($dest))
        {
            return true;
        }
        return false;
    }
}