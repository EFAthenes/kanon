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
/**
 * Description of AbstractDataTableComponent
 *
 * @author Louis Mulot
 */
abstract class AbstractDataTableComponent extends KComponent
{   
    abstract public function drawTable() : string; 
    private string $datatable_id="";
    private string $datatable_javascript_var="";
    /**
     * 
     * @var array<int,mixed>
     */
    private array $arrayColumns=[];
    /**
     * 
     * @var array<int,mixed>
     */
    private array $arrayLines=[];
    /**
     * 
     * @var array<int,mixed>
     */    
    private array $arrayOfComponents=[];
    /**
     * 
     * @var array<int,mixed>
     */    
    private array $arrayColumnsHidden=[];
    /**
     * 
     * @var array<int,mixed>|null
     */    
    private ?array $arrayOfComponentsWithToolbar=null;
    /**
     * 
     * @var array<string,int>
     */
    private array $arrayColumnsNotOrderable=[];
    
    //-----------------------------------
    
    private bool $activatePaging=true;
    private bool $activateInfo=true;
    private bool $activateFilter=true;
    
    //-----------------------------------
    
    private string $lengthMenu="";
    private string $zeroRecords="";
    private string $info="";
    private string $infoEmpty="";
    private string $infoFiltered="";
    private string $search="";
    private string $all_result="";
    private int $defaultNumberOfResults=10;
    private int $startPage=1;
    
    private string $first="";
    private string $last="";
    private string $next="";
    private string $previous="";
    
    private string $button_copy="";
    private string $button_xls="";
    private string $button_pdf="";
    private string $button_csv="";
    private string $button_columns="";
    
    private int $col_number=-1;
    private bool $desc=true;    
    
    private bool $button_bar=false;
    private bool $jumpTo=false;
    private string $jumpValue="";
    private int $jumpColNumber=0;
    
    private ?string $htmlLines=null;
    private ?string $htmlHeader=null;
    
    private string $setColumAdjust="";
    private bool $setRowSelection=false;
    private bool $scrollX=true;
    
    private bool $unAccentResearch=true;
    
    public const string PAGINATION_NUMBERS="numbers";
    public const string PAGINATION_SIMPLE="simple";
    public const string PAGINATION_SIMPLE_NUMBERS="simple_numbers";
    public const string PAGINATION_FULL="full";
    public const string PAGINATION_FULL_NUMBERS="full_numbers";
    public const string PAGINATION_FIRST_LAST_NUMBERS="first_last_numbers";
    private string $type_pagination="";
    private bool $paging=true;
    private string $version="2.3.8";
    
    public function draw() : string
    {
        return $this->drawTable().parent::draw();
    }    
    /**
     * 
     * @param string $datatable_id
     * @param array<int,mixed> $arrayColumns
     * @param array<int,mixed>|null $arrayLines
     */
    function __construct(string $datatable_id,array $arrayColumns,?array $arrayLines)
    {
        parent::__construct();
        $this->setNone();
        
        KDebugger::getInstance()->dump(LanguageManager::getInstance()->getLanguage(),"Lang");
        
        $layout=KApp::getInstance()->getLayout();
        if($this->version=="2.3.8")
        {
            $layout->addJsFileToBuffer(__DIR__."/js/2.3.8/datatables.min.js",true);
            $layout->addCSSFileToBuffer(__DIR__."/js/2.3.8/datatables.min.css");
        }
        else
        {
            $layout->addJsFileToBuffer(__DIR__."/js/jquery.dataTables.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/dataTables.bootstrap.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.ellipsis.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.buttons.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.pdfmake.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.jszip.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatable.vfs_font.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.html5buttons.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.buttons.bootstrap4.min.js",true);
            $layout->addJsFileToBuffer(__DIR__."/js/datatables.buttons.colvis.min.js",true);
        }
        $this->datatable_id=$datatable_id;
        $this->datatable_javascript_var="datatable_".$this->datatable_id;
        if(!is_null($arrayLines))
        {
            $this->arrayLines=$arrayLines;
        }
        $this->arrayColumns=$arrayColumns;
        
        $this->lengthMenu=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_1");
        $this->zeroRecords=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_2");
        $this->info=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_3");
        $this->infoEmpty=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_4");
        $this->infoFiltered=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_5");
        $this->search=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_6");
        $this->all_result=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_7");

        $this->first=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_PAGINATE_1");
        $this->last=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_PAGINATE_2");
        $this->next=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_PAGINATE_3");
        $this->previous=LanguageManager::_("DATATABLE_SIMPLE_TABLE_COMPONENT_PAGINATE_4"); 
        
        $this->button_copy=LanguageManager::_("DATATABLE_SIMPLE_TABLE_BUTTON_COPY");
        $this->button_xls=LanguageManager::_("DATATABLE_SIMPLE_TABLE_BUTTON_XLS");
        $this->button_pdf=LanguageManager::_("DATATABLE_SIMPLE_TABLE_BUTTON_PDF");
        $this->button_csv=LanguageManager::_("DATATABLE_SIMPLE_TABLE_BUTTON_CSV");
        $this->button_columns=LanguageManager::_("DATATABLE_SIMPLE_TABLE_BUTTON_COL");
        
        $this->type_pagination=self::PAGINATION_FULL_NUMBERS;
    }

