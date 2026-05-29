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
class KCaptchaComponent extends KComponent
{
    final public function __construct()
    {
        parent::__construct();
        $this->setNone();
        $comp=new HTMLComponent($this->makeHtml());
        $this->addComponent($comp);
    }

    function makeHtml() : string
    {
        require_once __ROOT__.'/K-lib/K-captcha/KCaptcha.class.php';  
        /** @phpstan-ignore-next-line */
        $captcha= new KCaptcha();
        /** @phpstan-ignore-next-line */
        $captcha->setPassPhraseInSession();
        /** @phpstan-ignore-next-line */
        $print='<img src="'.$captcha->makeImageBase64().'">
';
        return $print;
    }
    public static function getLastPassPhraseInSession() : string
    {
        return ''.SessionMemory::getInstance()->get("KCaptcha");
    } 
    
        #[\Override]
    public static function testMe() : ?static
    {
        //KDebugger::getInstance()->dump("louis");
        $class=new static();
        return $class;
    }    
}