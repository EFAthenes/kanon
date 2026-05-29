<?php
/**
 * Description of KObjectInputCheckBoxComponent
 *
 * @author louis.mulot
 */
class KObjectInputCheckBoxComponent extends CheckBoxComponent
{
    //put your code here
    function __construct(KObject $kobject,string $kobject_field,bool $require=false,bool $readOnly=false)
    {        
        parent::__construct(convertToBool($kobject->getFieldValue($kobject_field)),
                $kobject->getInputValueFieldName($kobject_field),
                $kobject_field,
                $require,
                $readOnly,
                2,10);
        $this->setValueLabelChecked("true");
        $this->setValueLabelunChecked("false");
    }
}