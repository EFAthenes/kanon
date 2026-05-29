<?php
/**
 * Description of KCSV
 *
 */

class KCSV
{
    private string $fileName="";
    private string $outputFileName="";
    private string $delimiter=";";
    /**
     * 
     * @var array<int,mixed>|null
     */
    private ?array $data=null;
    private int $nb_row=-1;
    private int $nb_col=-1;
    private string $enclosure="\"";
    private string $escape="\\";

    public function __construct()
    {
        
    }
    public function getOutputFileName() : string
    {
        return $this->outputFileName;
    }

    public function setOutputFileName(string $outputFileName)  : void
    {
        $this->outputFileName = $outputFileName;
    }

    /**
     * 
     * @return array<int,array<int,string|null>>|null
     */
    public function getData() :?array
    {
        return $this->data;
    }

    /**
     * 
     * @param array<int,array<int,string|null>> $data
     * @return void
     */
    public function setData(array $data) :void
    {
        $this->data = $data;
    }

    public function getEnclosure() : string
    {
        return $this->enclosure;
    }

    public function setEnclosure(string $enclosure) :void
    {
        $this->enclosure = $enclosure;
    }
    
    public function getEscape(): string
    {
        return $this->escape;
    }

    public function setEscape(string $escape): void
    {
        $this->escape = $escape;
    }

    
    
    public function setFilename(string $fileName): void
    {
        $this->fileName=$fileName;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter=$delimiter;
    }


    public function openFile(mixed $filename) : bool
    {
        $this->fileName="".$filename;
        if(empty($this->fileName))
        {
            return false;
        }
        $this->data=null;
        $this->nb_row=-1;
        $this->nb_col=-1;        
        
        
        if (($handle = fopen($filename, "r")) !== FALSE) 
        {
            $this->data=array();
            $line=null;
            $this->nb_row=0;
            while (($line = fgetcsv($handle, null,$this->delimiter,$this->enclosure,$this->escape )) !== FALSE) 
            {
                $this->nb_row++;
                $this->data[]=$line;
                if(count($line)>$this->nb_col)
                {
                    $this->nb_col=count($line);
                }
            }
            fclose($handle);
        } 
        if(is_array($this->data) && count($this->data))
        {
            return true;
        }
        return false;
    }

    public function saveFile(mixed $filename) : bool
    {
        $this->fileName="".$filename;
        if(empty($this->fileName))
        {
            return false;
        }
        $this->nb_row=-1;
        $this->nb_col=-1;              
              
        if(!is_array($this->data) || count($this->data)<=0 || !is_array($this->data[0]) )
        {
            return false;
        }

        if (($handle = fopen($filename, 'w')) !== FALSE) 
        {      
            $this->nb_row=0;
            foreach ($this->data as $row) 
            {
                $this->nb_row++;
                fputcsv($handle, $row,$this->delimiter);
                if(count($row)>$this->nb_col)
                {
                    $this->nb_col=count($row);
                }            
            }
            return fclose($handle);
        }
        return false;
    }

    public function makeFileName(string $filename) : void
    {
        $the_filename="".$filename;
        if(empty($the_filename))
        {
            $the_filename="default_".KRandom::makeRandomString(8);
        }
        $this->outputFileName=$the_filename.".csv";
    }
    
    public function findColumn(mixed $the_name) : int
    {
        $name="".$the_name;
        $column_number=-1;
        
        if(empty($name) 
                || $this->data==null 
                || array_key_exists(0,$this->data) 
                || !is_array($this->data[0]) )
        {
            return $column_number;
        }
        
        for ($i = 0; $i < count($this->data[0]); $i++) 
        {
            if($name==$this->data[0][$i])
            {
                $column_number=$i;
            }     
        }
        return $column_number;
    }

    public function getColumnContent(mixed $the_column_name) : mixed
    { 
        $column_name="".$the_column_name;
        if(empty($column_name)|| !is_array($this->data) || count($this->data)==0 || !is_array($this->data[0]) )
        {
            return null;
        }
        
        $array=array();

        $colNumber=$this->findColumn($column_name);

        if($colNumber >= 0)
        {
            for ($i = 0; $i < count($this->data); $i++) 
            {
                if(count($this->data[$i])>$colNumber)
                {
                    $array[]=$this->data[$i][$colNumber];
                }
            }
        }

        if(count($array)> 0)
        {
            return $array;
        }
        return null;
    }
    
    public function getCellValue(int $line ,int $col) : ?string
    {
        if(!is_null($this->data)&& array_key_exists($line, $this->data))
        {
            $the_line=$this->data[$line];
            if(!is_null($the_line)&& array_key_exists($col,$the_line))
            {
                return $the_line[$col];
            }
        }
        return null;
    }

    /**
     * 
     * @return array<int,string>|null
     */
    public function getFirstLine() : ?array
    {
        $array=array();

        $row=$this->getMaxRow();
        $column=$this->getMaxColumn();
          
        for($j=1 ; $j<= $column ; $j++)
        {
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
    public function getLine(mixed $the_number):?array
    {
        $number=intval($the_number);
        if(is_null($this->data) || !array_key_exists($number,$this->data) )
        {
            return null;
        }        
        return $this->data[$number];
    }


    public function getMaxRow() : int
    {
        return $this->nb_row;
    }

    public function getMaxColumn() : int
    {
        return $this->nb_col;
    }

    public function getOutputFile() : string
    {
        return $this->outputFileName;
    }
    public function free() : void
    {

    }
}
