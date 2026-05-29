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
class QueryField
{
    private string $field_name="";
    private mixed $field_value="";
    private string $field_type_search="";
    private bool $field_case_sensitive=false;
    private bool $field_accent_sensitive=true;
    private string $table_name="";
    public static string $EQUAL="=";
    public static string $NOT_EQUAL="!=";
    public static string $SUPERIOR=">";
    public static string $SUPERIOR_OR_EQUAL=">=";
    public static string $INFERIOR="<";
    public static string $INFERIOR_OR_EQUAL="<=";
    public static string $NOT_LIKE="NOT LIKE";
    public static string $LIKE_1="LIKE_1"; // '%---%'
    public static string $LIKE_2="LIKE_2"; // '%----'
    public static string $LIKE_3="LIKE_3"; // '----%'
    public static string $WHOLE_WORD="WHOLE_WORD";
    public static string $IN_QUERY="IN"; // '%---%'
    public static string $NOT_LIKE_2="NOT LIKE_2"; // TODO : À implémenter '%----'
    public static string $NOT_LIKE_3="NOT LIKE_3"; // '----%'
    public static string $GEOM_INTERSECT_WKT="geom_intersect_wkt";
    public static string $GEOM_CONTAINS_WKT="geom_contains_wkt";
    public static string $GEOM_IS_CONTAINED_WKT="geom_is_contained_wkt";
    public static string $BOOL_TRUE="true";
    public static string $BOOL_FALSE="false";
    private static ?QueryField $instance=null;

    public function __construct(string $field_name="",mixed $field_value="",string $field_type_search="",bool $field_case_sensitive=false,bool $field_accent_sensitive=false)
    {
        $this->init($field_name,$field_value,$field_type_search,$field_case_sensitive,$field_accent_sensitive);
    }

    public function init(string $field_name="",mixed $field_value="",string $field_type_search="",bool $field_case_sensitive=false,bool $field_accent_sensitive=false) : void
    {
        $this->field_name=$field_name;
        $this->field_value=$field_value;
        $this->field_case_sensitive=$field_case_sensitive;
        $this->field_accent_sensitive=$field_accent_sensitive;
        if($field_type_search=="")
        {
            $this->field_type_search=QueryField::$EQUAL;
        }
        else
        {
            $this->field_type_search=$field_type_search;
        }
        if(is_null($field_value))
        {
            $this->field_case_sensitive=false;
            $this->field_accent_sensitive=false;            
        }
    }

    public function __destruct()
    {
        
    }

    /**
     * 
     * @param string $field_name
     * @param mixed $field_value
     * @param string $field_type_search
     * @param bool $field_case_sensitive
     * @param bool $field_accent_sensitive
     * @return QueryField
     */
    public static function m(string $field_name="",mixed $field_value="",string $field_type_search="",bool $field_case_sensitive=false,bool $field_accent_sensitive=false): QueryField
    {
        if(is_null(self::$instance))
        {
            self::$instance=new self();
        }
        $instance=clone self::$instance;
        $instance->init($field_name,$field_value,$field_type_search,$field_case_sensitive,$field_accent_sensitive);
        return $instance;
    }

    public function getField_name() : string
    {
        return $this->field_name;
    }

    public function getField_type_search() : string
    {
        return $this->field_type_search;
    }

    public function setField_type_search(string $field_type_search) : void
    {
        $this->field_type_search=$field_type_search;
    }

    public function setField_name(string $field_name) : void
    {
        $this->field_name=$field_name;
    }

    public function getField_value() : mixed
    {
        return $this->field_value;
    }
    
    public function getField_value_asString() : string
    {
        return $this->field_value."";
    }    

    public function setField_value(mixed $field_value) : void
    {
        $this->field_value=$field_value;
    }

    public function getField_case_sensitive() : bool
    {
        return $this->field_case_sensitive;
    }

    public function setField_case_sensitive(bool $field_case_sensitive) : void
    {
        $this->field_case_sensitive=$field_case_sensitive;
    }

    public function getField_accent_sensitive() : bool
    {
        return $this->field_accent_sensitive;
    }

    public function setField_accent_sensitive(bool $field_accent_sensitive) : void
    {
        $this->field_accent_sensitive=$field_accent_sensitive;
    }

    public function getTable_name() : string
    {
        return $this->table_name;
    }

    public function setTable_name(string $table_name) : self
    {
        $this->table_name=$table_name;
        return $this;
    }

    public function onForeignKey(string $table_name) : self
    {
        $this->table_name=$table_name;
        return $this;
    }

    public static function getBoolValue(bool $bool): string
    {
        if($bool)
        {
            return self::$BOOL_TRUE;
        }
        else
        {
            return self::$BOOL_FALSE;
        }
    }

    public function toString(string $delimitor=" | "): string
    {
        return "field_name: ".$this->field_name.$delimitor
                ." field_value: ".$this->field_value.$delimitor
                ." field_type_search".$this->field_type_search.$delimitor
                ." field_case_sensitive".$this->field_case_sensitive.$delimitor
                ." field_accent_sensitive".$this->field_accent_sensitive.$delimitor
                ." table_name".$this->table_name.$delimitor.$delimitor;
    }
}