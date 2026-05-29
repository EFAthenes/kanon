<?php
/**
 * Description of KObjectInputIntegerComponent
 *
 * @author louis.mulot
 */
class KObjectInputIntegerComponent extends InputIntegerComponent
{
    //put your code here
    function __construct(KObject $kobject,string $kobject_field,bool $require=false,bool $readOnly=false)
    {        
        parent::__construct(convertStringtoIntegerOrNull($kobject->getFieldValue($kobject_field)),
                $kobject->getInputValueFieldName($kobject_field),
                $kobject_field,
                "",
                $require,
                $readOnly,
                2,10);
    }
}