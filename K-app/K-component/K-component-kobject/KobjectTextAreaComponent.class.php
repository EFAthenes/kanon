<?php
/**
 * Description of KObjectTextAreaComponent
 *
 * @author louis.mulot
 */
class KobjectTextAreaComponent extends TextAreaComponent
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
        //new TextAreaComponent($instit->getDescription(), $instit->getInputName_Description(), Institutions::$DESCRIPTION, null, false, false,2,10)
    }
}