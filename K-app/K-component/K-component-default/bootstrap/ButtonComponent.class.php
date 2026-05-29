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
class ButtonComponent extends KComponent
{
    protected string $label="";
    protected ? string $icon="";
    protected string $type="";
    protected string $popup="";
    protected string $tooltip="";
    protected string $tooltipPlacement="";
    protected bool $showLabel=true;
    protected string $image_url="";
    protected bool $small=false;
    protected bool $large=false;
    
    public static string $TYPE_PRIMARY="primary";
    public static string $TYPE_SECONDARY="secondary";
    public static string $TYPE_SUCCESS="success";
    public static string $TYPE_INFO="info";
    public static string $TYPE_WARNING="warning";
    public static string $TYPE_DANGER="danger";

    public static string $TOOLTIP_AUTO="auto";
    public static string $TOOLTIP_TOP="top";
    public static string $TOOLTIP_RIGHT="right";
    public static string $TOOLTIP_BOTTOM="bottom";
    public static string $TOOLTIP_LEFT="left";
    
    protected bool $disable=false;
    protected bool $outline=false;
    
    protected ?string $form_id=null;
    protected string $url="";
    protected string $onclick_fct="";
    protected bool $new_tab=false;
    
    protected string $random_class_name="";

    /**
     * 
     * @var array<int,string>
     */
    protected array $arrayType=[];
    
    public function __construct(string $label,?string $type=null,?string $icon="",bool $outline=false,bool $disable=false,?string $form_id=null)
    {        
        parent::__construct();
        $this->setNone();  
                
        $this->random_class_name="KTitleButton_".KRandom::makeRandomUniquId();
             
        $this->label=$label;
        
        $this->arrayType=[self::$TYPE_PRIMARY,self::$TYPE_SECONDARY,self::$TYPE_SUCCESS,self::$TYPE_INFO,self::$TYPE_WARNING,self::$TYPE_DANGER];
        if(is_null($type)||!$this->setType($type))
        {
            $this->setType(self::$TYPE_PRIMARY);          
        }
        
        $this->setSubmitForm($form_id);
        
        $this->icon=$icon;
        $this->outline=$outline;
        $this->disable=$disable;
    }

    public function setDisable(bool $disable) : void
    {
        $this->disable = $disable;
    }
    
    public function setType(string $type) : bool
    {
        if(in_array($type,$this->arrayType))
        {
            $this->type=$type;
            return true;
        }
        return false;
    }
    
    function setSmall(bool $small) : self
    {
        $this->small = $small;
        return $this;
    }

    function setLarge(bool $large) : self
    {
        $this->large = $large;
        return $this;
    }
    
    public function setImageUrl(string $image_url) : self
    {
        $this->image_url=$image_url;
        return $this;
    }
    
    public function getImageUrl() : string
    {
        return $this->image_url;
    }

    public function setSubmitForm(?string $form_id) : void
    {
        $this->form_id=$form_id;
    }
    
    public function setActionKURL(KURL $kurl,bool $new_tab=false) : self
    {
        $this->url=$kurl->printURLWithoutAmp();
        $this->new_tab=$new_tab;
        return $this;
    }    
    public function setActionURL(string $url,bool $new_tab=false) : self
    {
        $this->url=$url;
        $this->new_tab=$new_tab;
        return $this;
    }
    /**
     * 
     * @param string $routeItem
     * @param array<string,mixed>|null $args
     * @param bool $new_tab
     * @return self
     */
    public function setActionRouteItem(string $routeItem,?array $args=null,bool $new_tab=false): self
    {
        $kurl= KRoute::makeKURL($routeItem,$args);
        return $this->setActionURL($kurl->printURLWithoutAmp(),$new_tab);
    }
    /**
     * 
     * @param string $routeItem
     * @param array<string,mixed>|null $args
     * @param bool $new_tab
     * @return self
     */
    public function setKActionRouteItem(string $routeItem,?array $args=null,bool $new_tab=false): self
    {
        $kurl= KRoute::makeActionKURL($routeItem,$args);
        return $this->setActionURL($kurl->printURLWithoutAmp(),$new_tab);
    }
    
