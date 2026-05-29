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
class KWebPage extends KComponent
{
    protected string $title="";
    protected string  $meta="";
//    protected $cssText="";
    protected string $cssFile = "";
    protected string $script = "";
    protected string $scriptFile = "";
    protected string $scriptImportMap = "";
    protected string $scriptFileEndOfPage = "";
    protected ?HashMap $map = null;
    protected ?HashMap$mapCss = null;
    protected ?HashMap$mapJs = null;
    protected string $metaAuthor = "";
    protected string $metaKeywords = "";
    protected string $metaDescription = "";
    protected string $metaAbstract = "";
    protected string $metaCopyright= "";
    protected string $metaLanguage= "";
    protected string $metaGeography= "";
    protected string $charset = "UTF-8";
    protected string $scriptStartUp="";
    /**
     * 
     * @var array<int,KMetaProperty>
     */
    protected array $metaProperty=array();
    protected bool $jsToTheEndOfPage=false;
    /**
     * 
     * @var array<int,string>
     */
    protected array $classToBody=array();
    protected ?string $favico=null;
    protected string $lang_tag="";
    protected string $html_tag="";
    protected ?HashMap $cssBuffer=null;
    protected ?HashMap $jsBuffer=null;
    protected ?HashMap $jsBufferEnd=null;
    protected ?HashMap $jsModuleBuffer=null;
    protected ?HashMap $jsModuleBufferEnd=null;  
    protected ?HashMap $jsAppBuffer=null;     
       
    public const CSS_BUFFER_PREFIX="CSS_CACHE_";
    public const JS_BUFFER_PREFIX="JS_CACHE_";
    public const FONT_BUFFER_PREFIX="FONT_CACHE_";

    //  ISO-8859-1  UTF-8

    public function __construct(string $nameTitle,HashMap $map) 
    {
        parent::__construct();
        $this->title = $nameTitle;
        $this->mapCss = new HashMap();
        $this->mapJs = new HashMap();
        $this->map = $map;
    }

    public function __destruct() 
    {
        
    }
    
    public function resetClassToBody() : void
    { 
        $this->classToBody=[];
    }
    
    public function addClassToBody(string $classname) :void
    { 
        $this->classToBody[]=$classname;
    }
    
    public function setBody() : string
    {
        $print='';
        if(count($this->classToBody)>0)
        {
            $class="";
            foreach($this->classToBody as $classString)
            {
                $class=" ".$classString." ";
            }
            $print='
    <body class="'.$class.'">
';            
        }
        else
        {
            $print='
    <body>
';
        }
        return ($print);
    }

    public function stopDraw() : string
    {
        $print='';
        if($this->jsToTheEndOfPage)
        {
            $print.=$this->scriptFile;
        }
        if($this->scriptFileEndOfPage!="")
        {
            $print.=$this->scriptFileEndOfPage;
        }
        $print.=$this->processJSBufferEnd();
        $print.=$this->processJSModuleBufferEnd();
        $print.='
    </body>
</html>';
        return($print);
    }    
    
    public function setLangTag(string $tag) : void
    {
        $this->lang_tag=$tag;
    }
    
    private function getLangTag() : string
    {
        if($this->lang_tag!="")
        {
            return ' lang="'.$this->lang_tag.'"';
        }
        return "";
    }
    
    public function setHtmlTag(string $tag) : void
    {
        $this->html_tag=$tag;
    }
    
//    private function getHtmlTag() : string
//    {
//        if($this->html_tag!="")
//        {
//            return $this->html_tag;
//        }
//        return "";
//    }    
    
