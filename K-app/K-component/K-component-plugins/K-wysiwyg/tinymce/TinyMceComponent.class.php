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
 * Description of TinyMCEComponent
 *
 * @author Mulot Louis
 */
class TinyMCEComponent extends KComponent
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
        $layout->addJsFileToBuffer(__DIR__."/js/tinymce.min.js");
    }

    function setColLabelAndInput( ?int $colLabel, ?int $colInput): TinyMCEComponent
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
    
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getSeparator_label(): string
    {
        return $this->separator_label;
    }

        
    function setColLabel(int $colLabel) : TinyMCEComponent
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput) : TinyMCEComponent
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label) : TinyMCEComponent
    {
        $this->label = $label;
        return $this;
    }
    
    function setSeparator_label(string $separator_label) : TinyMCEComponent
    {
        $this->separator_label = $separator_label;
        return $this;
    }
    
    function setDisabled(bool $disabled) : TinyMCEComponent
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

    public function setValueTextArea(?string $valueTextArea) : TinyMCEComponent
    {
        $this->valueTextArea = $valueTextArea;
        return $this;
    }

    public function setReadOnly(bool $readOnly) : TinyMCEComponent
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
        
        $html='
<textarea id="'.$this->getName().'" name="'.$this->getName().'" '.$options.'>'.FormComponent::textareaString($value).'</textarea>
<script>	
tinymce.init({
  selector: "textarea#'.$this->getName().'",
  height: 500,
  menubar: false,
  plugins: [
    "advlist autolink lists link image charmap print preview anchor textcolor",
    "searchreplace visualblocks code fullscreen",
    "insertdatetime media table paste code help wordcount"
  ],
  toolbar: "undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help",
  content_css: [
    "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
    "//www.tiny.cloud/css/codepen.min.css"
  ]
});
</script>
';
        return $html;
    }
}