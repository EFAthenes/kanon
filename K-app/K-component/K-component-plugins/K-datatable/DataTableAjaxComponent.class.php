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
 * Description of DataTableAjaxComponent
 *
 * @author Louis Mulot
 */
class DataTableAjaxComponent extends AbstractDataTableComponent
{
    private ?KObject $kObject=null;
    private int $max_results=0;
    private ?KURL $ajaxUrl=null;


    /**
     * @var array<int,string>
     */
    private array $colsLabelField=[];     
    /**
     * @var array<string,string>
     */
    private array $colsDbField=[];      
    /**
     * 
     * @var array<mixed,mixed>|null
     */
    private ?array $arrayParam=null;
    

    function __construct(string $datatable_id, ?KObject $kObject,array $arrayColumns,int $max_results=0)
    {
        parent::__construct($datatable_id,$arrayColumns,null);

        $this->kObject=$kObject;
        $this->max_results=$max_results;
    }

    
    public function setAjaxURL(KURL $url) : void
    {
        $this->ajaxUrl=$url;
    }
    
    /**
     * 
     * @param array<mixed,mixed>|null $param
     * @return void
     */
    public function addParamToAjax(?array $param) : void
    {
        if(!is_null($param)&&count($param))
        {
            $this->arrayParam=$param;
        }
    }

     

