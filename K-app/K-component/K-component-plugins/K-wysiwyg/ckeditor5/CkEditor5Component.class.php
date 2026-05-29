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
 * Description of CkEditor5Component
 *
 * @author Mulot Louis
 */
class CkEditor5Component extends KComponent
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
        $layout->addJsFileToBuffer(__DIR__."/js/ckeditor.js");
        $layout->addJsFileToBuffer(__DIR__."/js/translations/fr.js");
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getSeparator_label(): string
    {
        return $this->separator_label;
    }

    function setColLabelAndInput( ?int $colLabel, ?int $colInput): CkEditor5Component
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
    
    function setColLabel(int $colLabel) : CkEditor5Component
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput) : CkEditor5Component
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label) : CkEditor5Component
    {
        $this->label = $label;
        return $this;
    }
    
    function setSeparator_label(string $separator_label) : CkEditor5Component
    {
        $this->separator_label = $separator_label;
        return $this;
    }
    
    function setDisabled(bool $disabled) : CkEditor5Component
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

    public function setValueTextArea(?string $valueTextArea) : CkEditor5Component
    {
        $this->valueTextArea = $valueTextArea;
        return $this;
    }

    public function setReadOnly(bool $readOnly) : CkEditor5Component
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
    ClassicEditor.create( document.querySelector( "#'.$this->getName().'" ), 
    {
        /*plugins: [ RemoveFormat ],*/
        toolbar: [ "bold", "italic", "link","|","undo","redo" ,"removeFormat" ],
        language: "fr"
    })
    .then( editor => {
        window.editor = editor;
        console.log( Array.from( editor.ui.componentFactory.names() ) ); 
    })
    .catch( err => {
        console.error( err.stack );
    });
    
</script>
';
        return $html;
    }
}