    //@Override
    public function draw() : string
    {
        $this->makeHeader();
        $print='<!DOCTYPE html>            
<html'.$this->getLangTag().' '.$this->html_tag.' >
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="content-type" content="text/html; charset='.htmlspecialchars($this->charset).'"/>
        <title>'.htmlspecialchars($this->title).'</title>';  
        
        if($this->metaAuthor=="")
        {
            $print.="
        <meta name=\"author\" content=\"K-Web\"/>";
        }
        else
        {
            $print.="
        <meta name=\"author\" content=\"".htmlspecialchars($this->metaAuthor)."\"/>";
        }

        if($this->metaDescription=="")
        {
            $print.="
        <meta name=\"description\" content=\"K-Web Framework\"/>";
        }
        else
        {
            $print.="
        <meta name=\"description\" content=\"".htmlspecialchars($this->metaDescription)."\"/>";
        }

        if($this->metaAbstract!="")
        {
            $print.="
        <meta name=\"abstract\" content=\"".htmlspecialchars($this->metaAbstract)."\"/>";
        }
        if($this->metaCopyright!="")
        {
            $print.="
        <meta name=\"copyright\" content=\"".htmlspecialchars($this->metaCopyright)."\"/>";
        }
        if($this->metaLanguage!="")
        {
            $print.="
        <meta name=\"language\" content=\"".htmlspecialchars($this->metaLanguage)."\"/>";
        } 
        if($this->metaGeography!="")
        {
            $print.="
        <meta name=\"language\" content=\"".htmlspecialchars($this->metaGeography)."\"/>";
        }        
        if($this->metaKeywords=="")
        {
            //echo "metaKeywords blank ";
            $print.="
        <meta name=\"keywords\" content=\"empty\"/>";
        }
        else
        {
            $print.="
        <meta name=\"keywords\" content=\"".htmlspecialchars($this->metaKeywords)."\"/>";
        }
        
        if($this->meta!="")
        {
            $print.="
        ".$this->meta."";            
        }
        
        foreach($this->metaProperty as $metaProperty)
        {
            /* @var $metaProperty KMetaProperty */
            $print.='
        <meta property="'.htmlspecialchars($metaProperty->metaProperty).'" content="'.htmlspecialchars($metaProperty->metaContent).'">';              
        }
        

        $print.=$this->processCSSBuffer(); 
        
        if($this->cssFile!="")
        {
            $print.= $this->cssFile;
        }
        
        if($this->css_code!="")
        {
            $print.='
        <!-- csstext -->
        <style type="text/css">'. $this->css_code.'
        </style>
        <!-- end csstext -->
';
        }     
        
        $print.=$this->printScriptImportMap();
                
        $print.=$this->processJSBufferStart();
        $print.=$this->processJSAppBufferStart();
        $print.=$this->processJSModuleBufferStart();

        if(!$this->jsToTheEndOfPage)
        {
            $print.=$this->scriptFile;
        }
        
//        $print.=$this->processJSBufferEnd();
        
        if($this->favico)
        {
            $print.= '
        <link rel="shortcut icon" type="image/x-icon" href="'.htmlspecialchars($this->favico).'"/>
        <link rel="apple-touch-icon" href="'.htmlspecialchars($this->favico).'"/>
        ';
        }
        
        if($this->script!="")
        {
        $print.='
    <!-- jstext -->
';
        $print.=$this->script;
        $print.='
    <!-- end jstext -->
';           
        }
        
        
        if($this->scriptStartUp!="")
        {
         $print.='
        <!-- jsStartup -->
        <script>
        $(document).ready(function() 
        {
';
        $print.=$this->scriptStartUp;
        $print.='
        });  
        </script>
        <!-- end jsStartup -->
';
        }        
        if($this->javascript_code!="")
        {
            $print.=$this->javascript_code;
        }
        
        if($this->comments!="")
        {
            $print.='
'.$this->comments;
        }
        return $print;
    }
    public function startDraw() : string
    {
        return $this->draw();
    }    
    public function endDraw() : string
    {
	$print='
    </head>
';
        return $print;
    }

    public function addMeta(string $meta) : void
    {
        $this->meta=$meta;
    }
    
    public function addMetaProperty(string $metaProperty, string $metaContent) : void
    {
        $this->metaProperty[]=new KMetaProperty($metaProperty,$metaContent);
    }    
    
    //----------------------------------

    public function setMetaAuthor(string $meta) : void
    {
        $this->metaAuthor=$meta;
    }

    public function setMetaKeywords(string $meta) : void
    {
        //echo "setMetaKeywords<br />";
        $this->metaKeywords=$meta;
    }

    public function setMetaDescription(string $meta) : void
    {
        $this->metaDescription=$meta;
    }
    public function addMetaDescription(string $meta) : void
    {
        $this->metaDescription.=$meta;
    }    

    public function setMetaAbstract(string $meta) : void
    {
        $this->metaAbstract=$meta;
    }
    
    public function setMetaCopyright(string $meta) : void
    {
        $this->metaCopyright=$meta;
    }
    
    public function setMetaGeography(string $meta) : void
    {
        $this->metaGeography=$meta;
    } 
    
    public function setMetaLanguage(string $meta) : void
    {
        $this->metaLanguage=$meta;
    }
    
    public function setTitle(string $title) : void
    {
        $this->title=$title;
    }
    
    public function getTitle() : string
    {
        return $this->title;
    }    

    //----------------------------------
//    public function addCSSText($text) : void
//    {
//        $this->cssText.=$text;
//    }

    public function addCSSFilesList(ArrayList $list) : void
    {
        if($list==null)
        {
            return;
        }

//        for($i=0; $i < $list->getSize() ; $i++)
//        {
//            $text=$list->get($i);
        foreach($list as $text)    
        {    
            $present=$this->mapCss->get($text);

            if($present==null)
            {
                $this->mapCss->put($text,$text);
                $this->cssFile.='
        <link rel="stylesheet" type="text/css" href="'.$text.'"/>';
            }           
        }
    }

