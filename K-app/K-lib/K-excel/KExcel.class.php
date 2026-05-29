<?php
/**
 * Description of KExcelclass
 *
 */

class KExcel
{
    private string $fileName="";
    private string $outputFileName="";
    private string $type="CSV";
    private mixed $reader=null;
    private mixed $writer=null;
    private mixed $PHPExcel=null;
    private string $delimiter=";";

    private string $column="A";

    public static string $CSV='Csv';
    public static string $EXCEL5='Xls';
    public static string $EXCEL7='Xlsx';
    public static string $ODS='Ods';

    /**
     * 
     * @var array<int,string>
     */
    public array $columnName=[];

    public string $owner="";

    public function __construct()
    {
        //require_once __ROOT__.'/K-composer/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';        
        require_once __ROOT__.'/K-composer/vendor/autoload.php';        
        $this->initSetMemory();
        $array=array();
        $array[]="A";
        $array[]="B";
        $array[]="C";
        $array[]="D";
        $array[]="E";
        $array[]="F";
        $array[]="G";
        $array[]="H";
        $array[]="I";
        $array[]="J";
        $array[]="K";
        $array[]="L";
        $array[]="M";
        $array[]="N";
        $array[]="O";
        $array[]="P";
        $array[]="Q";
        $array[]="R";
        $array[]="S";
        $array[]="T";
        $array[]="U";
        $array[]="V";
        $array[]="W";
        $array[]="X";
        $array[]="Y";
        $array[]="Z";

        $this->columnName=[];

        for($i=0; $i< count($array) ; $i++)
        {
            $this->columnName[]=$array[$i];
        }
        
        for($i=0; $i< count($array) ; $i++)
        {
            for($j=0; $j< count($array) ; $j++)
            {
                $this->columnName[]=$array[$i].$array[$j];
            }
        }       

    }
    
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    

    public function setFilename(string $fileName) : void
    {
        $this->fileName=$fileName;
    }


    public function setType(string $type) : void
    {
        $this->type=$type;
    }

    public function findType() : bool
    {
        $status=true;
        //echo $this->fileName;
        
        if(strpos($this->fileName,".csv"))
        {
            $this->type=KExcel::$CSV;
        }
        elseif(strpos($this->fileName,".xlsx"))
        {
            $this->type=KExcel::$EXCEL7;
        }
        elseif(strpos($this->fileName,".xls"))
        {
            $this->type=KExcel::$EXCEL5;
        }
        elseif(strpos($this->fileName,".ods"))
        {
            $this->type=KExcel::$ODS;
        }        
        else
        {
            $status=false;
        }
        return $status;
    }

    public function makeWriter(string $type) : bool
    {
        $this->writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->PHPExcel, $type);
        //$this->writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx();

