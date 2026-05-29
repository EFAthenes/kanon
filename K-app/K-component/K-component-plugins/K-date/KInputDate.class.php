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
 * Description of KInputDate
 *
 * @author Louis Mulot
 */
class KInputDate extends InputStringComponent
{ 
    private static int $MODE_DAY=1;
    private static int $MODE_MONTH=2;
    private static int $MODE_YEAR=3;
    private static int $MODE_DAY_DB=4;
    
    private int $mode=1;
    private int $mode_post=0;
    private bool $can_be_null=false;
    private string $language="";
    
    /**
     * 
     * @param mixed $inputValue
     * @param string $inputName
     * @param string|null $label
     * @param string|null $placeholder
     * @param bool $require
     * @param bool $readOnly
     * @param int|null $colLabel
     * @param int|null $colInput
     * @param array<int,string>|null $class_names
     */
    final function __construct(mixed $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names=null)
    {    
        parent::__construct("",$inputName,$label,$placeholder,$require,$readOnly,$colLabel,$colInput,$class_names);
        if(!is_null($inputValue))
        {
            $this->setAmericanDate($inputValue);
        }
        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/bootstrap-datepicker.min.js");
        $layout->addCssFileToBuffer(__DIR__."/js/bootstrap-datepicker.min.css");
        
        
        $lang=LanguageManager::getInstance()->getLanguage();
        $this->setLanguage($lang);
        
    }  
    
    public function setFrenchDate(string $value,string $separator="/") : KInputDate
    {
        $array=explode($separator, $value);
        if(count($array)==3)
        {
            if(isInteger($array[0])&&isInteger($array[1])&&isInteger($array[2]))
            {
                $year=intval($array[2]);
                $day=intval($array[0]);
                $month=intval($array[1]);
                if(checkdate($month,$day,$year))
                {
                    $this->setInputValue($value);
                }
            }
        }        
        return $this;
    }
    
    public function setFrenchDateWithDash() : KInputDate
    {
        $this->setFrenchDate($this->getInputValue(),"/");
        return $this;
    }
    
    public function setFrenchDateWithSlash() : KInputDate
    {
        $this->setFrenchDate($this->getInputValue(),"-");
        return $this;
    }
    
    public function setAmericanDate(mixed $the_value,string $separator="-") : KInputDate
    {
        $value="".$the_value;
        $array=explode($separator,$value);
        if(count($array)==3)
        {
            if(isInteger($array[0])&&isInteger($array[1])&&isInteger($array[2]))
            {
                $year=intval($array[0]);
                $day=intval($array[2]);
                $month=intval($array[1]);                
                if(checkdate($month,$day,$year))
                {
                    $this->setInputValue($value);
                }
            }
        }
        else if(count($array)==1)
        {
            if(isInteger($value)&&strlen($value)<=4)
            {
                $newValue="";
                for ($i=0 ; $i< (4-strlen($value)) ; $i++)
                {
                    $newValue.=0;
                }
                $newValue.=$value;
                $this->setInputValue($newValue."-01-01");
            }          
        }
        return $this;
    }  
    
    public function setAmericanDateWithDash(string $value) : KInputDate
    {
        $this->setAmericanDate($value,"-");
        return $this;
    }
    
    public function setAmericanDateWithSlash(string $value) : KInputDate
    {
        $this->setAmericanDate($value,"/");
        return $this;
    }    
    
    public function setModeDay() : KInputDate
    {
        $this->mode=self::$MODE_DAY;
        return $this;
    }

    public function setModeMonth() : KInputDate
    {
        $this->mode=self::$MODE_MONTH;
        return $this;
    }

    public function setModeYear() : KInputDate
    {
        $this->mode=self::$MODE_YEAR;
        return $this;
    } 
    
    public function setModeDayDB() : KInputDate
    {
        $this->mode=self::$MODE_DAY_DB;
        $this->mode_post=1;
        return $this;
    }    
    
    public function setPostYear() : KInputDate
    {
        $this->mode_post=1;
        return $this;        
    }
    
    public function setPostYearMonthDay() : KInputDate
    {
        $this->mode_post=1;
        return $this;        
    }
    
    public function setCanBeNull(bool $can_be_null) : KInputDate
    {
        $this->can_be_null=$can_be_null;
        return $this;           
    }
    
    public function setLanguage(mixed $stringLanguage) : void
    {
        
        $this->language=$stringLanguage;
        $js_filename=__DIR__.'/locales/bootstrap-datepicker.'.$this->language.'.min.js';
        if(file_exists($js_filename))
        {
            $layout = KApp::getInstance()->getLayout();
            $layout->addJsFileToBuffer($js_filename);    
        }
    }

