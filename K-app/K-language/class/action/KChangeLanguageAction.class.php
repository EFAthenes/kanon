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
class KChangeLanguageAction extends KAction
{
    public const string GET_LANG="lang";
    
    public const string CODE_OK="1";
    public const string CODE_WRONG_INIT="2";
    public const string CODE_LANG_NOT_SUPPORTED="3";
    public const string CODE_GET_LANG_NOT_PRESENT="4";
    
    private string $code_error="";
    
    
    public function execute(): bool
    {
        $lang="";
        if(KInput::checkInputGet(self::GET_LANG, KInput::$VARIABLE_STRING, $lang))
        {
            if(LanguageManager::getInstance()->isLanguageSupported($lang))
            {
                if(LanguageManager::getInstance()->initLanguage($lang))
                {
                    $this->code_error=self::CODE_OK;
                    //echo LanguageManager::getInstance()->toString();
                    return true;
                }
                else
                {
                    $this->code_error=self::CODE_WRONG_INIT;
                }
            }
            else
            {
                $this->code_error=self::CODE_LANG_NOT_SUPPORTED;
            }
        }
        else
        {
            $this->code_error=self::CODE_GET_LANG_NOT_PRESENT;
        } 
        return false;
    }    
    
    public function getCodeError() : string
    {
        return $this->code_error;
    }
}

    