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
abstract class KLayout
{   
    public static string $CENTER="center";
//    /* @var $comments string */
//    protected $comments;
    
    /* @var $printOnlyComponents bool */
    protected bool $printOnlyComponents=false;    

    /* @var $map HashMap */
    private HashMap $map;
    
    /* @var $page KWebPage */
    private KWebPage $page;
    
    /* @var $main KComponent */
    private ?KComponent $main=null;

    protected function __construct(string $title="KLayout")
    {
        $this->map=new HashMap();
        $this->page=new KWebPage($title,$this->map);
    }
    
    public function initializeLayout() : void
    {
        
    }
    
    abstract public function initialize() : void;
    abstract public function terminate() : void;
   
    public function drawAll() : void
    {       
        if($this->printOnlyComponents)
        {
            $this->drawComponents();
        }
        else
        {
            $components=$this->drawComponentsToString();        
            $body=$this->drawBodyToString();
            $end=$this->drawEndOfPAgeToString();            
            echo $body;
            echo $components;
            echo $end;
        }
    }    
    
    protected function drawComponents() : void
    {  
        //echo "KLayout::drawComponents  Start <br />";
        //echo $this->map->toString();
        /* @var $component KComponent */
        foreach($this->map as $component)
        {
            echo $component->draw();
        }   
    }   
    private function drawComponentsToString() : string
    {
        $html="";
        /* @var $component KComponent */
        foreach($this->map as $component)
        {
            $html.=$component->draw();
        }  
        return $html;
    }    

    public function  __destruct()
    {

    }

    public function drawBody() : void
    {
        echo $this->page->startDraw();
        echo $this->page->endDraw();
        echo $this->page->setBody();
    }
    
    public function drawBodyToString() :string
    {
        return $this->page->startDraw().$this->page->endDraw().$this->page->setBody();
    }    

    public function drawEndOfPage() : void
    {
        echo $this->page->stopDraw();
    }
    public function drawEndOfPAgeToString() : string
    {
        return $this->page->stopDraw();
    }    

    public function addMeta(string $meta) : void
    {
        $this->page->addMeta($meta);
    }

    //------------------

    public function setMetaLanguage(mixed $meta) : void
    {
        $this->page->setMetaLanguage($meta);
    }
    
    public function setMetaCopyright(mixed $meta) : void
    {
        $this->page->setMetaCopyright($meta);
    }  
    
    public function setMetaGeography(mixed $meta) : void
    {
        $this->page->setMetaGeography($meta);
    } 
    
    public function setMetaAuthor(mixed $meta) : void
    {
        $this->page->setMetaAuthor($meta);
    }

    public function setMetaKeywords(mixed $meta) : void
    {
        $this->page->setMetaKeywords($meta);
    }

    public function setMetaDescription(mixed $meta) : void
    {
        $this->page->setMetaDescription($meta);
    }
    public function addMetaDescription(mixed $meta) : void
    {
        $this->page->addMetaDescription($meta);
    }      

    public function setMetaAbstract(mixed $meta) : void
    {
        $this->page->setMetaAbstract($meta);
    }
    
    public function addMetaProperty(string $metaProperty, string $metaContent) : void
    {
        $this->page->addMetaProperty($metaProperty,$metaContent);
    }  
    
    public function setTitle(mixed $title) : void
    {
        $this->page->setTitle($title);
    }
    
    //------------------
    public function addScriptText(string $newScript) : void
    {
        $this->page->addJS($newScript);
    }

    public function addCSSText(string $text) : void
    {
        $this->page->addCSSText($text);
    }

    public function addCSSFile(string $text) : void
    {
        $this->page->addOneCSSFile($text);
    }

    public function addCSSFileToBuffer(string $path) : void
    {
        $this->page->addCSSFileToBuffer($path);
    }    

    public function addJsFile(string $file, bool $endOfFile=false) : void
    {
        $this->page->addOneScriptFile($file,$endOfFile);
    }
    
    public function addJsFileToBuffer(string $path,bool $end=false) : void
    {
        $this->page->addJsFileToBuffer($path,$end);
    }
    
