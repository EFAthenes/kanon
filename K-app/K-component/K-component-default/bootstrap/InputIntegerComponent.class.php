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
class InputIntegerComponent extends KComponent
{
    protected ?int $colLabel=2;
    protected ?int $colInput=11;
    protected ?string $label="";
    protected string $placeholder="";
    protected mixed $inputValue=0;
    protected string $inputName="";
    protected bool $require=false;
    protected bool $readOnly=false;
    protected string $separator_label="";
    protected ?int $min_value=null;
    protected ?int $max_value=null;
    protected ?int $step=null;
       
    /**
     * 
     * @param mixed $inputValue
     * @param string $inputName
     * @param string|null $label
     * @param string|null $placeholder
     * @param bool $require
     * @param bool $readOnly
     * @param int|null $colLabel
     * @param int|null $colInput
     * @param array<int,string>|null $class_names
     */
    function __construct(mixed $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [])
    {
        parent::__construct();
        $this->setNone();
        $this->inputValue=$inputValue;
        $this->inputName=$inputName;
        $this->label=$label;
        if(!is_null($placeholder))
        {
            $this->placeholder=$placeholder;
        }
        $this->require=$require;
        $this->readOnly=$readOnly;
        $this->setClass_names($class_names);
        $this->setColLabelAndInput($colLabel, $colInput);
    }
    
    function setColLabelAndInput( ?int $colLabel, ?int $colInput): self
    {
        if(!is_null($colLabel)&&!is_null($colInput))
        {
            if($colLabel>0 && $colLabel<=12)
            {
                $this->colLabel=$colLabel;
            }
            if($colInput>0 && $colInput<=12)
            {
                $this->colInput=$colInput;
            }            
        }
        else if(!is_null($colLabel))
        {
            if($colLabel>0 && $colLabel<12)
            {
                $this->colLabel=$colLabel;
                $this->colInput=12-$colLabel;
            }
        }
        else if(!is_null($colInput))
        {
            if($colInput>0 && $colInput<12)
            {
                $this->colInput=$colInput;
                $this->colLabel=12-$colInput;
            }            
        }
        return $this;
    }
    
    function setColLabel(int $colLabel) : self
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput) : self
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label) : self
    {
        $this->label = $label;
        return $this;
    }

    function setInputValue(mixed $inputValue) : self
    {
        if(is_null($inputValue))
        {
            $this->inputValue =null;
        }
        else
        {
            $this->inputValue = intval($inputValue);
        }
        return $this;
    }

    function setInputName(string $inputName) : self
    {
        $this->inputName = $inputName;
        return $this;
    }

    function setRequire(bool $require) : self
    {
        $this->require = $require;
        return $this;
    }

    function setReadOnly(bool $readOnly) : self
    {
        $this->readOnly = $readOnly;
        return $this;
    }
    
    function setPlaceholder(string $placeholder) : self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    function setSeparator_label(string $separator_label) : self
    {
        $this->separator_label = $separator_label;
        return $this;
    }
    
    function setMin_value(int $min_value): self
    {
        if(is_null($this->max_value))
        {
            $this->min_value = $min_value;
        }
        else if($min_value<$this->max_value)
        {
            $this->min_value = $min_value;
        }
        return $this;
    }

    function setMax_value(int $max_value): self
    {       
        if(is_null($this->min_value))
        {
            $this->max_value = $max_value;
        }
        else if($this->min_value<$max_value)
        {
            $this->max_value = $max_value;
        } 
        return $this;
    }
    
    function setStepDouble(float $step) : void
    {
        $this->step=intval($step);
    }

    function setStepInteger(int $step) : void
    {
        $this->step=$step;
    }    
    
    
    
    public function draw(): string
    {
        $html = '<div class="form-group row">';
        if ($this->label != null)
        {
            $html .= '<label for="' . $this->label . '" class="col-'.$this->colLabel.' label_form">' . $this->label.$this->separator_label . '</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html .= '<div class="col-12">';
        }
        $html .= '<input type="number" class="form-control'.$this->getClassName().'"';

        if ($this->require)
        {
            $html .= ' required="required" ';
        }
        if ($this->readOnly)
        {
            $html .= ' readonly="readonly" ';
        } 
        if(!empty($this->placeholder))
        {
            $html .= ' placeholder="'.FormComponent::inputString($this->placeholder).'" ';
        }
        if(!is_null($this->min_value))
        {
            $html .=' min="'.$this->min_value.'" ';
        }
        if(!is_null($this->max_value))
        {
            $html .=' max="'.$this->max_value.'" ';
        }
        if(!is_null($this->step))
        {
            $html .=' step="'.$this->step.'" ';
        }        
        
        $html .= ' name="' .$this->inputName . '" id="' . $this->inputName . '" ';

        $inputValue = "";
        if (!is_null($this->inputValue))
        {
            $inputValue = $this->inputValue;
        }
        $html .= ' value="' . FormComponent::inputString("".$inputValue) . '" />';

        $html .= '</div>'.parent::draw().'</div>';
        return $html;
    }
}