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
 * Description of CkEditor4Component
 *
 * @author louis.mulot
 */
class CkEditor4Component extends KComponent
{
    private string $additionnal_options="";
    private string $preadditionnal_options="";
    private ?string $label=""; 
    private ?string $valueTextArea=null;
    private bool $readOnly=false;
    private ?int $colLabel=null;
    private ?int $colInput=null;
    private bool $disabled=false;
    private string $separator_label=":";    
    
    function __construct(string $name,?string $valueTextArea=null,?string $label=null,bool $readOnly=false,?int $colLabel=1,?int $colInput=11)
    {    
        parent::__construct();
        $this->setName($name);
        $this->label=$label;
        $this->valueTextArea=$valueTextArea;
        $this->readOnly=$readOnly;        
        $this->setColLabelAndInput($colLabel, $colInput);
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getSeparator_label(): string
    {
        return $this->separator_label;
    }    
    /**
     * 
     * @param array<int,string> $paths
     * @return void
     */
    public function setRequiredPath(array $paths) : void
    {
        $layout = KApp::getInstance()->getLayout();
        foreach ($paths as $path)
        {
            if(stringEndsWith($path, ".css"))
            {
                $layout->addCSSFile($path);
            }
            elseif(stringEndsWith($path, ".js"))
            {
                $layout->addJsFile($path);
            }            
        }
    }

    function setColLabelAndInput( ?int $colLabel, ?int $colInput): CkEditor4Component
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
    
    function setColLabel(int $colLabel) : CkEditor4Component
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput) : CkEditor4Component
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label) : CkEditor4Component
    {
        $this->label = $label;
        return $this;
    }
    
    function setSeparator_label(string $separator_label) : CkEditor4Component
    {
        $this->separator_label = $separator_label;
        return $this;
    }
    
    function setDisabled(bool $disabled) : CkEditor4Component
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

    public function setValueTextArea(?string $valueTextArea) : CkEditor4Component
    {
        $this->valueTextArea = $valueTextArea;
        return $this;
    }

    public function setReadOnly(bool $readOnly) : CkEditor4Component
    {
        $this->readOnly = $readOnly;
        return $this;
    }
    
    public function setAdditionnalOptions(string $additionnal_options): CkEditor4Component
    {
        $this->additionnal_options=$additionnal_options;
        return $this;
    }
    
    public function setPreAdditionnalOptions(string $preadditionnal_options): CkEditor4Component
    {
        $this->preadditionnal_options=$preadditionnal_options;
        return $this;
    }
    
    
    public function draw(): string
    {
        $value="";
        
        if(!is_null($this->valueTextArea))
        {
            $value=$this->valueTextArea;
        }
        
        $html='';
        $class_form_group="form-group";
        if ($this->label != null)
        {
            $html .= '<div class="'.$class_form_group.' row"><label for="' . $this->label . '" class="col-'.$this->colLabel.' label_form">' . $this->label.$this->separator_label . '</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html .= '<div class="'.$class_form_group.' row"><div class="col-12">';
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
        
        $html.='
            <textarea class="form-control" id="'.$this->getName().'" name="'.$this->getName().'" '.$options.'  rows="3">
                '.FormComponent::textareaString($value).
            '</textarea>
<script>
$( document ).ready(function() 
{
    CKEDITOR.replace( "'.$this->getName().'",
    {
        '.$this->preadditionnal_options.'
        toolbar :
        [
            { name: "document", items : ["Source"] },
            { name: "links", items : [ "Link","Unlink"] },
            { name: "clipboard", items : [ "Cut","Copy","Paste","PasteText","PasteFromWord","-","Undo","Redo" ] },
            { name: "basicstyles", items : [ "Bold","Italic","Subscript","Superscript" ] },
            { name: "styles", items : [ "Format","FontSize" ] },
            { name: "xronika", items : ["Xronika-Authors","RemoveFormat" ] }
        ],
        language : "'.LanguageManager::getInstance()->getLanguage().'",
        '.$this->additionnal_options.'
    });
}); 
</script>';
        
        $html.='
</div></div>
';

        return $html;
    }
}