    public function addOneCSSFile(string $file) : void
    {
        if(empty($file))
        {
            return;
        }

        $present=$this->mapCss->get($file);

        if($present==null)
        {
            $this->mapCss->put($file,$file);
            $this->cssFile.='
        <link rel="stylesheet" type="text/css" href="'.$file.'"/>';
        }       
    }

    public function addScript(string $text) : void
    {
        $this->script.=$text;
    }
    
    public function addScriptImportMap(string $text) : void
    {
        $this->scriptImportMap.=$text;
    }  
    
    public function printScriptImportMap() : string
    {
        $importMap="";
        if(!empty($this->scriptImportMap))
        {
        $importMap.=' 
 <script type="importmap">
{
    '.$this->scriptImportMap.'
}
</script>
';
        }
        return $importMap;  
    }

    public function addOneScriptFile(string $file,bool $endOfFile) : void
    {
        if($file==null)
        {
            return;
        }

        $present=$this->mapJs->get($file);

        if($present==null)
        {
            $this->mapJs->put($file,$file);
            if(!$endOfFile)
            {
            $this->scriptFile.='
        <script src="'.$file.'"></script>';
            }
            else
            {
            $this->scriptFileEndOfPage.='
        <script src="'.$file.'"></script>';                
            }
        }
    }

    public function addScriptFiles(ArrayList $list) : void
    {
        if($list==null)
        {
            return;
        }

//        for($i=0; $i < $list->getSize() ; $i++)
//        {
//            $text=$list->get($i);
        foreach($list as $text)    
        {
            $present=$this->mapJs->get($text);

            if($present==null)
            {
                $this->mapJs->put($text,$text);
                $this->scriptFile.='
        <script src="'.$text.'"></script>';
            }
        }
    }
    public function addJsAtStartUp(string $js) : void
    {
        $this->scriptStartUp.=$js;
    }  
    private function makeHeader() : void
    {
        $list=$this->map->toArrayList();
        foreach($list as $component)
        {
            /* @var $component KComponent */
            $this->addScriptFiles($component->getJsFile());
            $this->addJS($component->getJS());
            $this->addCSSFilesList($component->getCssFile());
            $this->addCSSText($component->getCss());
        }
    }
    
    public function setJsToTheEndOfPage() : void
    {
        $this->jsToTheEndOfPage=true;
    }
    
    public function setFavIco(string $url) : void
    {
        $this->favico="".$url;
    }
    
    public function addJSFileToBuffer(string $path,bool $end=false) : void
    {
        if(!$end)
        {
            if(is_null($this->jsBuffer))
            {
                $this->jsBuffer=new HashMap();
            }
            $this->jsBuffer->put($path,$path);
        }
        else
        {
            if(is_null($this->jsBufferEnd))
            {
                $this->jsBufferEnd=new HashMap();
            }    
            $this->jsBufferEnd->put($path,$path);            
        }
    }
    
    public function addJSFileModuleToBuffer(string $path,bool $end=false) : void
    {
        if(!$end)
        {
            if(is_null($this->jsModuleBuffer))
            {
                $this->jsModuleBuffer=new HashMap();
            }
            $jsModule=new HashMap();
            $jsModule->put($path, $path);            
            $this->jsModuleBuffer->put($path,$jsModule);
        }
        else
        {
            if(is_null($this->jsModuleBufferEnd))
            {
                $this->jsModuleBufferEnd=new HashMap();
            }    
            $jsModule=new HashMap();
            $jsModule->put($path, $path);
            $this->jsModuleBufferEnd->put($path,$jsModule);            
        }
    }  
    
    public function addJSFileAppToBuffer(string $path) : void
    {
        if(is_null($this->jsAppBuffer))
        {
            $this->jsAppBuffer=new HashMap();
        }
        $jsApp=new HashMap();
        $jsApp->put($path, $path);            
        $this->jsAppBuffer->put($path,$jsApp);  
    }      
       
    public function addCSSFileToBuffer(string $path) : void
    {
        if(!empty($path))
        {
            if(is_null($this->cssBuffer))
            {
                $this->cssBuffer=new HashMap();
            }    
            $this->cssBuffer->put($path,$path);           
        }
    } 
    
