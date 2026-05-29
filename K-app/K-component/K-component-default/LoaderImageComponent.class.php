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
class LoaderImageComponent extends KComponent
{
    public function __construct(string $id="")
    {        
        parent::__construct();
        if(!empty($id))
        {
            $this->setId();
            $this->setIdName($id);
        }
        $this->addHtmlComponent('<div class="kdualring"></div>');  
        $this->initCss();
    }
    private function initCss() : void
    {
        $css='
.kdualring {
  display: inline-block;
  width: 80px;
  height: 80px;
}
.kdualring:after {
  content: " ";
  display: block;
  width: 64px;
  height: 64px;
  margin: 8px;
  border-radius: 50%;
  border: 6px solid '.StyleManager::getInstance()->main_colour.';
  border-color: '.StyleManager::getInstance()->main_colour.' transparent '.StyleManager::getInstance()->main_colour.' transparent;
  animation: kdualring 1.2s linear infinite;
}  
@keyframes kdualring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
';
        $this->addCssText($css);
    }
            
}