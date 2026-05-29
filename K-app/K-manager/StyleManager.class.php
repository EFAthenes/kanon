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
class StyleManager extends HashMap
{
    private static ?self $instance=null;
    
    public string  $main_colour="#0082AC";
    public string  $colour_2="#6699ff"; 
    public string  $colour_3="#80aaff";
    public string  $colour_4="#80aaff";
    
    public string  $font_colour_1="#FFFFFF";
    public string  $font_colour_2="#000000";
    public string  $font_colour_3="#000000";
    
    public string  $link_colour_1="#0082AC";
    public string  $link_colour_1_hover="#6699ff";
    public string  $link_colour_2="#FFFFFF";
    public string  $link_colour_2_hover="#80aaff";
    public string  $link_colour_3="#FFFFFF";
    public string  $link_colour_3_hover="#80aaff";  
    
    private bool $activated=false;
    
    private function __construct()
    {
        parent::__construct();
        $this->init();
    }
    
   
    public static function getInstance() : StyleManager
    {
        if(is_null(self::$instance))
        {
            self::$instance=new StyleManager();
        }
        return self::$instance;
    } 
    
    public function isActivated() : bool
    {
        return $this->activated;
    }
    
    private function init() : void
    {
        $this->activated=true;
        if(!ParamManager::getInstance()->debug)
        {
            
        }
        else
        {
            
        }
    }
}