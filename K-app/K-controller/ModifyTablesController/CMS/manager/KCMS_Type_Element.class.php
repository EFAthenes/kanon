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
class KCMS_Type_Element
{
    public string $title="";
    public string $label="";
    public int $id=-1;
    public string $table="";
    public string $icon="";
    public int $level=1;
    public function __construct(
            int $id=-1,
            string $title="",
            string $label="",
            string $table="",
            string $icon="",
            int $level=1
            )
    {
        $this->title=$title;
        $this->label=$label;
        $this->id=$id;
        $this->table=$table;
        $this->icon=$icon;
        $this->level=$level;
    }
}