    public function setComponentForColumn(string $columnName,KComponent $component) : void
    {
        if(($pos=array_search($columnName,$this->arrayColumns))!==false)
        {
            $this->arrayOfComponents[$pos]=$component;
        }
    }
    
    public function setColumnNotVisible(mixed $columnNumber) : bool
    {
        if(isInteger($columnNumber)&& intval($columnNumber)>=0 && intval($columnNumber) <  count($this->arrayColumns))
        {
            $this->arrayColumnsHidden[]=intval($columnNumber);
            return true;
        }
        return false;
    }
    /**
     * 
     * @param array<int,mixed> $columnsNumber
     * @return bool
     */
    public function setColumnsNotVisible(array $columnsNumber) : bool
    {
        $status=true;
        foreach ($columnsNumber as $number)
        {
            if(!$this->setColumnNotVisible($number))
            {
                $status=false;
            }
        }
        return $status;
    }
    

    public function setColumnNotOrderable(int $columnNumber,string $colName) : bool
    {
        if(isInteger($columnNumber)&& $columnNumber>=0 && $columnNumber <  count($this->arrayColumns))
        {
            $this->arrayColumnsNotOrderable[$colName]=$columnNumber;
            return true;
        }
        return false;
    }    
    
    public function setColumnNotOrderableByName(string $colName) : bool
    {
        $count=0;
        foreach ($this->arrayColumns as $colum_name)
        {
            if(ucfirst($colName)==$colum_name)
            {
                return $this->setColumnNotOrderable($count,$colName);
            }
            $count++;
        }       
        return false;
    }   
    
    protected function makeOrderableColumn(string $column_name) : string
    {
        $orderable=', "orderable": true';
        if(array_key_exists($column_name, $this->arrayColumnsNotOrderable))
        {
            $orderable=', "orderable": false';
        }   
        return $orderable;
    }   

       
    public function setColumnNotVisibleByName(string $colName) : bool
    {
        $count=0;
        foreach ($this->arrayColumns as $colum_name)
        {
            if(ucfirst($colName)==$colum_name||$colName==$colum_name )
            {
                return  $this->setColumnNotVisible($count);
            }
            $count++;
        }       
        return false;
    } 
     
    
    public function getButton_bar() : bool
    {
        return $this->button_bar;
    }

    public function setButton_bar(bool $button_bar) : self
    {
        $this->button_bar=$button_bar;
        return $this;
    }
    
    public function setStartPage(int $page) : bool
    {
        if($page>0)
        {
            $this->startPage=$page;
            return true;
        }
        return false;
    }
    
    public function jumpToColValue(int $jumpColNumber,string $jumpValue) : bool
    {
        if($jumpColNumber>=0)
        {
            $this->jumpTo=true;
            $this->jumpValue=$jumpValue;
            $this->jumpColNumber=$jumpColNumber;
            return true;
        }
        return false;
    }
    
    public function addComponentToButtonBar(KComponent $component,int $col_number=2) : void
    {
        if(is_null($this->arrayOfComponentsWithToolbar))
        {
            $this->arrayOfComponentsWithToolbar=[];
        }
        if($col_number<0)
        {
            $col_number=1;
        }
        elseif($col_number>12)
        {
            $col_number=12;
        }
        $this->arrayOfComponentsWithToolbar[]=[$component,$col_number];
    }
    
    public function setHtmlHeader(string $htmlHeader) : void
    {
        $this->arrayColumns=[];
        $this->htmlHeader=$htmlHeader;
    }
    
    public function getStartPage(): int
    {
        return $this->startPage;
    }

