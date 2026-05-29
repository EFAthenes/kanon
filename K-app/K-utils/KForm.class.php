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
class KForm
{
    public static string $IDENTIFIER="check";
    public static string $NEW="new";
    public static string $COPY="copy";
    public static int $MAX_POST_ITEM=100;
    private function __construct()
    {
    }

    public static function checkFormSubmit() : bool
    {
        if(isset($_GET[self::$IDENTIFIER])&&$_GET[self::$IDENTIFIER]=="1" && isset($_POST[self::$IDENTIFIER])&&$_POST[self::$IDENTIFIER]=="1" )
        {
            return true;
        }
        return false;
    }
}
