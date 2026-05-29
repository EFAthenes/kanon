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
 * Description of ButtonExcelLinkJs
 *
 * @author Mulot Louis
 */
class ButtonExcelLinkJs extends KComponent
{
    private ?ButtonComponent $button = null;
    final public function __construct(string $label,?string $type=null,?string $icon="",bool $outline=false,bool $disable=false,?string $form_id=null)
    {        
        parent::__construct();
        $this->setNone();
        $this->button=new ButtonComponent($label,$type,$icon,$outline,$disable,$form_id);
        $this->addComponent($this->button);
        $this->setName($this->button->getName()."comp");
    }
    /**
     * 
     * @param array<int,array<int,mixed>> $array
     * @param string $nameInput
     * @param string $filename
     * @return void
     */
    public function makeExcelLink(array $array,string $nameInput="",string $filename="") : void
    {
        if(empty($nameInput))
        {
            $nameInput=$this->getName()."excel_link";
        }
        $excel= new ExcelLinkJs($nameInput,$array,$filename);
        $this->addComponent($excel);
        $this->button->setClickAction($excel->getFunction_nameStringJs());
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        $class=new static("Excel Button",ButtonComponent::$TYPE_PRIMARY);
        
        $class->makeExcelLink([["A","B"],[1,2]],"inputName","filename");
        
        return $class;
    }  
}
