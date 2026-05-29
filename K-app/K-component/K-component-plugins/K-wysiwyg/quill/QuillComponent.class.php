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
 * Description of QuillComponent
 *
 * @author Mulot Louis
 */
class QuillComponent extends KComponent
{
    private string $label=""; 
    private ?string $valueTextArea=null;
    private bool $readOnly=false;
    private ?int $colLabel=null;
    private ?int $colInput=null;
    private bool $disabled=false;
    private string $separator_label="";
    
    function __construct(string $name,?string $valueTextArea=null,?string $label=null,bool $readOnly=false,?int $colLabel=1,?int $colInput=11)
    {    
        parent::__construct();
        $this->setName($name);
        $this->label=$label;
        $this->valueTextArea=$valueTextArea;
        $this->readOnly=$readOnly;        
        $this->setColLabelAndInput($colLabel, $colInput);
        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/quill.min.js");
        $layout->addCSSFileToBuffer(__DIR__."/js/quill.snow.css");
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getSeparator_label(): string
    {
        return $this->separator_label;
    }

        
    function setColLabelAndInput( ?int $colLabel, ?int $colInput): QuillComponent
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
    
    function setColLabel(int $colLabel) : QuillComponent
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput) : QuillComponent
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label) : QuillComponent
    {
        $this->label = $label;
        return $this;
    }
    
    function setSeparator_label(string $separator_label) : QuillComponent
    {
        $this->separator_label = $separator_label;
        return $this;
    }
    
    function setDisabled(bool $disabled) : QuillComponent
    {
        $this->disabled= $disabled;
        return $this;
    }  
    
    public function getValueTextArea() : ?string
    {
        return $this->valueTextArea;
    }

    public function getReadOnly() : bool
    {
        return $this->readOnly;
    }

    public function setValueTextArea(?string $valueTextArea) : QuillComponent
    {
        $this->valueTextArea = $valueTextArea;
        return $this;
    }

    public function setReadOnly(bool $readOnly) : QuillComponent
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    
    
    
    public function draw() : string
    {
        $value="";
        
        if(!is_null($this->valueTextArea))
        {
            $value=$this->valueTextArea;
        }
        
        $options='';
        if($this->readOnly)
        {
            $options.=' readonly="readonly" ';
        }
        if($this->disabled)
        {
            $options.=' disabled="disabled" ';
        }        
        // <textarea id="'.$this->getName().'" name="'.$this->getName().'" '.$options.'>'.FormComponent::textareaString($value).'</textarea>
        $html='
<div id="'.$this->getName().'_toolbar">
<span class="ql-formats">
      <select class="ql-font"></select>
      <select class="ql-size"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-bold"></button>
      <button class="ql-italic"></button>
      <button class="ql-underline"></button>
      <button class="ql-strike"></button>
    </span>
    <span class="ql-formats">
      <select class="ql-color"></select>
      <select class="ql-background"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-script" value="sub"></button>
      <button class="ql-script" value="super"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-header" value="1"></button>
      <button class="ql-header" value="2"></button>
      <button class="ql-blockquote"></button>
      <button class="ql-code-block"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-list" value="ordered"></button>
      <button class="ql-list" value="bullet"></button>
      <button class="ql-indent" value="-1"></button>
      <button class="ql-indent" value="+1"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-direction" value="rtl"></button>
      <select class="ql-align"></select>
    </span>
    <span class="ql-formats">
      <button class="ql-link"></button>
      <button class="ql-image"></button>
      <button class="ql-video"></button>
      <button class="ql-formula"></button>
    </span>
    <span class="ql-formats">
      <button class="ql-clean"></button>
    </span>
</div>
<div id="'.$this->getName().'">'.$value.'</div>
<script>	
 var quill = new Quill("#'.$this->getName().'", {
    modules: {
      //formula: true,
     // syntax: true,
      toolbar: "#'.$this->getName().'_toolbar"
    },
    placeholder: "Compose an epic...",
    theme: "snow"
  });
</script>
';
        return $html;
    }
}