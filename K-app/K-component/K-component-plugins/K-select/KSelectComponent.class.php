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
 * Description of KSelectComponent
 *
 * @author Louis Mulot
 */
class KSelectComponent extends KComponent
{
    private bool $multiple=false;
    /**
     * 
     * @var array<mixed,mixed>
     */
    private array $array=array();
    /**
     * 
     * @var array<mixed,mixed>|null
     */
    private ?array $selected=null;
    private ?string $label=null;
    private bool $readOnly=false;
    private ?int $colLabel=null;
    private ?int $colInput=null;
    private bool $disabled=false;
    private bool $simple=false;
    private string $separator_label="";
    private bool $allowNoValue=false;
    private string $placeholder="";
    private bool $first_selected=false;
    private bool $activate_form_group_class=true;
    private string $js_ajax_init="";
    private string $options_select2="";
    private string $js_on_change="";
    private string $js_on_empty="";
    
    //private bool $b5=false;
    
    /**
     * 
     * @param string $name
     * @param array<mixed,mixed> $array
     * @param mixed $selected
     * @param bool $multiple
     * @param string|null $label
     * @param bool $readOnly
     * @param int|null $colLabel
     * @param int|null $colInput
     */

    function __construct(string $name,array $array,mixed $selected=null,bool $multiple=false,?string $label=null,bool $readOnly=false,?int $colLabel=1,?int $colInput=11)
    {
        parent::__construct();
        $this->setNone();
        $this->setName($name);
        $this->multiple=$multiple;
        $this->array=$array;

        if(is_array($selected))
        {
            $this->selected=$selected;
        }
        else if(is_string($selected)||is_int($selected))
        {
            $this->selected=[$selected];
        }
        else
        {
            $this->selected=[];
        }

        $this->label=$label;
        $this->readOnly=$readOnly;
        $this->setColLabelAndInput($colLabel,$colInput);
        $layout=KApp::getInstance()->getLayout();
        $layout->addCSSFileToBuffer(__DIR__."/css/select2.min.css");
//        if($this->b5)
//        {
//           $layout->addCSSFileToBuffer(__DIR__."/css/select2.b5.css");
//        }
        $layout->addJsFileToBuffer(__DIR__."/js/select2.min.js");
    }

    function setColLabelAndInput(?int $colLabel,?int $colInput): KSelectComponent
    {
        if(!is_null($colLabel)&&!is_null($colInput))
        {
            if($colLabel>0&&$colLabel<=12)
            {
                $this->colLabel=$colLabel;
            }
            if($colInput>0&&$colInput<=12)
            {
                $this->colInput=$colInput;
            }
        }
        else if(!is_null($colLabel))
        {
            if($colLabel>0&&$colLabel<12)
            {
                $this->colLabel=$colLabel;
                $this->colInput=12-$colLabel;
            }
        }
        else if(!is_null($colInput))
        {
            if($colInput>0&&$colInput<12)
            {
                $this->colInput=$colInput;
                $this->colLabel=12-$colInput;
            }
        }
        return $this;
    }
    
    public function getReadOnly(): bool
    {
        return $this->readOnly;
    }

