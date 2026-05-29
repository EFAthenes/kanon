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

class InputRangeComponent extends InputIntegerComponent
{
    function __construct(?int $inputValue, int $min, int $max, string $inputName, ?string $label = null, ?string $placeholder = null, bool $require = false, bool $readOnly = false, ?int $colLabel = 1, ?int $colInput = 11, ?array $class_names = [])
    {
        parent::__construct($inputValue, $inputName, $label, $placeholder, $require, $readOnly, $colLabel, $colInput, $class_names);

        $this->setMin_value($min);
        $this->setMax_value($max);

        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/../js/bootstrap-slider.min.js", true);
        $layout->addCSSFileToBuffer(__DIR__."/../css/bootstrap-slider.min.css");

        $this->addCssText(".slider.slider-horizontal { margin-top: 8px; }");

        $css_selector = "#".$inputName;

        $this->addJSTextOnDocumentReady("
            if(typeof sliders != 'undefined') sliders['".$inputName."'] = $('".$css_selector."').bootstrapSlider();

            else {
                sliders = { ".$inputName.": $('".$css_selector."').bootstrapSlider() }
            }
        ");
    }

    public function draw(): string
    {
        $html = '<div class="form-group row">';

        if ($this->label != null)
        {
            $html .= '<label for="'.$this->label.'" class="col-'.$this->colLabel.' label_form">'.$this->label.$this->separator_label.'</label><div class="col-'.$this->colInput.'">';
        }
        else
        {
            $html .= '<div class="col-12">';
        }

        $html .= '<input type="text" class="'.$this->getClassName().'"';

        if ($this->require)
        {
            $html .= ' required="required" ';
        }
        if ($this->readOnly)
        {
            $html .= ' readonly="readonly" ';
        }
        if (!empty($this->placeholder))
        {
            $html .= ' placeholder="'.FormComponent::inputString($this->placeholder).'" ';
        }

        $html .= ' data-slider-min="'.$this->min_value.'" ';
        $html .= ' data-slider-max="'.$this->max_value.'" ';

        if (!is_null($this->step))
        {
            $html .= ' data-slider-step="'.$this->step.'" ';
        }

        $html .= ' name="'.$this->inputName.'" id="'.$this->inputName.'" data-slider-id="'.$this->inputName.'Slider" ';

        $inputValue = "";
        if (!is_null($this->inputValue))
        {
            $inputValue = $this->inputValue;
        }
        $html .= ' data-slider-value="'.FormComponent::inputString($inputValue).'" />';

        $html .= '</div></div>';
        return $html;
    }
}