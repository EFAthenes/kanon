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
/**
 * Description of PrivilegesUtilsKComponent
 *
 * @author Hippolyte
 */
abstract class PrivilegesUtilsKComponent extends FormComponent
{
    /**
     * Tableau regroupant les champs à faire apparaître en readonly
     */
    /**
     * 
     * @var array<int,string>|null
     */
    protected ?array $fieldsReadOnly=null;
    
    /**
     * Crée une div avec un input dedans.
     * @param string $idName
     * @param string $name_input
     * @param mixed $field_value
     * @param string $field_label
     * @param bool $readonly
     * @param int $colPos
     * @param int $colLength
     * @return KComponent
     */
    protected function makeInput(string $idName,string $name_input,mixed $field_value,string $field_label,bool $readonly=false, int $colPos=3, int $colLength=6): KComponent
    {
        $comp=(new InputStringComponent($field_value,$name_input,$field_label,"",false,$readonly,$colPos,$colLength))->setAutocomplete(false);
        return (new DivIdComponent($idName))->addComponent($comp);
    }

    /**
     * Crée une div avec un textArea (pour texte long) dedans.
     * @param string $idName
     * @param string $name_input
     * @param string|null $field_value
     * @param string $field_label
     * @param bool $readonly
     * @param int $colPos
     * @param int $colLength
     * @return KComponent
     */
    protected function makeTextArea(string $idName,string $name_input,?string $field_value,string $field_label,bool $readonly=false, int $colPos=3, int $colLength=6): KComponent
    {
        $comp=(new TextAreaComponent($field_value,$name_input,$field_label,"",false,$readonly,$colPos,$colLength))->setAutocomplete(false);
        return (new DivIdComponent($idName))->addComponent($comp);
    }

    /**
     * Crée une div avec un select dedans.
     * @param string $idName
     * @param string $name
     * @param array<mixed,mixed> $array
     * @param string $label
     * @param bool|null $allowedNoValue
     * @param mixed $selected
     * @param int $colPos
     * @param int $colLength
     * @return KComponent
     */
    protected function makeSelect(string $idName,string $name,array $array,string $label,?bool $allowedNoValue=false,mixed $selected=null, int $colPos=3, int $colLength=6): KComponent
    {
        //KDebugger::getInstance()->dump($selected,"makeSelect==>".$idName);
        $comp=(new KSelectComponent($name,$array,[$selected],false,$label,false,$colPos,$colLength))->setAllowNoValue($allowedNoValue);
        return (new DivIdComponent($idName))->addComponent($comp);
    }

    /**
     * Crée une div avec un sélecteur de date dedans.
     * @param string $idName
     * @param string $label
     * @param string $nom
     * @param string|null $value
     * @param int $colPos
     * @param int $colLength
     * @return KComponent
     */
    protected function makeDate(string $idName,string $label,string $nom,?string $value, int $colPos=3, int $colLength=6): KComponent
    {
        $comp=(new KInputDate($value,$label,$nom,$nom,false,false,$colPos,$colLength))->setCanBeNull(true);
        return (new DivIdComponent($idName))->addComponent($comp);
    }
}