    public function getHtmlLines(): ?string
    {
        return $this->htmlLines;
    }

    public function getHtmlHeader(): ?string
    {
        return $this->htmlHeader;
    }
    
    public function setHtmlLines(string $htmlLines) : void
    {
        $this->arrayLines=[];
        $this->htmlLines=$htmlLines;        
    }    
    
    public function getDatatable_javascript_var() : string
    {
        return $this->datatable_javascript_var;
    }
    
    
    
    public function setColumnAdjust() : string
    {
        $this->setColumAdjust=' 
<script>            
$(document).ready(function()
{     
    '.$this->datatable_javascript_var.'.columns.adjust();
    console.log("adjust"):
});
</script>
';   
        return $this->setColumAdjust;
    }
    
    public function getLengthMenu() : string
    {
        return $this->lengthMenu;
    }

    public function getZeroRecords() : string
    {
        return $this->zeroRecords;
    }

    public function getInfo() : string
    {
        return $this->info;
    }

    public function getInfoEmpty() : string
    {
        return $this->infoEmpty;
    }

    public function getInfoFiltered() : string
    {
        return $this->infoFiltered;
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getAll_result(): string
    {
        return $this->all_result;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function getNext(): string
    {
        return $this->next;
    }

    public function getPrevious() : string
    {
        return $this->previous;
    }

    public function setLengthMenu(string $lengthMenu) : void
    {
        $this->lengthMenu=$lengthMenu;
    }

    public function setZeroRecords(string $zeroRecords) : void
    {
        $this->zeroRecords=$zeroRecords;
    }

    public function setInfo(string $info) : void
    {
        $this->info=$info;
    }

    public function setInfoEmpty(string $infoEmpty) : void
    {
        $this->infoEmpty=$infoEmpty;
    }

    public function setInfoFiltered(string $infoFiltered) : void
    {
        $this->infoFiltered=$infoFiltered;
    }

    public function setSearch(string $search) : void
    {
        $this->search=$search;
    }

    public function setAll_result(string $all_result) : void
    {
        $this->all_result=$all_result;
    }

    public function setFirst(string $first) : void
    {
        $this->first=$first;
    }

    public function setLast(string $last) : void
    {
        $this->last=$last;
    }

    public function setNext(string $next) : void
    {
        $this->next=$next;
    }

    public function setPrevious(string $previous) : void
    {
        $this->previous=$previous;
    }
    
    public function getDefaultNumberOfResults() : int
    {
        return $this->defaultNumberOfResults;
    }

    public function setDefaultNumberOfResults(int $defaultNumberOfResults) : self
    {
        if($defaultNumberOfResults > 0)
        {
            $this->defaultNumberOfResults = $defaultNumberOfResults;
        }
        return $this;
    }

    public function activatePaging(bool $activate) : void
    {
        $this->activatePaging=$activate;
    }
    public function activateFilter(bool $activate) : void
    {
        $this->activateFilter=$activate;
    }
    public function activateInfo(bool $activate) : void
    {
        $this->activateInfo=$activate;
    }    
    
    public function setOrderByColumn(int $col_number,bool $desc=true) : void
    {
        $this->col_number=$col_number;
        $this->desc=$desc;
    }
    
    public function setPaginationType(string $type) : bool
    {
        if($type== self::PAGINATION_NUMBERS)
        {
            $this->type_pagination=self::PAGINATION_NUMBERS;
        }
        else if($type== self::PAGINATION_SIMPLE)
        {
            $this->type_pagination=self::PAGINATION_SIMPLE;
        } 
        else if($type== self::PAGINATION_SIMPLE_NUMBERS)
        {
            $this->type_pagination=self::PAGINATION_SIMPLE_NUMBERS;
        } 
        else if($type== self::PAGINATION_FULL)
        {
            $this->type_pagination=self::PAGINATION_FULL;
        } 
        else if($type== self::PAGINATION_FULL_NUMBERS)
        {
            $this->type_pagination=self::PAGINATION_FULL_NUMBERS;
        } 
        else if($type== self::PAGINATION_FIRST_LAST_NUMBERS)
        {
            $this->type_pagination=self::PAGINATION_FIRST_LAST_NUMBERS;
        } 
        else
        {
            return false;
        }
        return true;
    }
    public function setPaging(bool $paging) : self
    {
        $this->paging=$paging;
        return $this;
    }
    public function getPaging() : bool
    {
        return $this->paging;
    } 
    protected function getPagingString() : string
    {
        if($this->paging)
        {
            return 'true';
        }
        return 'false';
    }
    
    public function setUnaccentResearch(bool $unAccentResearch) : self
    {
        $this->unAccentResearch=$unAccentResearch;
        return $this;
    }
    public function getUnaccentResearch() : bool
    {
        return $this->unAccentResearch;
    }
    
    public function addSelectRowFunction() : void
    {
        $this->setRowSelection=true;
    }
    
    public function getDatatable_id(): string
    {
        return $this->datatable_id;
    }

    /**
     * 
     * @return array<int,mixed>
     */
    public function getArrayColumns(): array
    {
        return $this->arrayColumns;
    }
    /**
     * 
     * @return array<int,mixed>
     */
    public function getArrayLines(): array
    {
        return $this->arrayLines;
    }
    /**
     * 
     * @return array<int,mixed>
     */
    public function getArrayOfComponents(): array
    {
        return $this->arrayOfComponents;
    }
    /**
     * 
     * @return array<int,mixed>
     */
    public function getArrayColumnsHidden(): array
    {
        return $this->arrayColumnsHidden;
    }
    /**
     * 
     * @return array<int,mixed>|null
     */
    public function getArrayOfComponentsWithToolbar(): ?array
    {
        return $this->arrayOfComponentsWithToolbar;
    }

    public function getActivatePaging(): bool
    {
        return $this->activatePaging;
    }

    public function getActivateInfo(): bool
    {
        return $this->activateInfo;
    }

    public function getActivateFilter(): bool
    {
        return $this->activateFilter;
    }

    public function getButton_copy(): string
    {
        return $this->button_copy;
    }

    public function getButton_xls(): string
    {
        return $this->button_xls;
    }

    public function getButton_pdf(): string
    {
        return $this->button_pdf;
    }

    public function getButton_csv(): string
    {
        return $this->button_csv;
    }

    public function getButton_columns(): string
    {
        return $this->button_columns;
    }

    public function getCol_number(): int
    {
        return $this->col_number;
    }

    public function getDesc(): bool
    {
        return $this->desc;
    }

    public function getJumpTo(): bool
    {
        return $this->jumpTo;
    }

    public function getJumpValue(): string
    {
        return $this->jumpValue;
    }

    public function getJumpColNumber(): int
    {
        return $this->jumpColNumber;
    }

    public function getSetColumAdjust(): string
    {
        return $this->setColumAdjust;
    }

    public function getSetRowSelection(): bool
    {
        return $this->setRowSelection;
    }

    public function getType_pagination(): string
    {
        return $this->type_pagination;
    }

    public function setDatatable_id(string $datatable_id): void
    {
        $this->datatable_id = $datatable_id;
    }

    /**
     * 
     * @param array<int,mixed> $arrayColumns
     * @return void
     */
    public function setArrayColumns(array $arrayColumns): void
    {
        $this->arrayColumns = $arrayColumns;
    }

    /**
     * 
     * @param array<int,mixed> $arrayLines
     * @return void
     */
    public function setArrayLines(array $arrayLines): void
    {
        $this->arrayLines = $arrayLines;
    }

    /**
     * 
     * @param array<int,mixed> $arrayOfComponents
     * @return void
     */
    public function setArrayOfComponents(array $arrayOfComponents): void
    {
        $this->arrayOfComponents = $arrayOfComponents;
    }

    /**
     * 
     * @param array<int,mixed> $arrayColumnsHidden
     * @return void
     */
    public function setArrayColumnsHidden(array $arrayColumnsHidden): void
    {
        $this->arrayColumnsHidden = $arrayColumnsHidden;
    }

    /**
     * 
     * @param array<int,mixed>|null $arrayOfComponentsWithToolbar
     * @return void
     */
    public function setArrayOfComponentsWithToolbar(?array $arrayOfComponentsWithToolbar): void
    {
        $this->arrayOfComponentsWithToolbar = $arrayOfComponentsWithToolbar;
    }

    public function setActivatePaging(bool $activatePaging): void
    {
        $this->activatePaging = $activatePaging;
    }

    public function setActivateInfo(bool $activateInfo): void
    {
        $this->activateInfo = $activateInfo;
    }

    public function setActivateFilter(bool $activateFilter): void
    {
        $this->activateFilter = $activateFilter;
    }

    public function setButton_copy(string $button_copy): void
    {
        $this->button_copy = $button_copy;
    }

    public function setButton_xls(string $button_xls): void
    {
        $this->button_xls = $button_xls;
    }

    public function setButton_pdf(string $button_pdf): void
    {
        $this->button_pdf = $button_pdf;
    }

    public function setButton_csv(string $button_csv): void
    {
        $this->button_csv = $button_csv;
    }

    public function setButton_columns(string $button_columns): void
    {
        $this->button_columns = $button_columns;
    }

    public function setCol_number(int $col_number): void
    {
        $this->col_number = $col_number;
    }

    public function setDesc(bool $desc): void
    {
        $this->desc = $desc;
    }

    public function setJumpTo(bool $jumpTo): void
    {
        $this->jumpTo = $jumpTo;
    }

    public function setJumpValue(string $jumpValue): void
    {
        $this->jumpValue = $jumpValue;
    }

    public function setJumpColNumber(int $jumpColNumber): void
    {
        $this->jumpColNumber = $jumpColNumber;
    }

    public function setSetColumAdjust(string $setColumAdjust): void
    {
        $this->setColumAdjust = $setColumAdjust;
    }

    public function setSetRowSelection(bool $setRowSelection): void
    {
        $this->setRowSelection = $setRowSelection;
    }

    public function setType_pagination(string $type_pagination): void
    {
        $this->type_pagination = $type_pagination;
    }
    
    public function getScrollX(): bool 
    {
        return $this->scrollX;
    }

    public function setScrollX(bool $scrollX): void 
    {
        $this->scrollX = $scrollX;
    }
    
    protected function printSelectRowFunction() : string
    {
        $js="";
        if($this->setRowSelection)
        {
        $js=" 
    ".$this->datatable_javascript_var.".on('click', 'tbody tr', (e) => {
        let classList = e.currentTarget.classList;

        
        console.log(classList);
        if (classList.contains('data_row_selected')) 
        {
            classList.remove('data_row_selected');
        }
        else 
        {
            ".$this->datatable_javascript_var.".rows('.data_row_selected').nodes().each((row) => row.classList.remove('data_row_selected'));
            classList.add('data_row_selected');
        }
    });
";
        }
        return $js;
    }
    
    protected function makeColumnDefinition() : string
    {
        $columnsHidden="";
        foreach($this->getArrayColumnsHidden() as $columnHidden)
        {
            $columnsHidden.='{ "visible": false, "targets": '.$columnHidden.' },';
        }
        if(!empty($columnsHidden))
        {
            $columnsHidden='"columnDefs": [ '.$columnsHidden.' ],';
        }  
        return $columnsHidden;
    }
    
    protected function makeJSForLanguagesOptions() : string
    {
        $html='
        "language": {
            "lengthMenu": "'.$this->getLengthMenu().'",
            "zeroRecords": "'.$this->getZeroRecords().'",
            "info": "'.$this->getInfo().'",
            "infoEmpty": "'.$this->getInfoEmpty().'",
            "infoFiltered": "'.$this->getInfoFiltered().'",
            "search": "'.$this->getSearch().' :",               
            "paginate": {
                "first":"'.$this->getFirst().'",
                "last":"'.$this->getLast().'",
                "next":"'.$this->getNext().'",
                "previous":"'.$this->getPrevious().'"
            },  
        },    
            ';
        return $html;
    }
    
    protected function makeJSForButtonBar() : string
    {
        $html=' 
    new $.fn.dataTable.Buttons( '.$this->getDatatable_javascript_var().', {
        buttons: [
            { extend: "copyHtml5", text: "'.$this->getButton_copy().'" },
            { extend: "csv", text: "'.$this->getButton_csv().'" },
            { extend: "excel", text: "'.$this->getButton_xls().'" },
            { extend: "pdf", text: "'.$this->getButton_pdf().'",orientation: "landscape" },
            {
                text: "JSON",
                action: function ( e, dt, button, config ) 
                {
                    let data = dt.buttons.exportData();
                    $.fn.dataTable.fileSave(
                        new Blob( [ JSON.stringify( data ) ] ),
                        "Export.json"   
                    );
                }
            },               
            { extend: "colvis", text: "'.$this->getButton_columns().'" },
        ]
    });
    '.$this->getDatatable_javascript_var().'.buttons().container().appendTo($("#datatables_buttons_bar_'.$this->getDatatable_id().'"));
';         
        return $html;
    }
            
}