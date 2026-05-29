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
class HistoryPage extends SessionMemoryItem
{
    private static ?self $instance=null;
    private static string $name="HistoryPage";  
    private ArrayList $list;
    public static string $BACK_STRING="back_string";
    public static string $UPLOAD_STRING="jupart";
    private int $max_offset=20;
    public static string $STRING_SUBMITED_POST="string_submited_post";

    private function __construct()
    {        
        $this->list=new ArrayList();
    }
    public static function getInstance() : HistoryPage
    {
        if(HistoryPage::$instance == null )
        {
            HistoryPage::$instance = SessionMemory::getInstance()->get(HistoryPage::$name);
            if(HistoryPage::$instance == null )
            {
                HistoryPage::$instance=new HistoryPage();
                SessionMemory::getInstance()->put(HistoryPage::$name,HistoryPage::$instance);
            }
        }
        return HistoryPage::$instance;
    }
    public function  __destruct()
    {

    }
    public function update() : void
    {
        if($this->list->getSize()>($this->max_offset*2))
        {
            $this->list->removeFirstElements($this->max_offset);
        }

        if(isset($_GET[HistoryPage::$BACK_STRING])&& substr( $_GET[HistoryPage::$BACK_STRING], 0, 1) === "1")
        {           
            $this->list->remove($this->list->getSize()-1);
        }
        else if(!isset($_GET[HistoryPage::$UPLOAD_STRING]) && !isset($_GET[self::$STRING_SUBMITED_POST]))
        {
            $url=new KURL();
            if(!$this->list->isEmpty())
            {
                $url_last=$this->list->getLast();
                if($url_last == null || $url->printURL()!=$url_last->printURL())
                {
                    $this->list->add($url);
                }               
            }
            else
            {
                $this->list->add($url);
            }           
        }
    }
    
    public function printBack() : string
    {
       if($this->list->isIndexValid($this->list->getSize()-1))
       {
           $url=$this->list->get($this->list->getSize()-1);
           return $url->printAddReplaceWithoutAmp(HistoryPage::$BACK_STRING,"1");
       }
       return "";
    }
    public function printTwoBack() : string
    {
       if($this->list->isIndexValid($this->list->getSize()-2))
       {
           $url=$this->list->get($this->list->getSize()-2);
           return $url->printAddReplaceWithoutAmp(HistoryPage::$BACK_STRING,"1");
       }
       return "";
    }    
    
    public function printBackReference(string $string) : string
    {
        if($this->list->isIndexValid($this->list->getSize()-1))
        {
            /* @var $url KURL */
            $url=$this->list->get($this->list->getSize()-1);
            if($url!=NULL)
            {
                $url_back=new KURL($url->printURLWithoutAmp());
                return $url_back->printAddReplaceWithoutAmpNoEncode(HistoryPage::$BACK_STRING,"1#".$string);
            }
        }
        return "";
    }    
    public function deleteLast() : bool
    {
        return $this->list->remove($this->list->getSize()-1);
    }
    
    public function deleteLastIfContainsParams(string $string) : void
    {       
        if($this->list->isIndexValid($this->list->getSize()-2))
        {
            $url=$this->list->get($this->list->getSize()-2);  
            if($this->list->isIndexValid($this->list->getSize()-1))
            {
                $url_Last=$this->list->get($this->list->getSize()-1);       
                if($url!=null&&$url_Last!=null)
                {
                    $arg=$url->getArgValue($string);
                    $arg_last=$url_Last->getArgValue($string);
                    if($arg!=null&&$arg_last!=null)
                    {
                        $this->deleteLast();
                    }
                }
            }
        }
    }      
    public function getLast() : string
    {
        if($this->list->isIndexValid($this->list->getSize()-1))
        {
            $url=$this->list->get($this->list->getSize()-1);
            if($url!=null)
            {
                return $url->printURL();
            }
        }
        return "";
    }   
    
    public function getLastKUrl() : ?KURL
    {
        if($this->list->isIndexValid($this->list->getSize()-1))
        {
            $url=$this->list->get($this->list->getSize()-1);
            if($url!=null)
            {
                return $url;
            }
        }
        return null;
    } 
    
    public function replaceArgInLast(string $name,string $value) : bool
    {
        if($this->list->isIndexValid($this->list->getSize()-1))
        {
            /* @var $url KURL */
            $url=$this->list->get($this->list->getSize()-1);
            if($url!=null)
            {
                $url->addOrReplace($name, $value);
                return true;
            }
        }
        return false;        
    }
    
    public function getLastDifferent() : ?KURL
    {
        $url=new KURL();        
        $url->removeArg(HistoryPage::$STRING_SUBMITED_POST);
        for($i=$this->list->getSize()-1; $i>=0 ; $i--)
        {
            $url_last=$this->list->get($i);
            if ($url_last != null && $url->printURL() != $url_last->printURL())
            {
                return $url_last;
            }
        }
        return null;
    }      
    public function reset() : void
    {
        $this->list->clear();
    }
    public function toString(string $delimitor = "<br />") : string 
    {
        $string=" Size ==> ".$this->list->getSize().$delimitor;
        for($i=0; $i<$this->list->getSize(); $i++)
        {
            $string.=$i." // ".$this->list->get($i)->printURL().$delimitor;
        }
        return $string;
    }
}
