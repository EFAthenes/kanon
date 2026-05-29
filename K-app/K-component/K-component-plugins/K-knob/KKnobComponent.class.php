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
 * KKnoComponent permet d'instancier un un slider circulaire
 * ou un bouton rotatif en s'appuyant sur la librarie jquery.knob.js
 * @see http://anthonyterrien.com/demo/knob/
 * @author maxime.tueux@efa.gr
 */
class KKnobComponent extends KComponent
{
    protected int $defaultValue=0;
    protected int $min=0;
    protected int $max=1000;
    protected int $width=100;
    protected bool $displayInput=false;
    protected bool $cursorMode=false;
    protected bool $roundLinecap=false;
    protected bool $anticlockwise=false;
    protected bool $displayPrevious=false;
    protected int $angleOffset=0;
    protected int $angleArc=0;
    protected string $color="";
    protected bool $insideDiv=false;

    final function __construct(
        string $name,
        int $defaultValue = 0,
        int $min = 0,
        int $max = 360,
        int $width = 100,
        bool $displayInput = true,
        bool $cursorMode = false,
        bool $roundLinecap = false,
        bool $anticlockwise = false,
        bool $displayPrevious = false,
        int $angleOffset = 0,
        int $angleArc = 360,
        string $color = "#6b82ac",
    ) {
        parent::__construct();

        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__ . "/js/jquery.knob.min.js", false);
        $this->setNone();
        $this->setName($name);

        $this->defaultValue = $defaultValue;
        $this->min = $min;
        $this->max = $max;
        $this->width = $width;
        $this->displayInput = $displayInput;
        $this->cursorMode = $cursorMode;
        $this->roundLinecap = $roundLinecap;
        $this->anticlockwise = $anticlockwise;
        $this->displayPrevious = $displayPrevious;
        $this->angleOffset = $angleOffset;
        $this->angleArc = $angleArc;
        $this->color = $color;
    }
    
    public function insideDiv(bool $status) : void
    {
        $this->insideDiv=$status;
    }


    public function draw(): string
    {
        $html = '<input name="' . $this->name . '" value="' . $this->defaultValue . '" class="dial" data-width="' . $this->width . '" data-cursor="true" data-fgColor="' . $this->color . '" data-min="' . $this->min . '" data-max="' . $this->max . '" data-displayInput=' . $this->displayInput . ' data-angleOffset=' . $this->angleOffset . ' data-angleArc=' . $this->angleArc;

        if ($this->cursorMode) {
            $html .= ' data-cursor=true';
        }

        if ($this->roundLinecap) {
            $html .= ' data-linecap="round"';
        }

        if ($this->anticlockwise) {
            $html .= ' data-rotation="anticlockwise"';
        }

        if ($this->displayPrevious) {
            $html .= ' data-displayPrevious="true"';
        }

        $html .= '>';

        $html .= parent::draw();

        $this->addJSText('$(function() { $(".dial").knob(); });');

        if($this->insideDiv)
        {
            $div = new DivClassComponent($this->name."_div");
            $div->setStyleCode($this->getStyleCode());
            $this->setStyleCode("");
            $div->addHTML($html);
            $html=$div->draw();
        }
        
        return $html;
    }
    
    #[\Override]
    public static function testMe() : ?static
    { 
        $class=new static(name:"KKnob");
        $class->insideDiv(true);
        $class->addStyleCode("background-color:#FFFFFF;height:200px;width:200px;");
        return $class;
    }    
    
}
