<?php
/**
 * Description of UiActionVerificationItem

class UiActionVerificationItem
{
    private $value=null;
    private $field=null;
    private $condition=0;
    private $string_error="";
    public static $EQUAL="==";
    public static $NOT_EQUAL="!=";
    public static $SUPERIOR=">";
    public static $SUPERIOR_OR_EQUAL=">=";
    public static $INFERIOR="<";
    public static $INFERIOR_OR_EQUAL="<=";    
    function __construct($field,$value,$condition,$string_error)
    {
        $this->value=$value;
        $this->field=$field;
        $this->condition=$condition; 
        $this->string_error=$string_error;
    }
    function __destruct()
    {

    }
    function getValue()
    {
        return $this->value;
    }

    function getField()
    {
        return $this->field;
    }

    function getCondition()
    {
        return $this->condition;
    }

    function getString_error()
    {
        return $this->string_error;
    }

    function setValue($value)
    {
        $this->value = $value;
    }

    function setField($field)
    {
        $this->field = $field;
    }

    function setCondition($condition)
    {
        $this->condition = $condition;
    }

    function setString_error($string_error)
    {
        $this->string_error = $string_error;
    }
}
 * 
 */