    public function drawTable() : string
    {
       //table table-hover border-primary table-bordered table-striped dataTable 
        $html='
<div class="row">       
    <div class="col"> 
        <div id="datatables_buttons_bar_'.$this->getDatatable_id().'" class="float-right" style="padding-bottom:10px;">
        </div>    
    </div> 
</div>               
<table id="'.$this->getDatatable_id().'" class="table table-hover table-bordered table-striped " style="width:100%"><thead><tr>';

        foreach($this->getArrayColumns() as $column)
        {
            $html.='<th>'.$column.' </th>';
        }
        $html.='</tr></thead><tbody>';
        $html.='</tbody></table>  '; 
        
//        $columnsHidden="";
//        foreach($this->getArrayColumnsHidden() as $columnHidden)
//        {
//            if($columnsHidden!="")
//            {
//                $columnsHidden.=",";
//            }
//            $columnsHidden.='{ "visible": false, "targets": '.$columnHidden.' }';
//        }
//        if(!empty($columnsHidden))
//        {
//            $columnsHidden=',"columnDefs": [ '.$columnsHidden.' ]';
//        }
        
        $order_col="";
        if($this->getCol_number()>=0)
        {
            $desc="desc";
            if(!$this->getDesc())
            {
                $desc="asc";
            }       
            $order_col=',"order": [[ '.$this->getCol_number().', "'.$desc.'" ]]';
        }        
        
        
        $datatable_javascript_var="datatable_".$this->getDatatable_id();
        $default=false;
        if(is_null($this->ajaxUrl))
        {
            $default=true;
            $params=[   
                ShowEditTableElementAjax::$PARAM_CLASS_NAME=>$this->kObject->getClassName(),
                ShowEditTableElementAjax::$PARAM_MAX_RESULT=>$this->max_results];
            if(!is_null($this->arrayParam))
            {
                $params[ShowEditTableElementAjax::$PARAM_ADDITIONNAL]=json_encode($this->arrayParam);
            }
            $this->ajaxUrl=KRoute::makeActionKURL(RoutesItems::$SHOW_EDIT_TABLE_ELEMENT_AJAX, $params);
        }
        
        $html.='    
<script>
$(document).ready(function()
{        
    var '.$datatable_javascript_var.' = $("#'.$this->getDatatable_id().'").DataTable(
        {            
            "lengthMenu": [[2, 5, 10, 25, 50, 100, -1], [2, 5, 10, 25, 50, 100, "'.$this->getAll_result().'"]],
            "pageLength": 10,    
            "paging": '.$this->getPagingString().',
            "pagingType": "'.$this->getType_pagination().'",
            "mark" : true,
            '.$this->makeJSForLanguagesOptions().' 
            "scrollX": true,
            "processing": true,
            '.$this->makeColumnDefinition().'
            "serverSide": true,
            "ajax": {
                "url": "'.$this->ajaxUrl->printURLWithoutAmp().'",
                "type": "POST"
            } 
            ';
            

            if($default&&!is_null($this->kObject))
            {
                $html.='   
            ,"columns": [';
            
                //$list=$this->kObject->getListFieldName();
                $fieldsName=$this->getFieldsLabel();
                $fieldsDb=$this->getfieldsDb();
                
                $nb_field=0;
                foreach ($fieldsName as $field)
                {
                    if($nb_field>0)
                    { 
                        $html.=',';
                    }         
                    
                    $orderable=$this->makeOrderableColumn($field);
                    $html.='{ "data": "'.($field).'", "name":"'. $fieldsDb[$field].'" '.$orderable.$this->makeRenderColumm($nb_field).' }';
                    $nb_field++;                    
                }              
                $html.=']  
';
            }
            
            $html.='
            '.$order_col.'          
        }
    ); 
';
            //,"order": [[ 1, "asc" ]]
            //'.$datatable_javascript_var.'.page.draw();
        if($this->getButton_bar())
        {
            $html.=$this->makeJSForButtonBar();
        }
        
        $html.=' 
});
</script>
';
        return $html;
    }
    
    private function makeRenderComponent(?KComponent &$component) : void
    {
        if(is_null($component))
        {
            return;
        }
        
        if($component instanceof RowColsComponent
           ||$component instanceof RowColComponent 
                || $component instanceof RowComponent )
        {
            foreach ($component->getListComponent() as $subComp)
            {
                $this->makeRenderComponent($subComp);
            }
        }
        else if($component instanceof ColComponent )
        {
            foreach ($component->getListComponent() as $subComp)
            {
                $this->makeRenderComponent($subComp);
            }
        }        
        else if($component instanceof LinkDataTableComponent)
        {
            $component->addToLink("'+data+'");
            if($component->getListComponent()->getSize()==0)
            {
                $component->addHtmlComponent("'+data+'");
            }
        }
        else if($component instanceof HTMLComponent)
        {
            $component->addHTML("'+data+'");
        }
        else if($component instanceof DivClassComponent)
        {
            $component->addHtmlComponent("'+data+'");
        }
        else if($component instanceof ButtonDataTableComponent)
        {
            $component->setDynamicURL("'+data+'");
            $component->setDynamicLabel("'+data+'");
        }        
        
    }
    
    private function makeRenderColumm(int $column_index) : string
    {
        $renderer="";
        $arrayOfComponents=$this->getArrayOfComponents();
        if(array_key_exists($column_index, $arrayOfComponents))
        {
            $component=$arrayOfComponents[$column_index];
            
            $this->makeRenderComponent($component);
            
            $renderer=' ,render : function(data, type, row){ return \''.$this->removeLineBreaks($component->draw()).'\';} ';
        }
        return $renderer;
    }
    
    private function removeLineBreaks(string $s) : string
    {
        return str_replace(["\r", "\n"], '',$s);
    }
    
//    
//    /**
//     * 
//     * @param KObject $kobject
//     * @return array<int,string>
//     */
//    protected function makeColumns(KObject $kobject) : array
//    {
//        $arrayTablesDb_fields_name=$kobject->fieldsLabelToArray(false);
//        
//        if(count($this->colsDef))
//        {
//            $newArrayTablesColsField=[];
//            $newArrayTablesColsDb=[];
//            foreach ($this->colsDef as $colDef)
//            {
//                $db_field=$colDef[0];
//                $label_field=$colDef[1];
//                KDebugger::_($db_field, $label_field);
//                if(in_array($db_field, $arrayTablesDb_fields_name))
//                {
//                    $newArrayTablesColsField[]=$label_field;
//                    $newArrayTablesColsDb[$label_field]=$db_field;
//                    //KDebugger::_($newArrayTablesCols,"New Array");
//                }
//            }
//            if(count($newArrayTablesColsField))
//            {
//                $this->colsLabelField=$newArrayTablesColsField;
//                $this->colsDbField=$newArrayTablesColsDb;
//                return $this->colsLabelField;
//            }
//        }
//        
//        return $arrayTablesDb_fields_name;
//    }

    /*
    public function resetColumns(array $columnsDef) : bool
    {
        $colsDef=[];
        $i=0;
        $status=false;
        if(count($columnsDef))
        {
            foreach ($columnsDef as $columnDef)
            {
                if(count($columnDef)==2
                    &&!empty($columnDef[0])
                    &&!empty($columnDef[1]))
                {
                    $colsDef[$i]=$columnDef;
                    $status=true;
                    $i++;
                }
                else 
                {
                    return false;
                }
            }
        }
        if($status)
        {
            $this->colsDef=$colsDef;
            return true;
        }
        return false;
    }  
    */
    /**
     * 
     * @return array<mixed,mixed>
     */
    private function getFieldsLabel() : array
    {
        if(count($this->colsLabelField))
        {
            return $this->colsLabelField;
        }
        else   
        {
            return $this->kObject->fieldsLabelToArray(false);
        }
    }
    /**
     * 
     * @return array<string,string>
     */
    private function getFieldsDb() : array
    {
        if(count($this->colsDbField))
        {
            return $this->colsDbField;
        }
        else   
        {
            $array=$this->kObject->fieldsLabelToArray(false);
            $fieldsDb=[];
            foreach ($array as $field)
            {
                $fieldsDb[strval($field)]= ucfirst(strval($field));
            }
            return $fieldsDb;
        }
    } 
    
    /**
     * 
     * @param array<int,string> $colsLabelField
     * @return void
     */    
    public function setColsLabelField(array $colsLabelField): void
    {
        $this->colsLabelField = $colsLabelField;
    }

    /**
     * 
     * @param array<string,string> $colsDbField
     * @return void
     */
    public function setColsDbField(array $colsDbField): void
    {
        $this->colsDbField = $colsDbField;
    }

}