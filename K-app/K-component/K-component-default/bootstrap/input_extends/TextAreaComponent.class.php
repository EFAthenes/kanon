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
class TextAreaComponent extends InputStringComponent
{
    private ?int $cols=null;
    private ?int $row=null;
    private ?int $maxLength=null;
    private ?int $minLength=null;
    private bool $autoSize=true;
    
    function __construct(?string $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [])
    {
        parent::__construct($inputValue,$inputName,$label,$placeholder,$require,$readOnly,$colLabel,$colInput,$class_names);
    }
    
    public function getCols() : ?int
    {
        return $this->cols;
    }

    public function getRow(): ?int
    {
        return $this->row;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function getAutoSize() : bool
    {
        return $this->autoSize;
    }

    public function setCols(?int $cols) : TextAreaComponent
    {
        $this->cols = $cols;
        return $this;
    }

    public function setRow(?int $row) : TextAreaComponent
    {
        if(!is_null($row))
        {
            $this->setAutoSize(false);
            $this->row = $row;
        }
        return $this;
    }
    
    public function calculateRowWithContent() : TextAreaComponent
    {
        $count=substr_count($this->getInputValue(),"\n");
        if($count>2)
        {
            $this->row = $count;
        }
        else
        {
            $this->row = 1;
        }
        $this->autoSize=false;
        return $this;
    }

    public function setMaxLength(?int $maxLength) : TextAreaComponent
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function setMinLength(?int $minLength) : TextAreaComponent
    {
        $this->minLength = $minLength;
        return $this;
    }

    public function setAutoSize(bool $autoSize) : TextAreaComponent
    {
        $this->autoSize = $autoSize;
        return $this;
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
        $html .= '<textarea class="form-control'.$this->getClassName().'"';

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

        $html .= ' name="' .$this->getInputName() . '" id="' . $this->getInputName() . '" ';
        
        if(!$this->autoSize && !is_null($this->row))
        {
            $html .= ' rows="' .$this->row . '" ';
        }
        if(!is_null($this->cols))
        {
            $html .= ' cols="' .$this->cols . '" ';
        }        
        if(!is_null($this->maxLength))
        {
            $html .= ' maxlength="' .$this->maxLength . '" ';
        } 
        if(!is_null($this->minLength))
        {
            $html .= ' minlength="' .$this->minLength . '" ';
        }         

        $inputValue = "";
        if (!is_null($this->inputValue))
        {
            $inputValue = $this->inputValue;
        }
        $html .= '>' . FormComponent::textareaString($inputValue) . '</textarea>';

        $html .= '</div></div>';
        
        if($this->autoSize)
        {
            $html.=" 
<script>
$('#".$this->getInputName()."').each(function () 
{
    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
}).on('input', function () 
{
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
</script>
";
        }
        
        
        return $html;
    }    
}
