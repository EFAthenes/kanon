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
 * Description of KTreeJSNode
 *
 * @author louis.mulot
 */
class KTreeJSNode extends KComponent
{
    private string $idKey="";
    private string $text="";
    private int $selected=0;
    /**
     * 
     * @var array<int,KTreeJSNode|null>
     */
    private array $childrens=[];
    private int $disable=0;
    private ?string $url=null;
    private ?string $onClick=null;
    public static string $IDENTIFIER="_input_node_";
    private bool $all_childrens_selected=false;
    private ?string $icon=null;
    private string $typeTarget="_blank";

    /**
     * 
     * @param mixed $id
     * @param string $text
     * @param bool $selected
     * @param array<int,KTreeJSNode|null>|null $childrens
     * @param string|null $url
     * @param string|null $icon
     * @param bool $newTab
     * @param string|null $onClick
     */
    public function __construct(mixed $id,string $text,bool $selected=false,?array $childrens=null,?string $url=null,?string $icon="",bool $newTab=false,?string $onClick=null)
    {
        parent::__construct();
        $this->idKey="".$id;
        $this->text=$text;
        $this->url=$url;
        $this->onClick=$onClick;
        $this->typeTarget=$newTab ? "_blank" : "_self";
        $this->icon=is_null($icon) ? "" : $icon;
        $this->setSelected($selected);
        $this->setChildrens($childrens);
    }
    
    public function getIdKey() : string
    {
        return $this->idKey;
    }

    public function setDisable(bool $disable): KTreeJSNode
    {
        if($disable)
        {
            $this->disable=1;
        }
        else
        {
            $this->disable=0;
        }
        return $this;
    }

    public function setSelected(bool $selected): void
    {
        if($selected)
        {
            $this->selected=1;
        }
        else
        {
            $this->selected=0;
        }
    }

    public function getSelectedString(): string
    {
        if($this->selected)
        {
            return "true";
        }
        return "false";
    }

    public function getDisableString(): string
    {
        if($this->disable)
        {
            return "true";
        }
        return "false";
    }
    
//    public function getId(): string
//    {
//        return $this->id;
//    }    

    public function getText(): string
    {
        return $this->text;
    }

    public function hasChildren(): bool
    {
        return count($this->childrens)>0;
    }

    public function addChildren(?KTreeJSNode $node): bool
    {
        if(!is_null($node))
        {
            $this->childrens[]=$node;
            return true;
        }
        return false;
    }
    
    public function getOnClick() : ?string
    {
        return $this->onClick;
    }
    
    /**
     * 
     * @return array<int,KTreeJSNode|null>
     */
    public function getChildrens(): array
    {
        return $this->childrens;
    }

