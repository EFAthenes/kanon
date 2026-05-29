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
class CheckBoxComponent extends InputStringComponent
{
    private static int $TYPE_NORMAL=1;
    private static int $TYPE_TOGGLE=2;
    private static int $TYPE_FLIP=3;
    
    
    private string $valueLabelChecked="true";
    private string $valueLabelUnChecked="false";    
    private string $valuePostedChecked="true";
    private string $valuePostedUnChecked="false";
    private bool $checked=false;
    private int $type_checkbox=1;
    private bool $flippingMediumSize=false;
    
    /**
     * 
     * @param bool|null $inputValue
     * @param string $inputName
     * @param string|null $label
     * @param bool $require
     * @param bool $readOnly
     * @param int|null $colLabel
     * @param int|null $colInput
     * @param array<int,string>|null $class_names
     */
    function __construct(?bool $inputValue,string $inputName,?string $label=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [])
    {
        if(!is_null($inputValue) && $inputValue)
        {
            $this->checked=true;
        }
        parent::__construct("",$inputName,"".$label,"",$require,$readOnly,$colLabel,$colInput,$class_names);
        $this->setTypeNormal();
    }
    
    function setInputValue(string $inputValue): \InputStringComponent
    {
        $value_bool=convertToBool($inputValue);
        if(!is_null($value_bool))
        {
            $this->checked=$value_bool;
        }
        return $this;
    }
    
    function setValueLabelChecked(string $valueLabelChecked) : CheckBoxComponent
    {
        $this->valueLabelChecked = $valueLabelChecked;
        return $this;
    }

    function setValueLabelUnChecked(mixed $valueLabelUnChecked) : CheckBoxComponent
    {
        $this->valueLabelUnChecked = "".$valueLabelUnChecked;
        return $this;
    }    
    
    function setValuePostedChecked(mixed $valuePostedChecked) : CheckBoxComponent
    {
        $this->valuePostedChecked = "".$valuePostedChecked;
        return $this;
    }

    function setValuePostedUnChecked(mixed $valuePostedUnChecked) : CheckBoxComponent
    {
        $this->valuePostedUnChecked = "".$valuePostedUnChecked;
        return $this;
    }

    function setChecked(bool $checked) : CheckBoxComponent
    {
        $this->checked = $checked;
        return $this;
    }
    
    function setTypeNormal() : CheckBoxComponent
    {
        $this->type_checkbox=self::$TYPE_NORMAL;
        return $this;
    }
    
    function setTypeToggle() : CheckBoxComponent
    {
        $this->type_checkbox=self::$TYPE_TOGGLE;
        $this->setValuePostedChecked(1);
        $this->setValuePostedUnChecked(0);        
        return $this;
    }
    
    function setTypeFlipping() : CheckBoxComponent
    {
        $this->type_checkbox=self::$TYPE_FLIP;
        return $this;
    }

    function setFlippingMediumSize() : CheckBoxComponent
    {
        $this->flippingMediumSize=true;
        return $this;
    }    
    
    
    public function draw(): string
    {
        $rand="_".KRandom::makeRandom();
        $html = '<div class="form-group row">';
        if ($this->label != null)
        {
            //for="' . $this->label . '" 
            $html .= '<label class="col-'.$this->colLabel.' label_form">' . $this->label.$this->separator_label . '</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html .= '<div class="col-12">';
        }
        if($this->type_checkbox==self::$TYPE_FLIP)
        {
            $html .= '<div class="toggle-flip"><label>';
        }
        else if($this->type_checkbox==self::$TYPE_TOGGLE)
        {
            $html .= '<div class="toggle"><label>';
        }
        
        $inputValue = $this->valuePostedUnChecked;
        $checked="";
        if($this->checked)
        {
            $checked .=' checked ';
            $inputValue = $this->valuePostedChecked;
        }
        
        
        $html.='<input type="hidden" value="'. FormComponent::inputString($inputValue).'"  name="' .$this->getName(). '" id="' . $this->getName(). '"  />';
        
        $html .= '<input type="checkbox" class="form-check-input'.$this->getClassName().'"';

        if ($this->require)
        {
            $html .= ' required="required" ';
        }
        if ($this->readOnly)
        {
            $html .= ' readonly="readonly" ';
        }
        if($this->disabled)
        {
            $html .= ' disabled="disabled" ';
        }        
        if(!empty($this->placeholder))
        {
            $html .= ' placeholder="'.FormComponent::inputString($this->placeholder).'" ';
        }
        

        $html .= ' name="' .$this->getName() .$rand. '" id="' . $this->getName() .$rand. '" '.$checked.' ';

        $html .=' />';
        
        if($this->type_checkbox==self::$TYPE_FLIP)
        {
            $mediumSize="";
            if($this->flippingMediumSize)
            {
                $mediumSize="flip-indecator-medium";
            }            
            $html .= '<span class="flip-indecator '.$mediumSize.'"  data-toggle-on="'.FormComponent::inputString($this->valueLabelChecked).'" data-toggle-off="'.FormComponent::inputString($this->valueLabelUnChecked).'"></span></label></div>';
        }
        else if($this->type_checkbox==self::$TYPE_TOGGLE)
        {
            $html .= '<span class="button-indecator" data-toggle-on="'.FormComponent::inputString($this->valueLabelChecked).'" data-toggle-off="'.FormComponent::inputString($this->valueLabelUnChecked).'"></span></label></div>';
        }

        $html .= '</div></div>
<script>
$("#'.$this->getInputName().$rand.'").change(
    function()
    {
        if($("#'.$this->getInputName().$rand.'").is(":checked"))
        {
            $("#'.$this->getInputName().'").val("'.FormComponent::inputString($this->valuePostedChecked).'");
        }
        else
        {
            $("#'.$this->getInputName().'").val("'.FormComponent::inputString($this->valuePostedUnChecked).'");
        }
    }
);
</script>
';

        return $html;
    }    
}