    public function draw(): string
    {
        $html = '<div class="form-group row" id="' . $this->getName() . '_datepicker_container">';
        if ($this->label != null)
        { //for="' . $this->label . '"
            $html .= '<label  class="col-'.$this->colLabel.' label_form">' . $this->label.$this->separator_label . '</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html .= '<div class="col-12">';
        }
        $html .= '<input type="text" class="form-control'.$this->getClassName().'"';
        
        if ($this->require)
        {
            $html .= ' required="required" ';
        }
        if ($this->readOnly)
        {
            $html .= ' readonly="readonly" ';
        }
        if($this->disabled)
        {
            $html .= ' disabled="disabled" ';
        }
        if(!empty($this->placeholder))
        {
            $html .= ' placeholder="'.FormComponent::inputString($this->placeholder).'" ';
        }
        
        $inputName=$this->getName()."_".KRandom::makeRandom();
        //name="' .$inputName . '"
        $html .= ' id="' . $inputName . '" ';

        $inputValue = "";
        $inputShowValue = "";
        if (!is_null($this->inputValue)&&$this->inputValue!="")
        {
            if($this->mode_post==1)
            {
                $inputValue = substr($this->inputValue,0,4);
            }
            else
            {
                $inputValue = $this->inputValue;
            }
            if($this->mode!=self::$MODE_DAY_DB)
            {
                $inputShowValue= $this->trunkValue(frenchDate($this->inputValue,"-","/"));
            }
            else
            {
                $inputShowValue=$this->inputValue;
            }
        }
        $html .= ' value="' . FormComponent::inputString($inputShowValue) . '" />';
        
        $html .= '<input type="hidden" name="'.$this->getInputName().'" id="'.$this->getInputName().'"  value="'.FormComponent::inputString($inputValue).'" />';

        $html .= '</div></div>';
        
        if(!$this->readOnly && !$this->disabled)
        {
            $mode="";
            $format="";
            if($this->mode==self::$MODE_DAY)
            {
                $format="dd/mm/yyyy";
                $mode="startView: 0,minViewMode: 0,";
            }
            else if($this->mode==self::$MODE_MONTH)
            {
                $format="mm/yyyy";
                $mode="startView: 1,minViewMode: 1,";
            }            
            else if($this->mode==self::$MODE_YEAR)
            {
                $format="yyyy";
                $mode="startView: 2,minViewMode: 2,";
            }
            else if($this->mode==self::$MODE_DAY_DB)
            {
                $format="yyyy-mm-dd";
                $mode="startView:0,minViewMode: 0,";
            }     
            
            $stringLanguage="";
            if(!empty($this->language))
            {
                $stringLanguage.='language: "'.$this->language.'",';
            }
            
            $html .= '
<script>
$("#'.$inputName.'").datepicker(
{
    '.$mode.'
    format: "'.$format.'",
    autoclose: true,
    '.$stringLanguage.'
    calendarWeeks: true
});
$("#'.$inputName.'").change(function() 
{     
    let date_input=$("#'.$inputName.'").val();
    if(date_input!="" && '.$this->mode_post.')
    {
        $("#'.$this->getInputName().'").val(date_input);
    }        
    else if(date_input!="")
    {       
        let newDate="'.$this->getInputValue().'";
        let array=date_input.split("/");
        if(array.length==3)
        {
            newDate="";
            for(var i = array.length-1; i >=0; i--)
            {
                newDate+=array[i];
                if(i!=0)
                {
                    newDate+="-";
                }
            }            
        } 
        else if(array.length==2)
        {
            newDate="";
            for(var i = array.length-1; i >=0; i--)
            {
                newDate+=array[i];
                if(i!=0)
                {
                    newDate+="-";
                }
            } 
            newDate+="-01"
        }  
        else if(array.length==1)
        {
            newDate=array[0]+"-01-01"
        }    
        $("#'.$this->getInputName().'").val(newDate);
    }
    else if('.convertBoolToString($this->can_be_null).')
    {
        $("#'.$this->getInputName().'").val("NULL");
    }
});
</script>
';
        }        
        return $html;
    }
    
    private function trunkValue(string $value) : string
    {
        if($this->mode==self::$MODE_DAY)
        {
            return $value;
        }
        else if($this->mode==self::$MODE_MONTH)
        {
            return substr($value,3);
        }            
        else if($this->mode==self::$MODE_YEAR)
        {
            return substr($value,6);
        }
        return $value;
    }
    
    #[\Override]
    public static function testMe() : ?static
    {
        //mixed $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names=null)
        $class=new static("","date_me","label");
        return $class;
    }     
    
}