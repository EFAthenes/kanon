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
 * Description of JoditEditorComponent
 *
 * @author Mulot Louis
 */
class JoditEditorComponent extends KComponent
{
    private string $label=""; 
    private ?string $valueTextArea=null;
    private bool $readOnly=false;
    private ?int $colLabel=null;
    private ?int $colInput=null;
    private bool $disabled=false;
    private string $separator_label=""; 
    private string $toolbar="";

    /**
     * 
     * @param string $name
     * @param string|null $valueTextArea
     * @param string|null $label
     * @param bool $readOnly
     * @param int|null $colLabel
     * @param int|null $colInput
     * @param array<int,string>|null $class_names
     */
    final function __construct(string $name,?string $valueTextArea=null,?string $label=null,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [])
    {    
        parent::__construct();
        $this->setName(str_replace(" ", "_",$name ));
        $this->label=$label ?? "";
        $this->valueTextArea=$valueTextArea;
        $this->readOnly=$readOnly;    
        $this->setClass_names($class_names);
        $this->setColLabelAndInput($colLabel, $colInput);
        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/jodit.min.js");
        $layout->addCssFileToBuffer(__DIR__."/js/jodit.min.css");
        //$this->toolbar="source,|,bold,strikethrough,underline,italic,|";//,superscript,subscript,|,ul,ol,fontsize,table,link,hr,|,undo,redo,cut,eraser,copyformat,|,symbol,fullsize,selectall";
        //$this->toolbar="'source','|','bold','strikethrough','underline','italic','|','superscript','subscript','|','ul','ol','|','outdent','indent','|','font','fontsize','brush','paragraph','|','image','file','video','table','link','|','align','undo','redo','\n','cut','hr','eraser','copyformat','|','symbol','fullsize','selectall','print','about'";
        //$this->toolbar=",,,,,,,|,|,,|,|";
        $this->toolbar="'source','|','bold', 'italic', 'strikethrough','underline','ul','ol','|','outdent','indent','|','table','link','|','align','undo','redo','cut','hr','eraser','copyformat','|','symbol','fullsize','selectall'";
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getSeparator_label(): string
    {
        return $this->separator_label;
    }    

    function setColLabelAndInput( ?int $colLabel, ?int $colInput): JoditEditorComponent
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
    
    function setColLabel(int $colLabel) : JoditEditorComponent
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput) : JoditEditorComponent
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label) : JoditEditorComponent
    {
        $this->label = $label;
        return $this;
    }
    
    function setSeparator_label(string $separator_label) : JoditEditorComponent
    {
        $this->separator_label = $separator_label;
        return $this;
    }
    
    function setDisabled(bool $disabled) : JoditEditorComponent
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

    public function setValueTextArea(?string $valueTextArea) : JoditEditorComponent
    {
        $this->valueTextArea = $valueTextArea;
        return $this;
    }

    public function setReadOnly(bool $readOnly) : JoditEditorComponent
    {
        $this->readOnly = $readOnly;
        return $this;
    }
    
    public function getToolbar() : string
    {
        return $this->toolbar;
    }

    public function setToolbar(string $toolbar) : JoditEditorComponent
    {
        $this->toolbar = $toolbar;
        return $this;
    }
    
    
    public function draw() : string
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
        
        $value="";
        
        if(!is_null($this->valueTextArea))
        {
            $value=$this->valueTextArea;
        }
        
        $options='';
        if($this->readOnly)
        {
            $options.=' "readonly": true, ';
        }
        if($this->disabled)
        {
            $options.=' "disabled": true, ';
        }        
        
        $html.='
<textarea id="'.$this->getName().'" name="'.$this->getName().$this->getClassName().'" >'. FormComponent::textareaString($value).'</textarea>
<script>  
    var editor_'.$this->getName().' = new Jodit("#'.$this->getName().'", 
    {
        buttons : ['.$this->toolbar.'],
        buttonsSM : ['.$this->toolbar.'],
        buttonsMD: ['.$this->toolbar.'],
        buttonsXS: ['.$this->toolbar.'],            
        "language": "'.$this->getLanguageString().'",
        "enter" : "br",
        '.$options.'
        "enterBlock": "br"
    });
    function update_editor_'.$this->getName().'(value)
    {
        editor_'.$this->getName().'.value=value;   
    }  
    function getValue_'.$this->getName().'()
    {
        return editor_'.$this->getName().'.value;   
    }      
</script>
';
        $html .= '</div></div>';
        return $html;
    }
    
    private function getLanguageString() : string
    {
        $lg=LanguageManager::getInstance()->getLanguage();
        if(!empty($lg))
        {
            return $lg;
        }
        return "fr";
    }
    
    public function updateByJsStringFunctionName() : string
    {
        return 'update_editor_'.$this->getName().''; 
    }
    
    public function getValueByJsStringFunctionName() : string
    {
        return 'getValue_'.$this->getName().''; 
    }   
    

    #[\Override]
    public static function testMe(): static
    {
        //string $name,?string $valueTextArea=null,?string $label=null,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = []
        $content='
 <h3 class="mb-3 fw-semibold lh-1">κανὼν τῆς ἀληθείας</h3>
            <h1 class="mb-3 fw-semibold lh-1">
            Kanon : A clear standard for building, learning, and documenting code.
            </h1>
            <p class="lead mb-4">
                <b>Kanon</b> is a simple, robust PHP framework developed at the French School at Athens. Its name, inspired by Ancient Greek, reflects both structure and measure, while its development history reflects a collective effort involving trainees, students, and the IT service.
            </p>

            <h3 class="mb-3 fw-semibold lh-1">
            Open Source technologies and internal development
            </h3>
            <p class="lead mb-4">
            All client-side and server-side technologies used by Kanon are based on Open Source components. User interactions, data queries, interface design, data security, backups and project-specific features rely on internal development carried out by the IT service of the École française d’Athènes.

            This approach allows Kanon to combine the stability and transparency of widely used Open Source tools with the flexibility of a framework designed for the specific needs of the institution. The result is a simple, robust and maintainable technical foundation for building applications, documentation and data-oriented platforms.
            </p>            
';
        $class=new static("TheJoditEditorComponent",$content,"The Label:");
        return $class;
    }
}