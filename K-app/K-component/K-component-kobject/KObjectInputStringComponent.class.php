<?php
/**
 * Description of KObjectInputStringComponent
 *
 * @author louis.mulot
 */
class KObjectInputStringComponent extends InputStringComponent
{
    //put your code here
    function __construct(KObject $kobject,string $kobject_field,bool $require=false,bool $readOnly=false)
    {        
        parent::__construct($kobject->getFieldValue($kobject_field),
                $kobject->getInputValueFieldName($kobject_field),
                $kobject_field,
                "",
                $require,
                $readOnly,
                2,10);
    }
}