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
 * Description of DataTableSimpleTableComponent
 *
 * @author Louis Mulot
 */
class DataTableSimpleTableComponent extends AbstractDataTableComponent
{
    /**
     * 
     * @param string $datatable_id
     * @param array<int,mixed> $arrayColumns
     * @param array<int,mixed> $arrayLines
     */
    final function __construct(string $datatable_id,array $arrayColumns,array $arrayLines)
    {
        parent::__construct($datatable_id,$arrayColumns,$arrayLines);
        $this->setNone();
        
        $this->addStyleCode('width:100%;');
    }


    public function drawTable() : string
    {
        $html='';
        if($this->getButton_bar())
        {
        $html='
<div class="row">
';
        $arrayToolBar=$this->getArrayOfComponentsWithToolbar();
        if(!is_null($arrayToolBar))
        {
            /* @var $comp KComponent */
            foreach ($arrayToolBar as $comp)
            {
                $col_value="";
                if($comp[1]!=0)
                {
                    $col_value="-".$comp[1];
                }
                $html.='<div class="col'.$col_value.'">'.$comp[0]->draw().'</div>';
            }
        }
        $html.='
    <div class="col"> 
        <div id="datatables_buttons_bar_'.$this->getDatatable_id().'" class="float-right" style="padding-bottom:5px;">
        </div>    
    </div> 
</div> ';          
        }
        $html.='     
<table id="'.$this->getDatatable_id().'" class="table table-hover table-bordered table-striped dataTable" style="'.$this->getStyleCode().'"><thead><tr>';

        if(!is_null($this->getHtmlHeader()))
        {
             $html.=$this->getHtmlHeader();
        }
        else
        {
            foreach($this->getArrayColumns() as $column)
            {
                $html.='<th>'.$column.' </th>';
            }
        }
        
        $html.='</tr></thead><tbody>';
        
        if(!is_null($this->getHtmlLines()))
        {
            $html.=$this->getHtmlLines();
        }
        else
        {
            foreach($this->getArrayLines() as $line)
            {
                if(is_array($line))
                {
                    $col_number=0;
                    $html.='<tr>';
                    $arrayOfComponents=$this->getArrayOfComponents();
                    foreach($line as $column)
                    {
                        if(isset($arrayOfComponents[$col_number]))
                        {
                            $component=$arrayOfComponents[$col_number];
                            $component->clearComponentList();
                            $component->addComponent(new HTMLComponent("".$column));
                            if($component instanceof LinkDataTableComponent)
                            {
                                $component= clone ($component);
                                $component->addToLink($column);
                            }
                            $html.='<td>'.$component->draw().' </td>';
                        }
                        else if($column instanceof KComponent)
                        {
                            $html.='<td>'.$column->draw().' </td>';
                        }
                        else
                        {
                            if(is_null($column))
                            {
                                $column="null";
                            }
                            elseif(is_array($column))
                            {
                                $col_temp="";
                                foreach ($column as $col)
                                {
                                    $col_temp.="".$col;
                                }
                                $column=$col_temp;
                            }
                            $html.='<td>'. FormComponent::inputString("".$column).' </td>';
                        }
                        $col_number++;
                    }
                    $html.='</tr>';
                }
                else
                {
                    $html.='<tr><td>'. FormComponent::inputString("".$line).' </td></tr>';
                }
            }
        }
        $html.='</tbody></table>   
  
';
     
        $special_options="";
        if(!$this->getActivatePaging())
        {
            $special_options.='"paging": false,';
        }
        if(!$this->getActivateInfo())
        {
            $special_options.='"info": false,';
        }
        if(!$this->getActivateFilter())
        {
            $special_options.='"searching": false,';
        }      
        $order_col="";
        if($this->getCol_number()>=0)
        {
            $desc="desc";
            if(!$this->getDesc())
            {
                $desc="asc";
            }       
            $order_col='"order": [[ '.$this->getCol_number().', "'.$desc.'" ]]';
        }
        
        $html.='    
<script>
let '.$this->getDatatable_javascript_var().';

$(document).ready(function()
{    
';
        if($this->getJumpTo())
        {
            $html.=' 
        jQuery.fn.dataTable.Api.register( "page.jumpToData()", function ( data, column ) 
        {
            var pos = this.column(column, {order:"current"}).data().indexOf( data );
            if ( pos >= 0 ) {
                var page = Math.floor( pos / this.page.info().length );
                this.page( page ).draw( false );
            }
            return this;
        });   
';
        }
        
        if($this->getUnaccentResearch())
        {
            $html.='
        function removeAccents_'.$this->getDatatable_id().' ( data ) {
            if ( data.normalize ) {
                // Use I18n API if avaiable to split characters and accents, then remove
                // the accents wholesale. Note that we use the original data as well as
                // the new to allow for searching of either form.
                return data +" "+ data
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "");
            }

            return data;
        }

        let searchType_'.$this->getDatatable_id().' = jQuery.fn.DataTable.ext.type.search;

        searchType_'.$this->getDatatable_id().'.string = function ( data ) {
            return ! data ?
                "" :
                typeof data === "string" ?
                    removeAccents_'.$this->getDatatable_id().'( data ) :
                    data;
        };

        searchType_'.$this->getDatatable_id().'.html = function ( data ) {
            return ! data ?
                "" :
                typeof data === "string" ?
                    removeAccents_'.$this->getDatatable_id().'( data.replace( /<.*?>/g, "" ) ) :
                    data;
        };                
';
        }
        
    $html.='      
    
         '.$this->getDatatable_javascript_var().' = $("#'.$this->getDatatable_id().'").DataTable(
        {
            "lengthMenu": [[2, 5, 10, 25, 50, -1], [2, 5, 10, 25, 50, "'.$this->getAll_result().'"]],
            "paging": '.$this->getPagingString().',
            "pagingType": "'.$this->getType_pagination().'",
            '.$this->makeJSForLanguagesOptions().'    
            "scrollX": '.convertBoolToString($this->getScrollX()).',
            "displayStart": '.$this->getStartPage().',
            '.$special_options.'
            '.$this->makeColumnDefinition().'
            '.$order_col.'
        }
    );

    //'.$this->getDatatable_javascript_var().'.columns.adjust();
    '.$this->getDatatable_javascript_var().'.page.len('.$this->getDefaultNumberOfResults().').draw();
   
';
        if($this->getButton_bar())
        {
            $html.=$this->makeJSForButtonBar();
        }
        
        if($this->getJumpTo())
        {
            $html.='  
        '.$this->getDatatable_javascript_var().'.page.jumpToData( "'.$this->getJumpValue().'",'.$this->getJumpColNumber().');
';
        }
        
        $html.=$this->printSelectRowFunction();
        
        $html.=' 
}); 
</script>
';
        return $html;
    }
    #[\Override]
    public static function testMe(): ?static
    {
        //string $datatable_id,array $arrayColumns,array $arrayLines
        $class=new static("datatable_id",["Column1","Column2"],[["a","b"],["c","d"]]);
        $class->addStyleCode("background-color:#FFFFFF;");
        return $class;
    }     
}