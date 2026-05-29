<?php
/**
 * Description of KInputDate
 *
 * @author Louis Mulot
 */
class ExcelLinkJs extends KComponent
{ 
    private string $function_name="";
    /**
     * 
     * @param string $nameInput
     * @param array<int,array<int,mixed>> $array
     * @param string $filename
     */
    function __construct(string $nameInput,array $array,string $filename="")
    { 
        parent::__construct();
        
        $this->setName(convertStringToBeW3C_Id($nameInput));
        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/sheet.js");
        
        if(empty($filename))
        {
            $filename="excel_file-".KRandom::makeRandomString(8);
        }
        else
        {
            $filename=addslashes($filename);
        }
        
        $js_array='';
        foreach ($array as $line)
        {
            $line_js='';
            foreach ($line as $col)
            {         
                if(!empty($line_js))
                {
                    $line_js.=',';
                }
                $line_js.="'".addslashes("".strval($col))."'";
            }
            if(!empty($js_array))
            {
                $js_array.=',';
            }
            $js_array.="[".$line_js."]";
        }
        
        $this->function_name='clickButton_'.$this->getName();

        $js='
<script>            
function makeJsExcel_'.$this->getName().'() 
{    
  /* fetch JSON data and parse */

    const wb = XLSX.utils.book_new();
    var ws_name = "SheetJS";

    /* Create worksheet */
    var ws_data = ['.$js_array.'];
    var ws = XLSX.utils.aoa_to_sheet(ws_data);

    /* Add the worksheet to the workbook */
    XLSX.utils.book_append_sheet(wb, ws, ws_name);


//  const max_width = rows.reduce((w, r) => Math.max(w, r.name.length), 10);
//  worksheet["!cols"] = [ { wch: max_width } ];

    /* create an XLSX file and try to save to Presidents.xlsx */
    XLSX.writeFile(wb, "'.$filename.'.xlsx");
}
function '.$this->function_name.'()
{
    (async() => 
    {
        makeJsExcel_'.$this->getName().'();
    })();
}
</script>
';
        
        
        
        $this->addHtmlComponent($js);
        
//        $button= new ButtonComponent("Exporter un XLS des erreurs");
//        $button->setType(ButtonComponent::$TYPE_INFO);
//        $button->setClickAction('clickButton_'.$this->getName().'();');
//        $this->addComponent($button);
    }  
    
    
    public function getFunction_nameStringJs(): string
    {
        return $this->function_name.'();';
    }    
    public function getFunction_name(): string
    {
        return $this->function_name;
    }

    public function setFunction_name(string $function_name): void
    {
        $this->function_name = $function_name;
    }


}