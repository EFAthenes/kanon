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
class KMinify
{
    private bool $DEBUG=false;
    public function __construct()
    {
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/path-converter/src/ConverterInterface.php';
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/path-converter/src/Converter.php';
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/path-converter/src/NoConverter.php';
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/minify/src/Minify.php';     
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/minify/src/CSS.php';
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/minify/src/JS.php';
        require_once __ROOT__.'/K-composer/vendor/matthiasmullie/minify/src/Exception.php';
    }
    
    public function minifyCssString(string $string) : string
    {
        if($this->DEBUG)
        {
            return $string;
        }
        $minifier = new MatthiasMullie\Minify\CSS();
        $minifier->add($string);
        return $minifier->minify();
    }
    public function minifyJsString(string $string) : string
    {
        if($this->DEBUG)
        {
            return $string;
        }        
        $minifier = new MatthiasMullie\Minify\JS();
        $minifier->add($string);
        return $minifier->minify();        
    }   
}
        