    function setColLabel(int $colLabel): KSelectComponent
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput): KSelectComponent
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label): KSelectComponent
    {
        $this->label=$label;
        return $this;
    }

    function setSeparator_label(string $separator_label): KSelectComponent
    {
        $this->separator_label=$separator_label;
        return $this;
    }

    function setDisabled(bool $disabled): KSelectComponent
    {
        $this->disabled=$disabled;
        return $this;
    }

    function setSimple(bool $simple): KSelectComponent
    {
        $this->simple=$simple;
        return $this;
    }

    function setAllowNoValue(bool $allowNoValue): KSelectComponent
    {
        $this->allowNoValue=$allowNoValue;
        return $this;
    }

    function setPlaceholder(string $placeholer): KSelectComponent
    {
        $this->placeholder=$placeholer;
        return $this;
    }

    function setFirstSelected(bool $first_selected): KSelectComponent
    {
        $this->first_selected=$first_selected;
        return $this;
    }

    public function getActivate_form_group_class(): bool
    {
        return $this->activate_form_group_class;
    }

    public function setActivate_form_group_class(bool $activate_form_group_class): KSelectComponent
    {
        $this->activate_form_group_class=$activate_form_group_class;
        return $this;
    }

    public function addJsOnChange(string $js_on_change): void
    {
        $this->js_on_change=$js_on_change;
    }
    public function addJsOnEmpty(string $js_on_empty): void
    {
        $this->js_on_empty=$js_on_empty;
    }    
     
    public function addValue(mixed $value) : void
    {
        $this->array[]=$value;
    }
    public function addValueAtFirst(mixed $value) : void
    {
        array_unshift($this->array,$value);
    }    

    public function draw(): string
    {
        $html='';
        $html2='';
        $found_one_selected=false;
        $already_first_selected=false;
        if(count($this->array)>0)
        {
            reset($this->array);
            $first_key=key($this->array);
//            $first_key =array_key_first($this->array);
//            var_dump($first_key);
//            KDebugger::getInstance()->dump($first_key,"kselect");
//            exit();
            if(is_array($this->array[$first_key]))
            {
                foreach($this->array as $array)
                {
                    if(count($array)>=2)
                    {
                        $selected="";
                        $first_item=(array_slice($array,0,1))[0];
                        $second_item=(array_slice($array,1,1))[0];
                        if($this->first_selected)
                        {
                            if(!$already_first_selected)
                            {
                                $found_one_selected=true;
                                $selected=' selected="selected" ';
                                $already_first_selected=true;
                            }
                        }
                        else if(in_array($first_item,$this->selected))
                        //if($array[0]==$this->selected)
                        {
                            $found_one_selected=true;
                            $selected=' selected="selected" ';
                        }
                        $html2.='<option '.$selected.' value="'.FormComponent::inputString($first_item).'">'.FormComponent::inputString($second_item).'</option>';
                    }
                    else
                    {
                        $selected="";
                        if($this->first_selected)
                        {
                            if(!$already_first_selected)
                            {
                                $found_one_selected=true;
                                $selected=' selected="selected" ';
                                $already_first_selected=true;
                            }
                        }
                        else if(in_array($array[0],$this->selected))
                        //if($array[0]==$this->selected)
                        {
                            $found_one_selected=true;
                            $selected=' selected="selected" ';
                        }
                        $html2.='<option '.$selected.' value="'.FormComponent::inputString($array[0]).'">'.FormComponent::inputString($array[0]).'</option>';
                    }
                }
            }
            else
            {
                foreach($this->array as $field)
                {
                    $selected="";
                    //if($field==$this->selected)
                    if($this->first_selected)
                    {
                        if(!$already_first_selected)
                        {
                            $found_one_selected=true;
                            $selected=' selected="selected" ';
                            $already_first_selected=true;
                        }
                    }
                    else if(!is_null($this->selected)&&in_array($field,$this->selected))
                    {
                        $selected=' selected="selected" ';
                    }
                    $html2.='<option '.$selected.' value="'.FormComponent::inputString($field).'">'.FormComponent::inputString($field).'</option>';
                }
            }
        }

        $class_form_group="";
        if($this->activate_form_group_class)
        {
            $class_form_group="form-group";
        }

        if($this->label!=null)
        { //for="'.$this->label.'"
            $html.='<div class="'.$class_form_group.' row"><label  class="col-'.$this->colLabel.' label_form">'.$this->label.$this->separator_label.'</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html.='<div class="'.$class_form_group.' row"><div class="col-12">';
        }

        $disabled=' ';
        if($this->disabled)
        {
            $disabled=' disabled="disabled" ';
        }

        if($this->multiple)
        {
            $html.='
<input type="hidden" name="'.$this->getName().'" value="" />                
<select name="'.$this->getName().'[]" id="'.$this->getIdName().'" multiple="multiple" class="col-lg" '.$disabled.' '.$this->getStringEvents().' >
    '.$html2.'
</select>
';
        }
        else
        {
            if(!$this->simple)
            {
                $html.='
<input type="hidden" name="'.$this->getName().'" value="" /> ';
            }
            $html.='
<select name="'.$this->getName().'" id="'.$this->getIdName().'" class="form-control" '.$disabled.' '.$this->getStringEvents().'  >
    '.$html2.'
</select>
';
        }
        $html.='
</div>'.parent::draw().'</div>
';
        if(!$this->simple)
        {
            $b5_string='dropdownParent: $("#'.$this->getIdName().'").parent(),';
//            if($this->b5)
//            {
//               $b5_string='theme: "bootstrap-5", dropdownParent: $("#'.$this->getIdName().'").parent(),';
//            }
            
            //{ theme: 'bootstrap-5'}
            $this->options_select2='{'.$b5_string.'}';
            if($this->allowNoValue)
            {
                $this->options_select2='{ '.$b5_string.' allowClear: true,placeholder: \''.$this->placeholder.'\'}';
            }
            else if($this->placeholder!="")
            {
                $this->options_select2='{ '.$b5_string.' placeholder: \''.$this->placeholder.'\'}';
            }
            
            
            $html.='
<script>
$(document).ready(function() 
{
    $("#'.$this->getIdName().'").select2('.$this->options_select2.' );
';
            if($this->allowNoValue&&!$this->multiple&&!$found_one_selected)
            {
                $html.='
    $("#'.$this->getIdName().'").val("").trigger("change");   
';
            }

            if($this->js_on_change!="")
            {
                $html.='          
    $("#'.$this->getIdName().'").on("select2:select", function (e) {
        '.$this->js_on_change.'
    });
  ';
            }
            if($this->js_on_empty!="")
            {
                $html.='          
    $("#'.$this->getIdName().'").on("select2:unselect", function (e) {
        '.$this->js_on_empty.'
    });
  ';
            }            
            $html.='    
});
</script>
';
        }
        return $html;
    }
    
    public function makeJSForEmptyState() : string
    {
        //KDebugger::getInstance()->dump($this->selected);
        $js='';
        if($this->multiple)
        {
            $js='
    $("#'.$this->getIdName().'").val(null).trigger("change");
';
        }
        else if($this->allowNoValue)
        {
            $js='
    $("#'.$this->getIdName().'").val(null).trigger("change");
';
        }
        else
        {
            $first_value = reset($this->array);
            if(is_array($first_value))
            {
                $js='
    $("#'.$this->getIdName().'").val("'.$first_value[0].'").trigger("change");
';
            }
            else
            {
                $js='
    $("#'.$this->getIdName().'").val("'.$first_value.'").trigger("change");
';                
            }
        }        
        return $js;
    }
    
//    public function makeJSForResetSelected() : string
//    {
//        $js='
//    $("#'.$this->getIdName().'").val("'.$this->selected.'").trigger("change");
//';
//        return $js;
//    }    

    public function makeJSForInitialize(string $url,bool $onload=false) : string
    {
        $this->js_ajax_init='initalizeKselectComponent_'.$this->getIdName();

        $js='
function '.$this->js_ajax_init.'(the_url,callback=null)
{
     $.ajax({
        url: the_url,
        type: "POST",
        success: function (data) 
        {
            if ($("#'.$this->getIdName().'").hasClass("select2-hidden-accessible")) 
            {
                $("#'.$this->getIdName().'").select2("destroy");
                $("#'.$this->getIdName().'").empty();
            }        
            //console.log(data);
            $("#'.$this->getIdName().'").select2({
                data: data
            });
        }
    }).done(function() 
    {
        if(callback!=null && typeof callback == "function") 
        {
            callback();
        }
    });
}
';
        $this->addJSText($js);
        if($onload)
        {
            $this->setSimple(true);
            $this->addJSTextOnDocumentReady(''.$this->js_ajax_init.'("'.$url.'");');
        }
        return $this->js_ajax_init;
    }

}