        if( $this->writer != null)
        {
            if($type == KExcel::$CSV)
            {
                /* @phpstan-ignore-next-line */
                $this->writer->setDelimiter($this->delimiter);
                /* @phpstan-ignore-next-line */
                $this->writer->setEnclosure('');
                //$this->writer->setLineEnding("\r\n");
                /* @phpstan-ignore-next-line */
                $this->writer->setSheetIndex(0);
            }
            return true;
        }
        return false;
    }

    public function makeReader()  : void
    {
        $this->reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($this->type);
        //$this->reader = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        if( $this->type == KExcel::$CSV)
        {
            /* @phpstan-ignore-next-line */
            $this->reader->setDelimiter($this->delimiter);
            /* @phpstan-ignore-next-line */
            $this->reader->setEnclosure('');
            //$this->reader->setLineEnding("\r\n");
            /* @phpstan-ignore-next-line */
            $this->reader->setSheetIndex(0);
        }

        $this->PHPExcel = $this->reader->load($this->fileName);
        $this->PHPExcel->setActiveSheetIndex(0);
    }

    public function openFile(string $filename) : bool
    {
        $this->initSetMemory();        
        if(file_exists($filename))
        {
            $this->setFilename($filename);
            if($this->findType())
            {
                $this->makeReader();
                return true;
            }           
        }
        return false;
    }

    public function openFileType(string $filename,string $type) : bool
    {
        $this->initSetMemory();
        if(file_exists($filename))
        {
            $this->setFilename($filename);
            $this->type=$type;
            $this->makeReader();
            return true;
        }
        return false;
    }

    public function saveFile(string $filename,string $type) : bool
    {
        $this->initSetMemory();
        if( /* !file_exists($filename) && */ $this->makeWriter($type))
        {
            //$this->outputFileName=$filename;
            $this->makeFileName($filename,$type);
            $this->writer->save($this->outputFileName);

            if(file_exists($this->outputFileName))
            {
                return true;
            }
        }
        return false;
    }
    
    public function saveFileXLSX(string $filename) : bool
    {
        $this->initSetMemory();
        /* @phpstan-ignore-next-line */
        $this->writer = PHPExcel_IOFactory::createWriter($this->PHPExcel,KExcel::$EXCEL7);
        if($this->writer!=null)
        {
            $this->writer->save($filename);
            if(file_exists($filename))
            {
                return true;
            }
        }
        return false;
    }    

    public function setActiveSheet(int $i) : bool
    {
        if($i < $this->PHPExcel->getSheetCount() && $i>= 0)
        {
            $this->PHPExcel->setActiveSheetIndex($i);
            return true;
        }
        return false;
    }

    public function getSheetCount() : int
    {
        return $this->PHPExcel->getSheetCount();
    }

    public function getSheetNameByIndex(int $i) : string 
    {
        return $this->PHPExcel->getSheet($i)->getTitle();
    }

    public function makeFileName(string $filename,string $type) : void
    {
        if($type==KExcel::$CSV)
        {
            $this->outputFileName=$filename.".csv";
        }
        elseif($type==KExcel::$EXCEL5)
        {
            $this->outputFileName=$filename.".xls";
        }
        elseif($type==KExcel::$EXCEL7)
        {
            $this->outputFileName=$filename.".xlsx";
        }
        elseif($type==KExcel::$ODS)
        {
            $this->outputFileName=$filename.".ods";
        }
        else
        {
            $this->outputFileName=$filename.".csv";
        }
    }


    public function modifyCell(mixed $column,mixed $the_line,mixed $content) : bool
    {
        $line=intval($the_line);
        if($this->makeColumn($column)&&isInteger($line))
        {
            $this->PHPExcel->getActiveSheet()->setCellValue($this->column.$line, $content);
            return true;
        }
        return false;
    }


    public function insertURL(mixed $column,mixed $the_line,mixed $url) : void
    {
        $line=intval($the_line);
        if($this->makeColumn($column))
        {
            $sheet = $this->PHPExcel->getActiveSheet();
            $sheet->getCell($this->column.$line)->getHyperlink()->setUrl($url);
        }
    }


    public function getCellValue(mixed $column,mixed $the_line) : mixed
    {
        $line=intval($the_line);
        if($this->makeColumn($column))
        {
            $value=$this->PHPExcel->getActiveSheet()->getCell($this->column.$line)->getValue();
            if($value instanceof PhpOffice\PhpSpreadsheet\RichText\RichText)
            {
                $value=$value->getPlainText();
            }
            return $value;
        }
        return null; 
    }


    private function makeColumn(mixed $the_column) : bool
    {
        $column= intval($the_column);
        if($column >0 &&  $column< count($this->columnName))
        {
            $this->column=$this->columnName[$column-1];
            return true;
        }
        return false;
    }
    
    public function getColumnCorrespondance(int $number) : string
    {
        if(($number-1)<count($this->columnName))
        {
            return $this->columnName[$number-1];
        }
        return "UNDEFINED";
    }


    public function getOwner() : string
    {
        return $this->owner;
    }

    public function setOwner(string $owner) : void
    {
        $this->owner=$owner;
    }


    public function setDefaultProperties() : void
    {
        $this->PHPExcel->getProperties()->setCreator($this->owner);
        $this->PHPExcel->getProperties()->setLastModifiedBy($this->owner);
        $this->PHPExcel->getProperties()->setTitle($this->owner);
        $this->PHPExcel->getProperties()->setSubject($this->owner);
        $this->PHPExcel->getProperties()->setDescription($this->owner);
        $this->PHPExcel->getProperties()->setKeywords($this->owner);
        $this->PHPExcel->getProperties()->setCategory($this->owner);
    }

    
    public function findColumn(mixed $the_name) : int
    {
        $name="".$the_name;
        $var=-1;
        for($i=0; $i< count($this->columnName) ; $i++)
        {
            //echo $this->columnName[$i]."1"."  -> ".$this->PHPExcel->getActiveSheet()->getCell($this->columnName[$i]."1")->getValue()."\n";
            //if(strncmp($this->PHPExcel->getActiveSheet()->getCell($this->columnName[$i]."1")->getValue(),$name,strlen($name))==0)
            if($this->PHPExcel->getActiveSheet()->getCell($this->columnName[$i]."1")->getValue()==$name)        
            {
                $var=$i+1;
                $i= count($this->columnName);
            }
        }
        return $var;
    }
    
    public function findColumnBool(mixed $the_name) : bool
    {
        if($this->findColumn($the_name)!=-1)
        {
            return true;
        }
        return false;
    }

    private int $columnNumberForContent=0;
    public function getLastColumnNumberForContent() : int
    {
        return $this->columnNumberForContent;
    }
    
    /**
     * 
     * @param string $column_name
     * @param bool $with_column_title
     * @return array<int,string>|null
     */
    public function getColumnContent(string $column_name,bool $with_column_title=true) : ?array
    { 
        $start_line=1;
        if(!$with_column_title)
        {
            $start_line=2;
        }
        //$this->initSetMemory();
        $array=array();

        $this->columnNumberForContent=$this->findColumn($column_name);
        //KDebugger::getInstance()->dump($column_name."==>".$colNumber,"colNumber");

        if($this->columnNumberForContent > 0 && $this->makeColumn($this->columnNumberForContent))
        {
            $nb=0;
            $nb=$this->PHPExcel->getActiveSheet()->getHighestRow();

            for($line=$start_line ; $line<=$nb ; $line++)
            {
                $array[]="".$this->PHPExcel->getActiveSheet()->getCell($this->column.$line)->getValue();
            }
        }

        if(count($array))
        {
            return $array;
        }
        return null;
    }
    
    //TO TEST
    /**
     * 
     * @return array<int,array<int,string>>
     */
    public function exportToArray() : array
    {
        $array=[];
        $max_row=$this->getMaxRow();
        $max_col=$this->getMaxColumn();        
        for($row=1 ; $row<=$max_row ; $row++)
        {
            $line=[];
            for($col=1 ; $col<=$max_col ; $col++)
            {            
                $line[]="".$this->getCellValue($col,$row);
            }
            $array[]=$line;
        }
        return $array;
    }

    /*
     * Renvoie des PHP object et non des chaines de caractères
     * Utiliser getLine(1) pour récupérer les noms de colonne sous type string
     */
    /**
     * 
     * @return array<int,mixed>|null
     */
    public function getFirstLine() : ?array
    {
        $array=array();

        $row=$this->getMaxRow();
        $column=$this->getMaxColumn();
          
        for($j=1 ; $j<= $column ; $j++)
        {
            if($this->getCellValue($j,1)=="")
            {
                break;
            }
            $array[]=$this->getCellValue($j,1);
        }

        if(count($array)> 0)
        {
            return $array;
        }
        return null;
    }

    /**
     * 
     * @param int $the_number
     * @return array<int,string>|null
     */
    public function getLine(mixed $the_number) : ?array
    {
        $number=intval($the_number);
        $array=array();

        $row=$this->getMaxRow();
        $column=$this->getMaxColumn();

//        echo "<br />#######################<br />";
//        echo "ROW    == ".$row;
//        echo "<br />";
//        echo "COLUMN == ".$column;
//        echo "<br />";
//        echo "LINE == ".$number;
        

        if($number <1  && $number>$row )
        {
//            echo "<br />#######################<br />";
            return null;
        }


        for($j=1 ; $j<= $column ; $j++)
        {
            $var="".$this->getCellValue($j,$number);
            $array[]=$var;
//            echo "<br />";
//            echo "Var[$j][$number] == ".$var;
        }


//        echo "<br />#######################<br />";
        if(count($array)> 0)
        {
            return $array;
        }
        return null;
    }


    public function getMaxRow() : int
    {
        return intval($this->PHPExcel->getActiveSheet()->getHighestRow());
    }

    public function getMaxColumn() : int
    {
        return $this->columnIndexFromString($this->PHPExcel->getActiveSheet()->getHighestColumn());
    }

    public static function columnIndexFromString(string $colString = 'A') : int
    {
    	// Convert to uppercase
    	$pString = strtoupper($colString);

        // Convert column to integer
        if (strlen($pString) == 1)
        {
            return (ord($pString[0]) - 64);
        }
        elseif (strlen($pString) == 2)
        {
            return $result = ((1 + (ord($pString[0]) - 65)) * 26) + (ord($pString[1]) - 64);
        }
        elseif (strlen($pString) == 3)
        {
            return ((1 + (ord($pString[0]) - 65)) * 676) + ((1 + (ord($pString[1]) - 65)) * 26) + (ord($pString[2]) - 64);
        }
        else
        {
            return -1;
            //throw new Exception("Column string index can not be " . (strlen($pString) != 0 ? "longer than 3 characters" : "empty") . ".");
        }
    }


    public function getType() : string
    {
        return $this->type;
    }

    public function getOutputFile() : string
    {
        return $this->outputFileName;
    }

