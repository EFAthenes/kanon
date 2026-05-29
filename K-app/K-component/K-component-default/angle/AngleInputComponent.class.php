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
class AngleInputComponent extends KComponent
{
    public function __construct(string $name)
    {        
        parent::__construct();
        $layout=KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/AngleInputComponent.js",true);
        $layout->addCSSFileToBuffer(__DIR__."/css/AngleInputComponent.css");
        // We replace the spaces in the name
        $name_without_space = str_replace(' ', '_', $name);
        // We put a random number at the end of the name so that there is no duplicate name
        $this->setName($name_without_space.KRandom::makeRandomString());
        $this->setNone();
        $this->addComponent(new InputIntegerComponent(0, $this->getName()."-input-circle", null, null, true, false, null, null, [$this->getName().'-input']));
        
        $this->addJSTextOnDocumentReady('initAngleInput("'.$this->getName().'","'.$this->getName().'-input'.'");');
    }

    public function draw() : string
    {
        // Attention !
        // For the class name of the first div element (default-circle),
        // "$this->getName()" MUST BE the first class name to appear and musn't have a space in itself
        // This is because in the file "AngleInputComponent.js" the name is isolated by using the "split" method and a space (' ') as a separator
        $string='
<div class="'.$this->getName().' default-circle" tabindex="0">
    <input type="hidden" name="angle" value="0">
    <span class="angle-circle-pivot" style="transform: rotate(0deg);"></span>
</div>
';
//<input class="plain-angle-input" type="number" value="0" step="1"/>    
        return $string.parent::draw();
    }
}