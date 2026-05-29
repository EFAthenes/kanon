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
use KTabPanelComponent as GlobalKTabPanelComponent;

class KTabPanelComponentWithFilter extends KTabPanelComponent
{

    private string $id_filter;
    private string $function_filter;
    private string $placeholder_filter;

    function __construct(string $name, string $id_filter, string $function_filter, string $placeholder_filter)
    {
        parent::__construct($name);
        $this->setNone();
        $this->setName($name);

        $this->id_filter = $id_filter;
        $this->function_filter = $function_filter;
        $this->placeholder_filter = $placeholder_filter;
    }

    public function draw() : string
    {
        $list = $this->getListComponent();
        if($list->getSize() > 0)
        {
            $html = '
<div role="tabpanel">
    <!-- Nav tabs -->';

            $html_1 = "";
            $html_2 = "";
            $i = 0;
            foreach($list as $panel)
            {
                $active = "";
                if($i == 0 && parent::getActiveTitle() == "")
                {
                    $active='active';
                }
                if(parent::getActiveTitle() == $panel->getTitle())
                {
                    $active = 'active';
                }
                $html_1 .= '<a class="nav-item nav-link '.$active.'"  href="#'.$this->makeLabel($i).'" aria-controls="'.$this->makeLabel($i).'" aria-selected="true" aria-expanded="true" role="tab" data-bs-toggle="tab" data-toggle="tab">'.$panel->getTitle().'</a>';
                $html_2 .= '<div role="tabpanel" class="tab-pane '.$active.'" id="'.$this->makeLabel($i).'">'.$panel->draw().'</div>';
                $i++;
            }

            $html .= '
    <div id="'.$this->getName().'" class="nav nav-tabs" role="tablist">
        '.$html_1.'
    </div>
    <div class="filtre-auteurs"> 
        '.
        (new InputStringComponent(null, $this->id_filter, null, $this->placeholder_filter, false, false, 0, 12, null, 'onkeyup="'.$this->function_filter.'"'))->draw()
        .'
    </div>
    <div class="tab-content">
        '.$html_2.'
    </div>
</div>';

            $this->addHTML($html);
        }
        return parent::drawOnlyThisComponent(true);
    }   
}