/*
    public function makeFileFromArray($dest, $format,$array)
    {
        $this->type=$format;
        $this->fileName=$dest;
        $this->makeReader();

        $this->setDefaultProperties();
        $this->PHPExcel->setActiveSheetIndex(0);
        $this->PHPExcel->getActiveSheet()->setCellValue('A1', 'EAN');


        for($i=0; $i< count($array) ; $i++)
        {
            $value=$i+2;
            $this->PHPExcel->getActiveSheet()->setCellValue('A'.$value,$array[$i] );
        }      
        return $this->saveFile($dest, $format);
    }
*/

    /**
     * 
     * @param string $dest
     * @param string $type
     * @param array<int,mixed> $array
     * @return bool
     */
    public function makeFileFromRandomArray(string $dest,string $type,array $array):bool
    {
        $this->type=$type;
        $this->fileName=$dest;


        //$this->PHPExcel=new PHPExcel();
        $this->PHPExcel=new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->makeWriter($this->type);
        $this->setDefaultProperties();
        $this->PHPExcel->setActiveSheetIndex(0);
        $sheet=$this->PHPExcel->getActiveSheet();


        for($i=0; $i< count($array) ; $i++)
        {
            $value=$i+1;
            $sheet->setCellValue('A'.$value,$array[$i] );
        }
        return $this->saveFile($dest, $this->type);
        //return true;
    }
    
    /**
     * 
     * @param string $dest
     * @param string $type
     * @param array<mixed,mixed> $array
     * @return bool
     */
    public function makeFileFromDoubleArray(string $dest,string $type,array $array) : bool
    {
        $this->fileName=$dest;
        $this->type=$type;

        $this->PHPExcel=new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $this->makeWriter($this->type);
        $this->setDefaultProperties();
        $this->PHPExcel->setActiveSheetIndex(0);
        $sheet=$this->PHPExcel->getActiveSheet();
        //$sheet->
        
        $sheet->fromArray($array, NULL, 'A1'); 

//        for($i=0; $i< count($array) ; $i++)
//        {
//            $arrayTemp=$array[$i];
//            $var=$i+1;
//
//            for($j=0; $j< count($arrayTemp) ; $j++)
//            {
//                $sheet->setCellValue($this->columnName[$j].$var,$arrayTemp[$j] );
//            }
//        }
//        foreach(range('A','LZ') as $columnID) 
//        {
//            $sheet->getColumnDimension($columnID)->setAutoSize(true);
//        }        
        return $this->saveFile($dest, $this->type);
    }

    /**
     * 
     * @param string $dest
     * @param string $type
     * @param array<int,array<int,mixed>> $array
     * @return bool
     */
    public function makeFileFromDoubleArrayToText(string $dest,string $type,array $array)
    {
        $this->fileName=$dest;
        $this->type=$type;

        $this->PHPExcel=new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $this->makeWriter($this->type);
        $this->setDefaultProperties();
        $this->PHPExcel->setActiveSheetIndex(0);
        $sheet=$this->PHPExcel->getActiveSheet();
        $this->PHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode('@');
        //$sheet->

        for($i=0; $i< count($array) ; $i++)
        {
            $arrayTemp=$array[$i];
            $var=$i+1;

            for($j=0; $j< count($arrayTemp) ; $j++)
            {
                $sheet->setCellValue($this->columnName[$j].$var,$arrayTemp[$j] );
            }
        }
        foreach(range('A','LZ') as $columnID) 
        {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }        
        return $this->saveFile($dest, $this->type);
    }    
    
    /**
     * 
     * @param string $dest
     * @param string $type
     * @param array<int,mixed> $arraySheet 
     * @param array<int,array<int,mixed>> $array
     * @return bool
     */
    public function makeFileFromTripleArray(string $dest,string $type,array $arraySheet,array $array)
    {
        $this->fileName=$dest;
        $this->type=$type;
        $this->PHPExcel=new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->makeWriter($this->type);
        $this->setDefaultProperties();  
        
        for ($k = 0; $k < count($array); $k++)
        {
            $this->PHPExcel->createSheet(NULL, $k);
            $this->PHPExcel->setActiveSheetIndex($k);
            $sheet=$this->PHPExcel->getActiveSheet();
            $sheet->setTitle($arraySheet[$k]);
            $last=1;
            for($i=0; $i< count($array[$k]) ; $i++)
            {
                $arrayTemp=$array[$k][$i];
                $var=$i+1;
                
                if(count($arrayTemp)>$last)
                {
                    $last=count($arrayTemp);
                }                

                for($j=0; $j< count($arrayTemp) ; $j++)
                {
                    //echo $this->columnName[$j]."-".$var."-".$j."-".count($arrayTemp)."\n";
                    $sheet->setCellValue($this->columnName[$j].$var,$arrayTemp[$j] );
                    //$last=$this->columnName[$j];
                }
                //$sheet->getColumnDimension($this->columnName[$j])->setAutoSize(true);
            }
//            foreach(range('A',$last) as $columnID) 
//            {
            for($j=0; $j< $last ; $j++)
            {
                //echo $this->columnName[$j]."<br />";          
                $sheet->getColumnDimension($this->columnName[$j])->setAutoSize(true);
            }            
        }
        $this->PHPExcel->setActiveSheetIndex(0);
        return $this->saveFile($dest, $this->type);
    }
    
        /**
     * 
     * @param string $dest
     * @param string $type
     * @param array<string,array<int,mixed>> $array
     * @return bool
     */
    public function makeFileFromTripleArrayKey(string $dest,string $type,array $array)
    {
        $this->fileName=$dest;
        $this->type=$type;
        $this->PHPExcel=new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->makeWriter($this->type);
        $this->setDefaultProperties();  
        $k=0;
        
        foreach ($array as $key=>$arrayTable)
        {
            $this->PHPExcel->createSheet(NULL, $k);
            $this->PHPExcel->setActiveSheetIndex($k);
            $sheet=$this->PHPExcel->getActiveSheet();
            $sheet->setTitle($key);
            $last=0;
            $line_number=1;
            foreach($arrayTable as $line)
            {
                $j=0;
                foreach($line as $col)
                {
//                    var_dump($j);
//                    var_dump($line_number);
//                    var_dump($col);
//                    exit();
                    if($col instanceof stdClass)
                    {
                        if(isset($col->date))
                        {
                            $sheet->setCellValue($this->columnName[$j].$line_number, strval($col->date) );
                        }
                    }
                    else
                    {
                        $sheet->setCellValue($this->columnName[$j].$line_number, strval($col) );
                    }
                    $j++;
                    if($j>$last)
                    {
                        $last=$j;
                    }
                }
                $line_number++;
            }
            
            for($j=0; $j<($last-1) ; $j++)
            {        
                $sheet->getColumnDimension($this->columnName[$j])->setAutoSize(true);
            }  
            
            $k++;
            //break;
        }
        $this->PHPExcel->setActiveSheetIndex(0);
        return $this->saveFile($dest, $this->type);
    }

    
    public function toTable() : string
    {
//        $html="";
//        $row=$this->getMaxRow();
//        $column=$this->getMaxColumn();
//
//        for($i=0 ; $i< $row ; $i++)
//        {
//            for($j=0 ; $j< $column ; $j++)
//            {
//                $html.="<td>".$this->getCellValue($j,$i)."</td>";
//            }
//        }

        $html="<table>";

        $row=$this->getMaxRow();
        $column=$this->getMaxColumn();

        for($i=0 ; $i< $row ; $i++)
        {
            $html.="<tr>";
            for($j=0 ; $j< $column ; $j++)
            {
                $html.="<td>".$this->getCellValue($j,$i)."</td>";
            }
            $html.="</tr>";
        }
        $html.="</table>";
        return $html;
    }


    public function free() : void
    {
        unset($this->reader);
        unset($this->writer);
        unset($this->PHPExcel);
    }
    
    public function initSheet(string $dest,string $type) : mixed
    {
        $this->type=$type;
        $this->fileName=$dest;


        $this->PHPExcel=new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->makeWriter($this->type);
        $this->setDefaultProperties();
//        $this->PHPExcel->setActiveSheetIndex(0);
//        $sheet=$this->PHPExcel->getActiveSheet();
        return $this->PHPExcel;        
    }
    
