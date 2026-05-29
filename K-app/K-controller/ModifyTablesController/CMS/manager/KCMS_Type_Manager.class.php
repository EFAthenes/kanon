<?php
declare(strict_types=1);
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
class KCMS_Type_Manager extends HashMapSingleton
{
    public const LAYOUT=1;
    public const LAYOUT_ITEM=2;
    public const LAYOUT_VIEW=3;
    public const BLOCK=4;
    public const MENU=5;
    public const MENU_ITEM=6;
    public const CONTENT=7;

    public const UNKNOWN_ITEM=-1;
    
    public function init() : void
    {
        if(!class_exists('Kapp_Layout'))
        {
            return;
        }        
        
        $this->put(self::LAYOUT,
                new KCMS_Type_Element(
                        self::LAYOUT,
                        "Layout",
                        "Layout",
                        Kapp_Layout::$TABLE_NAME,
                        "fa-solid fa-paint-roller",
                        1
                        ));
        $this->put(self::LAYOUT_ITEM,
                new KCMS_Type_Element(
                        self::LAYOUT_ITEM,
                        "Layout Item",
                        "Layout Item",
                        Kapp_Layout_Item::$TABLE_NAME,
                        "fa-solid fa-table-cells-large",
                        1
                        ));
        
        $this->put(self::LAYOUT_VIEW,
                new KCMS_Type_Element(
                        self::LAYOUT_VIEW,
                        "Layout view",
                        "Layout view",
                        Kapp_Layout_View::$TABLE_NAME,
                        "fa-regular fa-eye",
                        1
                        ));
        
        $this->put(self::BLOCK,
                new KCMS_Type_Element(
                        self::BLOCK,
                        "Block",
                        "block",
                        Kapp_Layout_Item::$TABLE_NAME,
                        "fa-solid fa-cubes",
                        3
                        ));
        
        $this->put(self::MENU,
                new KCMS_Type_Element(
                        self::MENU,
                        "Menu",
                        "Menu",
                        Kapp_Layout_Item::$TABLE_NAME,
                        "fa-solid fa-bars",
                        4
                        )); //<i class=""></i> <i class="fa-solid fa-minus"></i>
        
        $this->put(self::MENU_ITEM,
                new KCMS_Type_Element(
                        self::MENU_ITEM,
                        "Menu",
                        "Menu Item",
                        Kapp_Layout_Item::$TABLE_NAME,
                        "fa-solid fa-minus",
                        4
                        ));  
        
        $this->put(self::CONTENT,
                new KCMS_Type_Element(
                        self::CONTENT,
                        "Content",
                        "Content",
                        Kapp_Layout_Item::$TABLE_NAME,
                        "fa-solid fa-wand-magic-sparkles",
                        5
                        )); 

        
    }
    
    public static function getItem(int $type) : KCMS_Type_Element
    {
        $item=self::getInstance()->get($type);
        if(!is_null($item))
        {
            return self::getInstance()->get($type);     
        }
        return self::returnUnknown();
    }

    public function getItemByTableName(string $tablename) : KCMS_Type_Element
    {
        /* @var $type KCMS_Type_Element */
        foreach ($this as $type)
        {
            if($tablename==$type->table)
            {
                return $type;
            }
        }       
        return self::returnUnknown();
    }
    
    public static function returnUnknown() : KCMS_Type_Element
    {     
        return new KCMS_Type_Element(self::UNKNOWN_ITEM,"unknown","unknown","","",99);
    }    
}