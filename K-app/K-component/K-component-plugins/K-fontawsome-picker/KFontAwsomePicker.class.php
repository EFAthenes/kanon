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
class KFontAwsomePicker extends DivClassComponent
{
    /**
     * 
     * @param mixed $inputValue
     * @param string $inputName
     * @param string|null $label
     * @param string|null $placeholder
     * @param bool $require
     * @param bool $readOnly
     * @param int|null $colLabel
     * @param int|null $colInput
     * @param array<int,string>|null $class_names
     * @param string $input_option
     */
    final function __construct(mixed $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [], string $input_option="")
    {
        parent::__construct("input-group"); 
        $input =new InputStringComponent($inputValue,$inputName,$label,$placeholder,$require ,$readOnly,$colLabel,$colInput,$class_names,$input_option);
        $input->setJustInput();

        $className="icp-auto_".convertStringToBeW3C_Id($inputName);
        $input->addClass_Name("form-control icp ".$className);
        $this->addComponent($input);
        
        $classNameGroup="input-group-addon_".convertStringToBeW3C_Id($inputName);
        $this->addHtmlComponent('<span class="'.$classNameGroup.'"></span>');

        $layout = KApp::getInstance()->getLayout();
        $layout->addJsFileToBuffer(__DIR__."/js/fontawesome-iconpicker.js", false);
        $layout->addCSSFileToBuffer(__DIR__."/css/fontawesome-iconpicker.min.css");
        
        $js="
$(function () 
{
    let options = {
         component: '.".$classNameGroup."',
    };
    $('.".$className."').iconpicker(options);
});
";
        $this->addJSText($js);
        
        $css='
.iconpicker-popover.fade:not(.show) {
  opacity: 100;
}     
.'.$classNameGroup.'
{
  padding:10px 12px;
  font-size: 14px;
  font-weight: normal;
  line-height: 1;
  color: @input-color;
  text-align: center;
  background-color: #eee;
  border: 1px solid #ccc;
  border-radius: 4px;
}
';
        $this->addCssText($css);
        
/*
var options = {
    title: false, // Popover title (optional) only if specified in the template
    selected: false, // use this value as the current item and ignore the original
    defaultValue: false, // use this value as the current item if input or element value is empty
    placement: 'bottom', // (has some issues with auto and CSS). auto, top, bottom, left, right
    collision: 'none', // If true, the popover will be repositioned to another position when collapses with the window borders
    animation: true, // fade in/out on show/hide ?
    //hide iconpicker automatically when a value is picked. it is ignored if mustAccept is not false and the accept button is visible
    hideOnSelect: false,
    showFooter: false,
    searchInFooter: false, // If true, the search will be added to the footer instead of the title
    mustAccept: false, // only applicable when there's an iconpicker-btn-accept button in the popover footer
    selectedCustomClass: 'bg-primary', // Appends this class when to the selected item
    icons: [], // list of icon objects [{title:String, searchTerms:String}]. By default, all Font Awesome icons are included.
    fullClassFormatter: function(val) {
        return 'fa ' + val;
    },
    input: 'input,.iconpicker-input', // children input selector
    inputSearch: false, // use the input as a search box too?
    container: false, //  Appends the popover to a specific element. If not set, the selected element or element parent is used
    component: '.input-group-addon,.iconpicker-component', // children component jQuery selector or object, relative to the container element
    // Plugin templates:
    templates: {
        popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' +
            '<div class="popover-title"></div><div class="popover-content"></div></div>',
        footer: '<div class="popover-footer"></div>',
        buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' +
            ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
        search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
        iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
        iconpickerItem: '<a role="button" href="#" class="iconpicker-item"><i></i></a>',
    }
};
 */        
        
    }
    
    #[\Override]
    public static function testMe(): static
    {
        //mixed $inputValue,string $inputName,?string $label=null,?string $placeholder=null, bool $require = false,bool $readOnly=false,?int $colLabel=1,?int $colInput=11,?array $class_names = [], string $input_option="")
        $class=new static("fab fa-affiliatetheme","fontawsome","Label");
        return $class;
    }  
}