    public function addJsFileAppToBuffer(string $path) : void
    {
        $this->page->addJSFileAppToBuffer($path);
    }    
    
    public function addJsFileModuleToBuffer(string $path,bool $end=false) : void
    {
        $this->page->addJSFileModuleToBuffer($path,$end);
    }    
    
    public function addScriptImportMap(string $jsCode) : void
    {
        $this->page->addScriptImportMap($jsCode);
    }
       
    public function addJsAtStartUp(string $js) : void
    {
        $this->page->addJsAtStartUp($js);
    }
    public function addJsText(string $js) : void
    {
        $this->page->addJsText($js);
    }
    public function addScript(string $js) : void
    {
        $this->page->addScript($js);
    }    
/*
    public function printComments() : void
    {
        print(($this->comments));
    }
*/    
    protected function addLayoutItem(string $value,KComponent $component) : void
    {
        $this->map->putOrReplace($value,$component); 
        //echo $this->map->toString();
    }
    
    public function addLayoutItemToMain(KComponent $component) : bool
    {
        /* @var $this->main KComponent */
        if(!is_null($this->main))
        {
            $this->main->addComponent($component);
            return true;
        }
        return false;
    }    
    
    public function resetLayout() : void
    {
        $this->map->clear();
    }

    
    public function setComponentAsMain(string $value) : bool
    {
        $object=$this->map->get($value);
        if($object!=null && $object instanceof KComponent)
        {
            $this->main=$object;
            return true;
        }
        return false;
    }    

    public function addComponent(string $value,KComponent $component) : bool
    {
        $object=$this->map->get($value);
        if($object!=null && $object instanceof KComponent)
        {
            $object->addComponent($component);
            return true;
        }
        return false;
    }
    public function addHtml(string $value,string $html) : bool
    {   
        $object=$this->map->get($value);
        if($object!=null)
        {
            $component= new HTMLComponent($html);
            $object->addComponent($component);
            return true;
        }
        return false;
    }    

    public function addComment(string $texts) : void
    {
        $this->page->addComment($texts);
    }

    public function drawAllToString() : string
    {
        return $this->drawBodyToString().$this->drawComponentsToString().$this->drawEndOfPAgeToString();        
    }

    public function getTitle(): string
    {
        return $this->page->getTitle();
    }

    public function setDefaultsMetas() : void
    {
        $this->setTitle("École Française d'Athènes EFA");
        $this->setMetaAbstract("École Française d'Athènes EFA");
        $this->setMetaAuthor("École Française d'Athènes EFA");
        $this->setMetaDescription("École Française d'Athènes EFA");
        $this->setMetaKeywords("École Française d'Athènes EFA ");
        $this->setMetaLanguage("fr");
        $this->setMetaCopyright("Tous droits réservés École Française d'Athènes 2018");        
    }
    
    public function setJsToTheEndOfPage() : void
    {
        $this->page->setJsToTheEndOfPage();
    }
    
    public function resetClassToBody() : void
    {
        $this->page->resetClassToBody();
    }    
    
    public function addClassToBody(string $classname) : void
    {
        $this->page->addClassToBody($classname);
    }
    function getPrintOnlyComponents() : bool
    {
        return $this->printOnlyComponents;
    }

    function setPrintOnlyComponents(bool $printOnlyComponents) : void
    {
        $this->printOnlyComponents=$printOnlyComponents;
    }  
    
    function setFavIco(string $url_favico) : void
    {
        $this->page->setFavIco($url_favico);
    }
    function setLangTag(string $lang_tag) : void
    {
        $this->page->setLangTag($lang_tag);
    }
    
    function setHtmlTag(string $html_tag) : void
    {
        $this->page->setHtmlTag($html_tag);
    }    

    protected function getMap() : HashMap
    {
        return $this->map;
    }

    protected function setMap(HashMap $map) : void
    {
        $this->map=$map;
    }
    
    public function includeJsFiles() : void
    {
        
    }
    
    public function includeCssFiles() : void
    {
        
    } 
    
    protected function includeAllFiles() : void
    {
        $this->includeJsFiles();
        $this->includeCssFiles();
    }
}