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
class InputStringComponent extends KComponent
{
    protected ?int $colLabel=2;
    protected ?int $colInput=11;
    protected ?string $label="";
    protected string $placeholder="";
    protected ?string $inputValue="";
    //protected $inputName="";
    protected string $inputType="text";
    protected bool $require=false;
    protected bool $readOnly=false;
    protected string $separator_label="";
    protected bool $disabled=false;
    protected bool $autocomplete=false;
    protected string$input_option = "";
    protected bool $justInput=false;
    
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
     * @param string $input_option
     */
    function __construct(mixed $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [], string $input_option="")
    {
        parent::__construct();
        $this->setNone();
        if(is_null($inputValue))
        {
            $this->inputValue=null;
        }
        else
        {
            $this->inputValue="".strval($inputValue);          
        }
        $this->setName($inputName);
        $this->label=$label;
        if(!is_null($placeholder))
        {
            $this->placeholder=$placeholder;
        }
        $this->require=$require;
        $this->readOnly=$readOnly;
        $this->setClass_names($class_names);
        $this->input_option = $input_option;

        $this->setColLabelAndInput($colLabel, $colInput);
    }
        
    public function setColLabelAndInput( ?int $colLabel, ?int $colInput): InputStringComponent
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
    
    public function setColLabel(int $colLabel) : InputStringComponent
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    public function setColInput(int $colInput) : InputStringComponent
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    public function setLabel(string $label) : InputStringComponent
    {
        $this->label = $label;
        return $this;
    }
    
    public function setJustInput() : static
    {
        $this->justInput=true;
        return $this;
    }
    
    public function getInputValue() :string
    {
        return $this->inputValue;
    }

    public function setInputValue(string $inputValue) : InputStringComponent
    {
        $this->inputValue = $inputValue;
        return $this;
    }

    public function setInputName(string $inputName) : InputStringComponent
    {
        $this->setName($inputName);
        return $this;
    }
    
    public function getInputName() : string
    {
        return $this->getName();
    }    

    public function setRequire(bool $require) : InputStringComponent
    {
        $this->require = $require;
        return $this;
    }

    public function setReadOnly(bool $readOnly) : InputStringComponent
    {
        $this->readOnly = $readOnly;
        return $this;
    }

   
    public function setPlaceholder(string $placeholder) : InputStringComponent
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setSeparator_label(string $separator_label) : InputStringComponent
    {
        $this->separator_label = $separator_label;
        return $this;
    }

    public function setDisabled(bool $disabled) : InputStringComponent
    {
        $this->disabled= $disabled;
        return $this;
    }    
    
    public function getAutocomplete() : bool
    {
        return $this->autocomplete;
    }

    public function setAutocomplete(bool $autocomplete): InputStringComponent
    {
        $this->autocomplete = $autocomplete;
        return $this;
    }

        
    public function setInputType(string $inputType): InputStringComponent
    {
        $this->inputType=$inputType;
        return $this;
    }
    
    public function setHidden(): InputStringComponent
    {
        $this->inputType="hidden";
        return $this;
    }
    
    
    public function draw(): string
    {
        $html="";
        if($this->justInput)
        {
            
        }
        else if($this->inputType!="hidden")
        {
            $html.= '<div class="form-group row">';
            if ($this->label != null)
            { //for="' . $this->label . '"
                $html .= '<label  class="col-'.$this->colLabel.' label_form">' . $this->label.$this->separator_label . '</label><div class="col-'.$this->colInput.'">';
            }
            else
            {
                $html .= '<div class="col-12 col-sm-12">';
            }
        }
        $html .= '<input type="'.$this->inputType.'" class="form-control'.$this->getClassName().'"';
        
        if(!empty($this->input_option))
        {
            $html .= ' '.$this->input_option.' ';
        }
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
        if($this->autocomplete)
        {
            $html .= ' autocomplete="on" ';
        }
        else
        {
            $html .= ' autocomplete="off" ';
        }
        if(!empty($this->placeholder))
        {
            $html .= ' placeholder="'.FormComponent::inputString($this->placeholder).'" ';
        }

        $html .= ' name="' .$this->getName() . '" id="' . $this->getName() . '" ';

        $inputValue = "";
        if (!is_null($this->inputValue))
        {
            $inputValue = $this->inputValue;
        }
        $html .= ' value="' . FormComponent::inputString($inputValue) . '" '.$this->getStringEvents().' />';

        if($this->justInput)
        {
            $html .=parent::draw();
        }
        else if($this->inputType!="hidden")
        {
            $html .=parent::draw().'</div></div>';
        }
        return $html;
    }
}