    /**
     * 
     * @param array<int,KTreeJSNode|null>|null $array
     * @return bool
     */
    public function setChildrens(?array $array=null): bool
    {
        if(is_array($array)&&count($array))
        {
            $new_array=[];
            foreach($array as $node)
            {
                if(!is_null($node))
                {
                    $new_array[]=$node;
                }
                else
                {
                    return false;
                }
            }
            $this->childrens=$new_array;
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param array<int,KTreeJSNode|null>|null $array
     * @return bool
     */
    public function setChildrensWithNull(?array $array=null): bool
    {
        if(is_array($array)&&count($array))
        {
            $new_array=[];
            foreach($array as $node)
            {
                if(!is_null($node))
                {
                    $new_array[]=$node;
                }
                else
                {
                    return false;
                }
            }
            $this->childrens=$new_array;
        }
        else
        {
            $this->childrens=[];
        }
        return true;
    }
    
    public function makeInputHTML(string $identifier="",bool $postValue=true): string
    {
        $html='';
        if($postValue)
        {        
            $html='<input id="'.$identifier.self::$IDENTIFIER.$this->idKey.'" name="'.$identifier.self::$IDENTIFIER.$this->idKey.'" type="checkbox" value="'.$this->getSelectedString().'">'.$this->getText();
        }
//        $debug=false;
//        if($debug)
//        {
//            $html=''.$this->id.' : <input id="'.$identifier.self::$IDENTIFIER.$this->idKey.'" name="'.$identifier.self::$IDENTIFIER.$this->idKey.'" type="text" value="'.$this->getSelectedString().'"> <br />';
//        }
        /* @var KtreeJSNode $children */
        foreach($this->childrens as $children)
        {
            $html.=$children->makeHTML($identifier,$postValue);
        }
        return $html;
    }    

    public function makeHTML(string $identifier="",bool $postValue=true): string
    {
        $html='';
        if($postValue)
        {
            $html='<input id="'.$identifier.self::$IDENTIFIER.$this->idKey.'" name="'.$identifier.self::$IDENTIFIER.$this->idKey.'" type="hidden" value="'.$this->getSelectedString().'">';
        }

//        $debug=false;
//        if($debug)
//        {
//            $html=''.$this->id.' : <input id="'.$identifier.self::$IDENTIFIER.$this->idKey.'" name="'.$identifier.self::$IDENTIFIER.$this->idKey.'" type="text" value="'.$this->getSelectedString().'"> <br />';
//        }
        /* @var KtreeJSNode $children */
        foreach($this->childrens as $children)
        {
            $html.=$children->makeHTML($identifier,$postValue);
        }
        return $html;
    }

    /**
     * 
     * @param array<int,mixed> $array
     * @param int $level
     * @param string $delimitor
     */
    public function makeNodeArray(array &$array,int $level=1,string $delimitor="-") : void
    {
        $array[]=[$this->idKey,$this->getOptionText($level,$delimitor)];
        /* @var KtreeJSNode $children */
        foreach($this->childrens as $children)
        {
            $children->makeNodeArray($array,($level+1),$delimitor);
        }
    }

    private function getOptionText(int $level,string $delimitor="-"): string
    {
        $before_string="";
        for($i=1; $i<$level; $i++)
        {
            $before_string.=$delimitor;
        }
        return $before_string.$this->text;
    }

    public function makeJSData(bool $checkboxMode=false): string
    {
        $js='';
        $icon='';
        if(!empty($this->icon))
        {
            $icon.='"icon" : "'.$this->icon.'",';
        }
        if(count($this->childrens)==0)
        {
            $this->all_childrens_selected=boolval($this->selected);
            $url="";
            if(!is_null($this->url)||!is_null($this->onClick))
            {
                $onclickString='';
                if(!is_null($this->onClick))
                {
                    $onclickString=',"onclick":"'.$this->onClick.'"';
                }
                $url=' ,"a_attr":{"target":"'.$this->typeTarget.'","href":"'.$this->url.'"'.$onclickString.'} ';
            }
            $js='{ "text" : '. (json_encode($this->text,JSON_UNESCAPED_UNICODE)).','.$icon.' "state" : { "selected" : '.$this->getSelectedString().', "disabled" : '.$this->getDisableString().' } ,"id" : "'.$this->idKey.'" '.$url.' }';
        }
        else
        {
            //"undetermined" : true // "selected" : '.$this->getSelectedString().' ,

            $js_childrens=',"children" : [';
            $i=0;

            $state='selected';
            foreach($this->childrens as $children)
            {
                if($i!=0)
                {
                    $js_childrens.=',';
                }
                $js_childrens.=$children->makeJSData($checkboxMode);
                if($checkboxMode)
                {
                    if(!$children->getAll_childrens_selected())
                    {
                        //     $state='undetermined'; --> if you want the father to select all
                        $state='selected';    
                    }
                }
                $i++;
            }
            $js_childrens.=']';

            $url="";
            if(!is_null($this->url))
            {
                $url=' ,"a_attr":{"target":"'.$this->typeTarget.'","href":"'.$this->url.'"} ';
            }

            $js='{ "text" : '.(json_encode($this->text,JSON_UNESCAPED_UNICODE)).','.$icon.' "state" : { "'.$state.'" : '.$this->getSelectedString().', "disabled" : '.$this->getDisableString().' } ,"id" : "'.$this->idKey.'" '.$js_childrens.' '.$url.' }';
        }
        return $js;
    }

    public function getAll_childrens_selected(): bool
    {
        return $this->all_childrens_selected;
    }

}