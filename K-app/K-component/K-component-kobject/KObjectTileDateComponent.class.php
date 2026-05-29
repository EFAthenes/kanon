<?php
/**
 * Description of KObjectTileDateComponent
 *
 * @author louis.mulot
 */
class KObjectTileDateComponent extends KComponent
{
    public function __construct(KObject $kobject)
    {
        parent::__construct();
        $this->setNone();
        $tile2 = new TileComponent();
        $tile2->addComponent(new InputStringComponent($kobject->getDate_created(), '', 'Date Creation', null, false, true,2,10));
        $tile2->addComponent(new InputStringComponent($kobject->getDate_modified(),'', 'Date Modification', null, false, true,2,10));
        $this->addComponent($tile2);
    }
}