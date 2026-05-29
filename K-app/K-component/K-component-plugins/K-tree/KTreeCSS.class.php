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
 * Description of KTreeCSS
 *
 * @author louis.mulot
 */
class KTreeCSS extends KComponent
{
    /**
     * 
     * @var array<int,KTreeJSNode|null>
     */
    private array $array=array();
    private ?string $label=null;
    private ?int $colLabel=null;
    private ?int $colInput=null;
    private string $separator_label="";
    private bool $purifyText=false;
    private bool $openAllOnInit=false;
    private bool $postValue=true;
//    private bool $openAllOnInitIfNoSelected=false;

    private bool $checkbox=false;
    // Boutons
//    private bool $enableButtons=false;
//    private bool $enableButtonsOpen=true;
//    private bool $enableButtonsClose=true;
//    private bool $enableButtonsCheck=true;
//    private bool $enableButtonsUncheck=true;
//    private bool $enableButtonsRestore=true;
    
//    private string $idNode="";
//    private string $idPanel="";    

  
    /**
     * 
     * @param string $name
     * @param array<int,KTreeJSNode|null> $array
     * @param bool $checkbox
     * @param string|null $label
     * @param int|null $colLabel
     * @param int|null $colInput
     */
    function __construct(string $name,array $array,bool $checkbox=false,?string $label=null,?int $colLabel=1,?int $colInput=11)
    {
        parent::__construct();
        $this->setNone();
        $this->setName("treecss_".$name);

        $this->setArrayNodes($array);
        
        $this->checkbox=$checkbox;

        $this->label=$label;
        $this->setColLabelAndInput($colLabel,$colInput);
        
        self::initIncludes();
    }
        
    public static function initIncludes() : void
    {
        $layout=KApp::getInstance()->getLayout();
        $layout->addCSSFileToBuffer(__DIR__."/css/tree.css");
    }
    
    public function getPostValue(): bool
    {
        return $this->postValue;
    }

    public function setPostValue(bool $postValue): void
    {
        $this->postValue = $postValue;
    }    
    
//    public function getOpenAllOnInit(): bool
//    {
//        return $this->openAllOnInit;
//    }
//
//    public function getOpenAllOnInitIfNoSelected(): bool
//    {
//        return $this->openAllOnInitIfNoSelected;
//    }
//
//    public function getEnableButtons(): bool
//    {
//        return $this->enableButtons;
//    }
//
//    public function getEnableButtonsOpen(): bool
//    {
//        return $this->enableButtonsOpen;
//    }
//
//    public function getEnableButtonsClose(): bool
//    {
//        return $this->enableButtonsClose;
//    }
//
//    public function getEnableButtonsCheck(): bool
//    {
//        return $this->enableButtonsCheck;
//    }
//
//    public function getEnableButtonsUncheck(): bool
//    {
//        return $this->enableButtonsUncheck;
//    }
    
    public function purifyText(bool $status) : void
    {
        $this->purifyText=$status;
    }

    
    /**
     * 
     * @param array<int,KTreeJSNode|null> $array
     * @return bool
     */
    public function setArrayNodes(?array $array=null): bool
    {
        if(is_array($array)&&count($array))
        {
            $new_array=[];
            foreach($array as $node)
            {
                /** @phpstan-ignore-next-line */
                if(!is_null($node)&&$node instanceof KTreeJSNode)
                {
                    $new_array[]=$node;
                }
                else
                {
                    return false;
                }
            }
            $this->array=$new_array;
            return true;
        }
        return false;
    }

    function setColLabelAndInput(?int $colLabel,?int $colInput): self
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

    function setColLabel(int $colLabel): self
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput): self
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label): self
    {
        $this->label=$label;
        return $this;
    }

    function setSeparator_label(string $separator_label): self
    {
        $this->separator_label=$separator_label;
        return $this;
    }
    