    public function setClickAction(string $onclick_fct) : void
    {
        $this->onclick_fct=$onclick_fct;
    }
    
    public function showLabel(bool $showLabel) : self
    {
        $this->showLabel=$showLabel;
        return $this;
    }
     
    public function setActionURLWithOkPopUp(KURL $url,string $message,string $validate,string $cancel) : void
    {
        $this->popup='
<script>
if(true)
{
    let message = document.createElement("div");
    message.innerHTML = "'.convertDoubleQuotes($message).'";
    $(".'.$this->random_class_name.'").click(function() 
    {
        swal(
        {
            content: message,
            buttons: ["'.$cancel.'", "'.$validate.'"],
        }).then((result) =>
        {
            if (result) 
            {
                window.location.href ="'.$url->printURLWithoutAmp().'";
            } 
            else 
            {

            }
        });
    });
}   
</script>
';
    }  
    // Don't want to add Components
    public function addComponent(KComponent $component) : KComponent
    {
        return $this;
    }
    
 
    public function setToolType(string $tooltip, ?string $placement=null):void
    {
        if(is_null($placement))
        {
            $placement=self::$TOOLTIP_AUTO;
        }
        if($placement==self::$TOOLTIP_BOTTOM||$placement==self::$TOOLTIP_TOP||$placement==self::$TOOLTIP_AUTO
                ||$placement==self::$TOOLTIP_LEFT||$placement==self::$TOOLTIP_RIGHT)
        {
            if(!empty($tooltip))
            {
                $this->tooltip=$tooltip;
                $this->tooltipPlacement=$placement;
            }
        }
    }
    
    public function draw() : string
    {
        $id_tag="";
        if($this->isId() && $this->getName())
        {
            $id_tag=' id="'.$this->getName().'" ';
        }
        $typeButton="btn";
        if($this->outline)
        {
            $typeButton.="-outline";
        }
        $typeButton.="-".$this->type;
        
        $disableButton="";
        if($this->disable)
        {
            $disableButton=' disabled="" ';
        }
        
        $formButton="";        
        if(!is_null($this->form_id) && $this->form_id!="")
        {
            $formButton=' type="submit" form="'.$this->form_id.'" ';
        }   
        else
        {
            $formButton=' type="button" ';
        }
        
        $html="";

        if($this->popup==""&&$this->url!="")
        {
            $new_tab_string="";
            if($this->new_tab)
            {
                $new_tab_string=" target=\"_blank\" ";                
            }
            $html.='<a href="'.$this->url.'" '.$new_tab_string.' >';
        }
        
        $onclick_fct="";
        if($this->onclick_fct!="")
        {
           $onclick_fct='onclick="'.$this->onclick_fct.'"';
        }
        
        $tooltip="";
        if(!empty($this->tooltip))
        {
            $tooltip='data-toggle="tooltip" data-placement="'.$this->tooltipPlacement.'" title="'.$this->tooltip.'"';
        }
               
        $label=$this->label;
        if(!$this->showLabel)
        {
           $label=''; 
        }
        
        $html.='<button '.$id_tag.' class="btn '.$typeButton.' '.$this->random_class_name.' '.$this->getClassName().'" '.$disableButton.' '.$formButton.' '.$tooltip.' '.$onclick_fct.' >';
        if(!is_null($this->icon)&& $this->icon!="")
        {
            $html.='<i class="'.$this->icon.'"></i>&nbsp;<span class="">'.$label."</span>";
        }  
        else if($this->image_url!="")
        {
            $html.='<img style="width:18px;" src="'.$this->image_url.'">&nbsp;<span class="">'.$label."</span>";
        }
        else
        {
             $html.=$this->label;
        }
        $html.="</button>"; 
        if(!empty($this->popup))
        {
             $html.=$this->popup;
        }        
        else if(empty($this->popup)&&$this->url!="")
        {
            $html.='</a>';
        }
        if($tooltip!="")
        {
            $html.='<script> $(\'[data-toggle="tooltip"]\').tooltip(); </script>';
        }
        if(!$this->isVisible()&&!empty($id_tag))
        {
            $html.='<script> $(\'#'.$this->getName().'\').hide(); </script>';
        }
        
        return $html;
    }
}