    private function processCSSBuffer() : string
    {
        if(is_null($this->cssBuffer))
        {
            return "";
        }        
        $return_string="";
        $key="";
        $file= new KFile();
        $status=false;
        foreach($this->cssBuffer as $path)
        {            
            $file->setPath($path);
            if($file->exists())
            {
                $key.=$file->getPath();
                $key.=$file->getLastModified();
                $status=true;
            }
        }
        
        if(!$status)
        {
            return "";
        }
        

        //$path_cache=ParamManager::getInstance()->app_folder.KFile::separator().KApp::$FOLDER_CACHE;
        $keyx=md5($key);
        $cache_filename=self::CSS_BUFFER_PREFIX.$keyx;
        if(!KCache::getInstance()->isValueCached($cache_filename))
        {          
            require_once __ROOT__."/K-lib/K-minify/KMinify.class.php";
            $minify=new KMinify();
            $string="";
            foreach($this->cssBuffer as $path)
            {            
                $file->setPath($path);
                if($file->exists())
                {
                    //$string.=minifyStringCSS($file->toContentString())."\n";
                    $string.=($file->toContentString())."\n";
                    //$string.=$minify->minifyCssString($file->toContentString())."\n";
                }
            }
            if(!KCache::getInstance()->makeCacheFromString($string))
            //if(!KCache::getInstance()->makeFileCacheFromString($minify->minifyCssString($string)))
            {
                echo '<error> folder for cache not writable : '.KCache::getInstance()->getPath_cache().' </error>';
            }
        }
        
        if(KCache::getInstance()->isValueCached($cache_filename))
        {
            $url=KRoute::makeKURL(KRoutesItems::$CSS_KACHE,["key"=>$keyx]);
            $return_string='
        <link rel="stylesheet" type="text/css" href="'.$url->printURLWithoutAmp().'" />';
        }
        return $return_string;
    }
    
    private function processJSBufferStart() : string
    {
        return $this->processJSBuffer($this->jsBuffer);
    }

    private function processJSBufferEnd() : string
    {
        return $this->processJSBuffer($this->jsBufferEnd);
    }
    
    private function processJSModuleBufferStart() : string
    {
        $include="";
        if(!is_null($this->jsModuleBuffer))
        {        
            foreach ($this->jsModuleBuffer as $jsModule)
            {
                $include.=$this->processJSBuffer($jsModule,true);
            }
        }
        return $include;
    }
    
    private function processJSAppBufferStart() : string
    {
        $include="";
        if(!is_null($this->jsAppBuffer))
        {        
            foreach ($this->jsAppBuffer as $jsApp)
            {
                $include.=$this->processJSBuffer($jsApp,false,true);
            }
        }
        return $include;
    }    
    
    private function processJSModuleBufferEnd() : string
    {
        $include="";
        if(!is_null($this->jsModuleBufferEnd))
        {
            foreach ($this->jsModuleBufferEnd as $jsModule)
            {
                $include.=$this->processJSBuffer($jsModule,true);
            }
        }
        return $include;
    }    
    
    private function processJSBuffer(?HashMap $jsBuffer,bool $module=false,bool $app=false) : string
    {
        if(is_null($jsBuffer))
        {
            return "";
        }
        $return_string="";
        $key="";
        $file= new KFile();
        $status=false;
        foreach($jsBuffer as $path)
        {            
            $file->setPath($path);
            if($file->exists())
            {
                $key.=$file->getName();
                $key.=$file->getLastModified();
                $status=true;
            }
        }
        
        if(!$status)
        {
            return "";
        }
        
        //$path_cache=ParamManager::getInstance()->app_folder.KFile::separator().KApp::$FOLDER_CACHE;
        $keyx=md5($key);
        $cache_filename=self::JS_BUFFER_PREFIX.$keyx;
        KCache::getInstance()->isValueCached($cache_filename);
        if(!KCache::getInstance()->isValueCached($cache_filename))
        {
            require_once __ROOT__."/K-lib/K-minify/KMinify.class.php";
            $minify=new KMinify();       
            $string="";
            foreach($jsBuffer as $path)
            {            
                $file->setPath($path);
                if($file->exists()&&$file->isFile())
                {
                    //$string.=minifyStringCSS($file->toContentString())."\n";
                    //$string.= "/* File path : " . $path . "*/\n\n";
                    $string .= ($file->toContentString())."\n";
                    //$string.=$minify->minifyJsString($file->toContentString())."\n";
                }
            }
            
            if(!KCache::getInstance()->makeCacheFromString($string))
            //if(!KCache::getInstance()->makeFileCacheFromString($minify->minifyJsString($string)))
            {
                echo '<error> folder for cache not writable : '.KCache::getInstance()->getPath_cache().' </error>';
            }
        }
        
        if(KCache::getInstance()->isValueCached($cache_filename))
        {
            $url=KRoute::makeKURL(KRoutesItems::$JS_KACHE,["key"=>$keyx]);
            if($module)
            {
            $return_string='
        <script type="module" src="'.$url->printURLWithoutAmp().'"></script>';
            }
            else if($app)
            {
            $return_string='
        <script type="application/javascript" src="'.$url->printURLWithoutAmp().'"></script>';
            }            
            else
            {
            $return_string='
        <script src="'.$url->printURLWithoutAmp().'"></script>';                
            }
        }
        
        return $return_string;        
    }
}