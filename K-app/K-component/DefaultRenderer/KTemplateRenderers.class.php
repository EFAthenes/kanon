<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2024 EFA, Ecole française d'athènes, EFAthenes.
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
class KTemplateRenderers extends KSingleton
{
    public const string PHP_RENDERER="PHP_RENDERER";
    public const string TWIG_RENDERER="TWIG_RENDERER";
    #[\Override]
    public function init(): void
    {
        $this->put(self::PHP_RENDERER, new PurePHPRenderer());
//        if(ParamManager::getInstance()->get("TWIG_TEMPLATE"))
//        {
            $this->put(self::TWIG_RENDERER, new TwigRenderer());
//        }
    }
    
    public function getDefaultRenderer() : AbstractKRenderer
    {
        $renderer=$this->get(self::PHP_RENDERER);
        return $renderer;
    }
    
    /**
     * 
     * @param string $phpFile
     * @param array<mixed,mixed>|null $data
     * @return string
     */
    public static function renderPHP(string $phpFile,?array $data) : string
    {
        $instance=self::getInstance();
        $renderer=$instance->getDefaultRenderer();
        return "".$renderer->renderFile($phpFile,$data);
    }
    
    /**
     * 
     * @param string $phpFile
     * @param array<mixed,mixed>|null $data
     * @return string
     */    
    public static function renderTwig(string $phpFile,?array $data) : string
    {
        
        $instance=self::getInstance();
        $renderer=$instance->get(self::TWIG_RENDERER);
        if(!is_null($renderer))
        {
            //KDebugger::_($phpFile, self::TWIG_RENDERER);
            return "".$renderer->renderFile($phpFile,$data);
        }
        else
        {
            return $instance->renderPHP($phpFile, $data);
        }        
    }    
    
    /**
     * 
     * @param string $phpFile
     * @param array<mixed,mixed>|null $data
     * @return string
     */
    public static function render(string $phpFile,?array $data) : string
    {
        $instance=self::getInstance();
        $renderer=$instance->getDefaultRenderer();
        return "".$renderer->renderFile($phpFile,$data);
    }
}