//
//    function setEnableButtons(bool $enableButtons): self
//    {
//        $this->enableButtons=$enableButtons;
//        return $this;
//    }
//
//    public function setEnableButtonsOpen(bool $enableButtonsOpen): self
//    {
//        $this->enableButtonsOpen=$enableButtonsOpen;
//        return $this;
//    }
//
//    public function setEnableButtonsClose(bool $enableButtonsClose): self
//    {
//        $this->enableButtonsClose=$enableButtonsClose;
//        return $this;
//    }
//
//    public function setEnableButtonsCheck(bool $enableButtonsCheck): self
//    {
//        $this->enableButtonsCheck=$enableButtonsCheck;
//        return $this;
//    }
//
//    public function setEnableButtonsUncheck(bool $enableButtonsUncheck): self
//    {
//        $this->enableButtonsUncheck=$enableButtonsUncheck;
//        return $this;
//    }
//
//    public function setEnableButtonsRestore(bool $enableButtonsRestore): self
//    {
//        $this->enableButtonsRestore=$enableButtonsRestore;
//        return $this;
//    }    


    function openOnInit(bool $state): self
    {
        if($state)
        {
            return $this->openAllOnInit();
        }
        else
        {
            return $this->closeAllOnInit();
        }
    }

    function openAllOnInit(): self
    {
        $this->openAllOnInit=true;
        //$this->openAllOnInitIfNoSelected=false;
        return $this;
    }

    function closeAllOnInit(): self
    {
        $this->openAllOnInit=false;
        return $this;
    }

    function openAllOnInitIfNoSelected(): self
    {
        //$this->openAllOnInitIfNoSelected=true;
        $this->openAllOnInit=false;
        return $this;
    }

//    function doNotopenAllOnInitIfNoSelected(): self
//    {
//        $this->openAllOnInitIfNoSelected=false;
//        return $this;
//    }
     
//    public function forcePositionOnDiv(string $idNode,string $idPanel) : void
//    {
//        $this->idNode=$idNode;
//        $this->idPanel=$idPanel;
//    }
    
    /**
     * 
     * @param array<int,KTreeJSNode|null> $childrens
     * @return string
     */
    private function makeHtmlChildrens(array $childrens) : string
    {
        $html='';
        foreach($childrens as $node)
        {
            if(!is_null($node))
            {
                $html.=$this->makeHtmlNode($node);
            }
        }
        return $html;
    }
    
 
    private function makeHtmlNode(KTreeJSNode $node) : string
    {
        $open= $this->openAllOnInit ? 'open=""':''; 
        $html='';
        $childrens=$node->getChildrens();
        /** @phpstan-ignore-next-line */
        if(is_array($childrens)&&count($childrens))
        {
            $onclick='';
            $onclick_end='';
            if(!empty($node->getOnClick()))
            {
                $onclick=' <a '.$onclick.' href="javascript:void(0);" onclick="'.$node->getOnClick().'" >';
                $onclick_end=' </a> ';
            }
            
            $drawNode='';
            if(!$this->checkbox)
            {
                if($this->purifyText)
                {
                    $drawNode=kPurify($node->getText());
                }
                else
                {
                    $drawNode=$node->getText();
                }
            }
            else
            {
                $drawNode=$node->makeInputHTML(postValue: $this->postValue);
            }
            
            $html='  
<details  '.$open.'>
    <summary>
   '.$onclick.$drawNode.$onclick_end.'
     </summary>        
        <div class="treecss_folder">
   '.$this->makeHtmlChildrens($childrens).'
        </div>
</details> 
';            
        }
        else
        {
            $onclick='';
            $onclick_end='';
            if(!empty($node->getOnClick()))
            {
                $onclick=' <a '.$onclick.' href="javascript:void(0);" onclick="'.$node->getOnClick().'" >';
                $onclick_end=' </a> ';
            }
            
             $drawNode='';
            if(!$this->checkbox)
            {
                if($this->purifyText)
                {
                    $drawNode=kPurify($node->getText());
                }
                else
                {
                    $drawNode=$node->getText();
                }
            }
            else
            {
                $drawNode=$node->makeInputHTML(postValue: $this->postValue);
            }
            
            $html='   
<div class="treecss_details">
   '.$onclick.$drawNode.$onclick_end.'
</div>
'; 
        }
        return $html;
    }

    public function draw(): string
    {
        $insideHtml="";
        /* @var $node KTreeJSNode */
        foreach($this->array as $node)
        {
            $insideHtml.=$this->makeHtmlNode($node);
        }


        $html='<div class="form-group row">';
        if($this->label!=null)
        {
            $html.='<label for="'.$this->label.'" class="col-'.$this->colLabel.' label_form">'.$this->label.$this->separator_label.'</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html.='<div class="col-12">';
        }
        
        $html.='            
        <div id="'.$this->getName().'"> 
            <div id="'.$this->getName().'_inner_html">
            '.$insideHtml.'
            </div>
        </div>
    </div> <!--end col -->
</div> <!--end row -->
';    
        return $html;
    }
}