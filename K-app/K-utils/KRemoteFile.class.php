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
class KRemoteFile
{
    private string $url="";
    /**
     * 
     * @var array<int,string>
     */
    private array $headers=[];
    public function __construct(string $url="")
    {
        $this->url=$url;
    }      

    public function changeURL(string $url) : void
    {
        $this->url=$url;
        $this->headers=[];
    }
    /**
     * 
     * @param bool $reload
     * @return array<int,string>
     */
    public function getHeaders(bool $reload=false) : array
    {
        if($reload || count($this->headers)==0)
        {
            $headers = @get_headers($this->url);
            if(is_array($headers))
            {
                $this->headers=$headers;
            }
            else
            {
                $this->headers=[];
            }
        }
        return $this->headers;
    }
    
    public function getURLHeadersString(string $separator="<br />") : string
    {
        $s="URL = ".$this->url.$separator.$this->getHeadersString($separator);
        return $s;
    }    
    
    public function getHeadersString(string $separator="<br />") : string
    {
        $s="";
        foreach ($this->headers as $header)
        {
            $s.=$header.$separator;
        }
        return $s;
    }
    
    public function getContent() : ?string
    {
        $this->headers = $this->getHeaders();
        //KDebugger::getInstance()->dump($this->headers);
        if(count($this->headers)>0 && strpos( $this->headers[0], '200')) 
        {
             $content = file_get_contents($this->url);
             if($content!==false)
             {
                return $content;
             }
        }
        return null;
    }
}