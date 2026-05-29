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
class KPurifier
{
    private static ?KPurifier $instance = null;
    /**
     * 
     * @var array<string,HTMLPurifier>
     */
    private array $purifiers=[];
    private const DEFAULT="default";
    //private bool $DEBUG=false;
    private function __construct()
    {
        require_once __ROOT__.'/K-composer/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
        $config = \HTMLPurifier_Config::createDefault();
        $config->set("Cache.SerializerPath", KApp::getInstance()->getCacheFolder());
        //$config->set("HTML.AllowedElements",["a","p"]);        
        $this->purifiers[self::DEFAULT] = new HTMLPurifier($config);
    }
    
    public static function getInstance(): self
    {
        if(self::$instance==null)
        {
            self::$instance=new self();
        }
        return self::$instance;
    } 
    
    private function makePurify(string $s,string $accepted_tags) : string
    {
        if($accepted_tags==self::DEFAULT)
        {
            return $this->purifiers[self::DEFAULT]->purify($s);
        }
        else
        {
            if(!array_key_exists($accepted_tags,$this->purifiers))
            {
                $config = \HTMLPurifier_Config::createDefault();
                $config->set("Cache.SerializerPath", KApp::getInstance()->getCacheFolder());
                $config->set("HTML.AllowedElements",$accepted_tags);        
                $this->purifiers[$accepted_tags] = new HTMLPurifier($config);                
            }
            return $this->purifiers[$accepted_tags]->purify($s);
        }
    }
    
    /**
     * 
     * @param string $s
     * @param array<int,string> $acceptedTags
     * @return string
     */
    private function purifyHtml(string $s,array $acceptedTags=[]) : string
    {
        $accepted_tags=self::DEFAULT;
        if(count($acceptedTags))
        {
            $accepted_tags=implode(",", $acceptedTags);
        }
        return $this->makePurify($s,$accepted_tags);
    }
    /**
     * 
     * @param mixed $dirty_html
     * @param array<int,string> $acceptedTags
     * @return string
     */    
    public static function purify(mixed $dirty_html,array $acceptedTags=[]) : string
    {
        return KPurifier::getInstance()->purifyHtml("".$dirty_html,$acceptedTags);
    }
}
        