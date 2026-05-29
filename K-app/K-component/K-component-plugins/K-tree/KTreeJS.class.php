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
 * Description of KTreeJS
 *
 * @author louis.mulot
 */
class KTreeJS extends KComponent
{
    private bool $checkbox=false;
    /**
     * 
     * @var array<int,KTreeJSNode|null>
     */
    private array $array=array();
    private ?string $label=null;
    private ?int $colLabel=null;
    private ?int $colInput=null;
    private string $separator_label="";
    private bool $openAllOnInit=false;
    private bool $openAllOnInitIfNoSelected=false;
    private string $enableIcons="true"; // Sous format string pour pouvoir l'utiliser avec le JS
    // Boutons
    private bool $enableButtons=false;
    private bool $enableButtonsOpen=true;
    private bool $enableButtonsClose=true;
    private bool $enableButtonsCheck=true;
    private bool $enableButtonsUncheck=true;
    private bool $enableButtonsRestore=true;
    // Tri alphabétique 
    private string $sortPlugin="";
    //Set position on opening
    private string $idNode="";
    private string $idPanel="";   
    private bool $postValue=true;

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
        $this->setName("treejs_".$name);

        $this->setArrayNodes($array);

        $this->checkbox=$checkbox;

        $this->label=$label;
        $this->setColLabelAndInput($colLabel,$colInput);
        self::includeFilesToLayout();
    }
    
    public static function includeFilesToLayout() : void
    {
        $layout=KApp::getInstance()->getLayout();
        $layout->addCSSFileToBuffer(__DIR__."/js/themes/proton/style.min.css");
        //$layout->addCSSFileToBuffer(__DIR__."/js/themes/bootstrap/style.css");
        $layout->addJsFileToBuffer(__DIR__."/js/jstree.min.js");        
    }

    /**
     * 
     * @param array<int,KTreeJSNode|null>|null $array
     * @return bool
     */
    public function setArrayNodes(?array $array=null): bool
    {
        if(is_array($array)&&count($array))
        {
            $new_array=[];
            /* @var $node KTreeJSNode */
            foreach($array as $node)
            {
                if(!is_null($node)) /*&&$node instanceof KTreeJSNode) */
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

    function setColLabelAndInput(?int $colLabel,?int $colInput): KTreeJS
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

    function setColLabel(int $colLabel): KTreeJS
    {
        return $this->setColLabelAndInput($colLabel,$this->colInput);
    }

    function setColInput(int $colInput): KTreeJS
    {
        return $this->setColLabelAndInput($this->colLabel,$colInput);
    }

    function setLabel(string $label): KTreeJS
    {
        $this->label=$label;
        return $this;
    }

    function setSeparator_label(string $separator_label): KTreeJS
    {
        $this->separator_label=$separator_label;
        return $this;
    }

    function setEnableButtons(bool $enableButtons): KTreeJS
    {
        $this->enableButtons=$enableButtons;
        return $this;
    }

    public function setEnableButtonsOpen(bool $enableButtonsOpen): KTreeJS
    {
        $this->enableButtonsOpen=$enableButtonsOpen;
        return $this;
    }

    public function setEnableButtonsClose(bool $enableButtonsClose): KTreeJS
    {
        $this->enableButtonsClose=$enableButtonsClose;
        return $this;
    }

    public function setEnableButtonsCheck(bool $enableButtonsCheck): KTreeJS
    {
        $this->enableButtonsCheck=$enableButtonsCheck;
        return $this;
    }

    public function setEnableButtonsUncheck(bool $enableButtonsUncheck): KTreeJS
    {
        $this->enableButtonsUncheck=$enableButtonsUncheck;
        return $this;
    }

    public function setEnableButtonsRestore(bool $enableButtonsRestore): KTreeJS
    {
        $this->enableButtonsRestore=$enableButtonsRestore;
        return $this;
    }

    public function setEnableIcons(bool $enableButtonsRestore): KTreeJS
    {
        if($enableButtonsRestore)
        {
            $this->enableIcons="true";
        }
        else
        {
            $this->enableIcons="false";
        }
        return $this;
    }
    
    public function setSort(bool $sort): KTreeJS
    {
        $this->sortPlugin=$sort ? "sort," : "";
        return $this;
    }

    function openOnInit(bool $state): KTreeJS
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

    function openAllOnInit(): KTreeJS
    {
        $this->openAllOnInit=true;
        $this->openAllOnInitIfNoSelected=false;
        return $this;
    }

    function closeAllOnInit(): KTreeJS
    {
        $this->openAllOnInit=false;
        return $this;
    }

    function openAllOnInitIfNoSelected(): KTreeJS
    {
        $this->openAllOnInitIfNoSelected=true;
        $this->openAllOnInit=false;
        return $this;
    }

    function doNotopenAllOnInitIfNoSelected(): KTreeJS
    {
        $this->openAllOnInitIfNoSelected=false;
        return $this;
    }
    
    public function getPostValue(): bool
    {
        return $this->postValue;
    }

    public function setPostValue(bool $postValue): void
    {
        $this->postValue = $postValue;
    }
     
    public function forcePositionOnDiv(string $idNode,string $idPanel) : void
    {
        $this->idNode=$idNode;
        $this->idPanel=$idPanel;
    }
    
    public function draw(): string
    {
        $insideHtml="";
        $dataJS="";
        $i=0;
        /* @var $node KTreeJSNode */
        foreach($this->array as $node)
        {
            //KDebugger::getInstance()->dump($this->getPostValue(),$this->getName());
            $insideHtml.=$node->makeHTML($this->getName(),$this->getPostValue());
//            KDebugger::getInstance()->dump($this->getName(),"node");
            if($i!=0)
            {
                $dataJS.=',';
            }
            $dataJS.=$node->makeJSData($this->checkbox);
            $i++;
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


        if($this->enableButtons)
        {
            if($this->enableButtonsOpen)
            {
                $button_open=new ButtonComponent(Ki18::_("TREE_BUTTON_1"),ButtonComponent::$TYPE_PRIMARY,"fa fa-plus");
                $button_open->setClickAction('open_'.$this->getName().'();');
                $html.=$button_open->draw()."&nbsp;";
            }

            if($this->enableButtonsClose)
            {
                $button_close=new ButtonComponent(Ki18::_("TREE_BUTTON_2"),ButtonComponent::$TYPE_SECONDARY,"fa fa-minus");
                $button_close->setClickAction('close_'.$this->getName().'();');
                $html.=$button_close->draw()."&nbsp;";
            }
            if($this->enableButtonsCheck)
            {
                $button_check=new ButtonComponent(Ki18::_("TREE_BUTTON_3"),ButtonComponent::$TYPE_INFO,"fas fa-check");
                $button_check->setClickAction('check_'.$this->getName().'();');
                $html.=$button_check->draw()."&nbsp;";
            }
            if($this->enableButtonsUncheck)
            {
                $button_uncheck=new ButtonComponent(Ki18::_("TREE_BUTTON_4"),ButtonComponent::$TYPE_SECONDARY,"fas fa-times");
                $button_uncheck->setClickAction('uncheck_'.$this->getName().'();');
                $html.=$button_uncheck->draw()."&nbsp;";
            }
            if($this->enableButtonsRestore)
            {
                $button_restore=new ButtonComponent(Ki18::_("TREE_BUTTON_5"),ButtonComponent::$TYPE_SUCCESS,"fas fa-trash-restore-alt");
                $button_restore->setClickAction('restore_'.$this->getName().'();');
                $html.=$button_restore->draw()."&nbsp;";
            }

            $html.="<br /><br />";
        }


        $plugins=$this->sortPlugin;
        if($this->checkbox)
        {
            $plugins.='"checkbox","wholerow"';
        }
        
        $html.='            
<div id="'.$this->getName().'"> 
</div>

<div id="'.$this->getName().'_inner_html">
'.$insideHtml.'
</div>

</div>
</div>
';
        $html.=' 
<script>
function setPosition_'.$this->getName().'()
{
';
        if(!empty($this->idNode)&&!empty($this->idPanel))
        {     
            $html.='
    $(function() 
    {
        let id_node = $("#'.$this->idNode.'");
        let panel=$("#'.$this->idPanel.'");
        if(id_node&&panel)
        {
            panel.animate({scrollTop: id_node.offset().top - panel.offset().top});
        }
    });
';
        }
        $html.=' 
}

$(function() 
{
    init_'.$this->getName().'(false);
});

var '.$this->getName().'_inner_html = document.getElementById("'.$this->getName().'_inner_html").innerHTML;

function init_'.$this->getName().'(reiterate)
{
    let data=['.$dataJS.'];
    $("#'.$this->getName().'").jstree(
    {
        "plugins": ['.$plugins.'],
        "core": {
            "themes": {
                "name": "proton",
                "responsive": true,
                "icons": '.$this->enableIcons.'
            },
            "data" : data
        },
        "checkbox" : {
                "three_state" : false,
                "cascade" : "up+undetermined"
        },
    })
    .bind("loaded.jstree", function (event, data) 
    {
';
        if($this->openAllOnInit)
        {
            $html.='         
        $(this).jstree("open_all");
';
        }
        else if($this->openAllOnInitIfNoSelected&&$this->checkbox)
        {
            $html.='
        if(
         (!$(this).jstree().get_undetermined() || !$(this).jstree().get_undetermined().length)
         &&
         (!$(this).jstree().get_selected() || !$(this).jstree().get_selected().length)
         )
        {
            $(this).jstree("open_all");
        }
';
        }
        if($this->checkbox)
        {
            $html.='  
        if($(this).jstree())
        {
            $(this).jstree().get_undetermined().forEach(function(id) 
            {
                $("#'.$this->getName().KTreeJSNode::$IDENTIFIER.'"+id).val("true");   
            });
        }
        ';
        }
        $html.='  
            
        if(reiterate)
        {
            iterateOverFirstLevel_'.$this->getName().'(false);
            document.getElementById("'.$this->getName().'_inner_html").innerHTML='.$this->getName().'_inner_html;    
        }
    })
    .on("select_node.jstree", function (evt, data) 
    {
//        console.log("Select");
//        console.log(data);
//        console.log(data.node.a_attr.href);
        ';
        if($this->checkbox)
        {
            $html.='
        checkAllNodesJson'.$this->getName().'(data.node,true);
            ';
        }
        else
        {
            $html.='
        if (data.node.a_attr.href != "#" & data.event.bubbles) 
        {
            window.open(data.node.a_attr.href, data.node.a_attr.target);
        } 
        ';
        }
        $html.='
    })
    .on("deselect_node.jstree", function (evt, data) 
    {
        //console.log("Deselect");
        //console.log(data);
        ';
        if($this->checkbox)
        {
            $html.='
        checkAllNodesJson'.$this->getName().'(data.node,true);
            ';
        }
        $html.='
    })
    .bind("ready.jstree",function (event, data) 
    {
        setPosition_'.$this->getName().'();
    })
    ;
}

function open_'.$this->getName().'()
{
    $("#'.$this->getName().'").jstree("open_all");
}

function close_'.$this->getName().'()
{
    $("#'.$this->getName().'").jstree("close_all");
}

function uncheck_'.$this->getName().'()
{
    $("#'.$this->getName().'").jstree("deselect_all",false);
    //console.log(data);
    iterateOverFirstLevel_'.$this->getName().'(false);
}

function check_'.$this->getName().'()
{
    $("#'.$this->getName().'").jstree("select_all",false);   
        
    iterateOverFirstLevel_'.$this->getName().'(false);
}

function restore_'.$this->getName().'()
{ 
    $("#'.$this->getName().'").jstree("destroy");
    init_'.$this->getName().'(true);  
    
}

function iterateOverFirstLevel_'.$this->getName().'(iterate_as_first)
{
    let v=$("#'.$this->getName().'").jstree().get_json("#");
    for (i = 0; i < v.length; i++) 
    {
        checkAllNodesJson'.$this->getName().'(v[i],iterate_as_first);
    }
}

function checkAllNodesJson'.$this->getName().'(node,first)
{
    let debug=false;
    if(node.id)
    {
        //console.log(node); 
        let value_selected=convertSelectedToNumber'.$this->getName().'(node.state.selected); 
        if(debug)
        {    
            console.log("ID="+node.id+"=>"+node.state.selected+"=>"+value_selected);
        }
        $("#'.$this->getName().KTreeJSNode::$IDENTIFIER.'"+node.id).val(value_selected);
        if(debug)
        {
            console.log("Value changed ==> "+node.id+" => "+value_selected);
        }
        node.children.forEach(function(id) 
        {
            let the_node=$("#'.$this->getName().'").jstree(true).get_json(id, {"flat": false});
            checkAllNodesJson'.$this->getName().'(the_node,false);    
        });
        if(first)
        {
            node.parents.forEach(function(id) 
            {
                if(id!="#")
                {
                    if(value_selected=="true")
                    {
                        let the_node=$("#'.$this->getName().'").jstree(true).get_json(id, {"flat": false});
                        if(the_node)
                        {
                            if(debug)
                            {
                                console.log("a)Value changed ==> "+the_node.id+" => "+value_selected);
                            }
                            $("#'.$this->getName().KTreeJSNode::$IDENTIFIER.'"+the_node.id).val(value_selected);   
                        }                            
                    }
                    else
                    {
                        let the_node=$("#'.$this->getName().'").jstree(true).get_json(id, {"flat": false});
                        if(the_node)
                        {
                            let children_select=0;
                            the_node.children.forEach(function(id_children)  
                            {
                                let the_node_children=$("#'.$this->getName().'").jstree(true).get_json(id_children, {"flat": false});
                                if($("#'.$this->getName().KTreeJSNode::$IDENTIFIER.'"+the_node_children.id).val()=="true")
                                {
                                    children_select=1;
                                    if(debug)
                                    {
                                        console.log("SELECT YES "+the_node_children.id);
                                    }
                                }
                            });
                            if(children_select)
                            {
                                if(debug)
                                {
                                    console.log("b)Value changed ==> "+the_node.id+" => "+convertSelectedToNumber'.$this->getName().'(1));
                                }
                                $("#'.$this->getName().KTreeJSNode::$IDENTIFIER.'"+the_node.id).val(convertSelectedToNumber'.$this->getName().'(1)); 
                            }
                            else
                            {
                                if(debug)
                                {
                                    console.log("c)Value changed ==> "+the_node.id+" => "+convertSelectedToNumber'.$this->getName().'(0));
                                }                            
                                $("#'.$this->getName().KTreeJSNode::$IDENTIFIER.'"+the_node.id).val(convertSelectedToNumber'.$this->getName().'(0)); 
                            }
                        }                             
                    }                    
                }
            });     
        }
    }
}

function convertSelectedToNumber'.$this->getName().'(selected)
{
    if(selected)
    {
        return "true";
    }
    return "false";
}

</script>
';
        return $html;
    }

}