//    public function getSheetByName($sheet_name_to_find)
//    {
//        $the_sheet=null;
//        $sheets = $this->PHPExcel->listWorksheetNames($sheet_name_to_find);
//        foreach ($sheets as $sheetname)
//        {
////            if($sheetname==$sheet_name_to_find)
////            {
////                $the_sheet
////            }
//            echo $sheetname."<br />";
//        }
//    }
    
    private function initSetMemory() : void
    {
        //ini_set('memory_limit','8192M');
        set_time_limit(0);        
        //ini_set("maximum_execution_time","600");
    }
    
    /*
     * Renvoie des PHP object et non des chaines de caractères
     * Utiliser getLine(1) pour récupérer les noms de colonne sous type string
     */
    /**
     * 
     * @param bool $firstLine
     * @return array<int,mixed>|null
     */
    public function convertToArray2D(bool $firstLine=false) : ?array
    {
        $row=$this->getMaxRow();
        $column=$this->getMaxColumn();
        
        $start=1;
        if(!$firstLine)
        {
            $start=2;
        }
        
        $array=[];
        for($i=$start;$i<=$row;$i++)
        {  
            $line=[];
            for($j=1;$j<=$column;$j++)
            {
                $line[]=$this->getCellValue($j,$i);
            }
            $array[]=$line;
        }

        if(count($array)> 0)
        {
            return $array;
        }
        return null;
    }    
}
