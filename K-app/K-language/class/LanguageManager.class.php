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
class LanguageManager extends SessionMemoryItem
{
    private static ?self $instance=null;
    //private $list=null;
    private ?HashMap $map=null;
    private static string $default_language="fr";
    private ?string $language="fr";
    private string $templatePath="";
    /**
     * 
     * @var array<int,string>
     */
    private array $language_supported=[];    
    /**
     * 
     * @var array<int,string>
     */
    private array $language_accepted=[];
    
    public static string $LANGUAGE_FR="fr";
    public static string $LANGUAGE_EN="en";
    public static string $LANGUAGE_GR="el";
    public static string $LANGUAGE_ES="es";
    public static string $LANGUAGE_IT="it";
    
    public static string $LANGUAGE_FR_LABEL="Français";
    public static string $LANGUAGE_EN_LABEL="English";
    public static string $LANGUAGE_GR_LABEL="Ελληνικά";
    public static string $LANGUAGE_ES_LABEL="Español";
    public static string $LANGUAGE_IT_LABEL="Italiano";    
    
    public string $folder ="i18n";
    /**
     * 
     * @var array<int,string>|null
     */
    private ?array $array_languages_label=null;
    private string $separator="==>";
    
    public static bool $IS_ACTIVE=false;
    
    public static string $GET_PARAM="lang";
    
//    private static $DEBUG=true;
    private static bool $DEBUG_RELOAD =true;
    private static bool $DEBUG_PRINT =false;    

    private function __construct()
    {        
        $this->map=new HashMap();
        $this->initLanguageSupported();
        $this->tryToInitByConfigLanguage();
        self::$IS_ACTIVE=$this->initLanguage($this->language);
    }
    
    private function tryToInitByConfigLanguage() : bool
    {
        $default=ParamManager::getInstance()->get("DEFAULT_LANG");  
        if(!empty($default)&&$default!="auto")
        {
            if($this->setLanguage($default))
            {
                return true;
            }    
        }     
        return $this->setDefaultByNavigator();
    }
    
    private function initLanguageAccepted(): void
    {
        //Examples : 
        //ParamManager::getInstance()->add("LANGUAGES",LanguageManager::$LANGUAGE_FR.",".LanguageManager::$LANGUAGE_EN.",".LanguageManager::$LANGUAGE_GR.",".LanguageManager::$LANGUAGE_ES.",".LanguageManager::$LANGUAGE_IT); 
        $config_langs = ParamManager::getInstance()->get("LANGUAGES");
        $this->language_accepted = [];
        if (!empty($config_langs))
        {
            $langs = explode(",", "".ParamManager::getInstance()->get("LANGUAGES"));
            foreach ($langs as $value)
            {
                if (in_array($value, $this->language_supported))
                {
                    $this->language_accepted[] = $value;
                }
            }
        }

        if (count($this->language_accepted) == 0)
        {
            $this->initByDefaultLangArray($this->language_accepted);
        }
    }
    
    /**
     * 
     * @param array<int,string> $array
     * @return void
     */
    private function initByDefaultLangArray(array &$array) : void
    {
        $array = array(
            self::$LANGUAGE_FR, 
            self::$LANGUAGE_EN, 
            self::$LANGUAGE_GR, 
            self::$LANGUAGE_ES, 
            self::$LANGUAGE_IT); 
    }

    private function initLanguageSupported() : void
    {
        $this->initByDefaultLangArray($this->language_supported);
        
        $this->array_languages_label=[];
        $this->array_languages_label[self::$LANGUAGE_FR]=self::$LANGUAGE_FR_LABEL;
        $this->array_languages_label[self::$LANGUAGE_EN]=self::$LANGUAGE_EN_LABEL;
        $this->array_languages_label[self::$LANGUAGE_GR]=self::$LANGUAGE_GR_LABEL;
        $this->array_languages_label[self::$LANGUAGE_ES]=self::$LANGUAGE_ES_LABEL;
        $this->array_languages_label[self::$LANGUAGE_IT]=self::$LANGUAGE_IT_LABEL;
        
        $this->initLanguageAccepted();
    }
    
    public static function _(string $string) : string
    {
        return self::getInstance()->get($string);
    }
      
    /**
     * 
     * @return array<int,string>
     */
    public function getArrayOfLanguages() : array
    {
        return $this->language_accepted;
    }
    
    public function setDefaultByNavigator() : bool
    {
        $supportedLanguages = [
             // first one is the default/fallback
            'fr',
            'fr-FR',
            'en-US',
            'en',
            'en-GB',
            'it',
            'es',
            'el'
        ];
        if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
        {
            $this->language=$this->prefered_language($supportedLanguages,$_SERVER["HTTP_ACCEPT_LANGUAGE"]);   
            if(is_null($this->language))
            {
                $this->language=self::$LANGUAGE_EN;
            }
            return true;
        }
        return false;
    }
    
    public static function getInstance() : LanguageManager
    {
        if(self::$DEBUG_RELOAD)
        {
            if(self::$instance==null)
            {            
                $tempLanguage=SessionMemory::getInstance()->get(SessionMemory::$LANGUAGE);
                self::$instance=new LanguageManager();               
                SessionMemory::getInstance()->putOrReplace(SessionMemory::$LANGUAGE,self::$instance);
                if(!is_null($tempLanguage)&&!is_null($tempLanguage->getLanguage()))
                {
                    self::$instance->initLanguage($tempLanguage->getLanguage());
                }
            }
            return self::$instance; 
        }
        if(self::$instance == null )
        {
            self::$instance = SessionMemory::getInstance()->get(SessionMemory::$LANGUAGE);
            if(self::$instance == null )
            {
                self::$instance=new LanguageManager();
                SessionMemory::getInstance()->put(SessionMemory::$LANGUAGE,self::$instance);
            }
        }
        return self::$instance;
    }
    
    public function  __destruct()
    {

    }
    
    function getLanguage() : string
    {
        return $this->language;
    }

    function setLanguage(mixed $language):bool
    {
        if($this->isLanguageSupported($language))
        {
            $this->language=$language;
            return true;
        }
        return false;
    }
    
    public function isLanguageSupported(mixed $language) : bool
    {
        $lang=trim(strval($language));
        //KDebugger::getInstance()->dump($this->language_accepted,"language_accepted");
        if(in_array($lang,$this->language_accepted))
        {
            return true;
        }
        return false;
    }
        
    public function reset() :void
    {
        $this->map->clear();
    }
    public function toString(string $delimitor="<br />") : string
    {
        $string=" Language ==> ".$this->language.$delimitor;
        $string.=" Size ==> ".$this->map->getSize().$delimitor;
        $i=1;
        if(self::$DEBUG_PRINT)
        {
            foreach($this->map as $key => $value)
            {
                $string.=$i." // ".$key."=>".$value.$delimitor;
                $i++;
            }
        }
        return $string;
    }
    
    private function getLanguageTemplateKFile(string $language) : KFile
    {
        $kfile_template =new KFile($this->templatePath.KFile::separator().$language.".txt");
        return $kfile_template;
    }
    
    private function initLanguageTemplate(string $language,HashMap $map) : bool
    {
        $kfile_template =$this->getLanguageTemplateKFile($language);
        //echo $file_framework->getPath();
        if($kfile_template->exists() && $kfile_template->isFile())
        {
            $this->readFile($kfile_template,$map);  
            return true;
        }
        return $kfile_template->insertStringInFile("");
    }    
    
    private function initLanguageFramework(HashMap $map) : bool
    {
        $file_framework =new KFile(dirname(__FILE__).KFile::separator()."..".KFile::separator()."i18n".KFile::separator().$this->language.".txt");
        //echo $file_framework->getPath();
        if($file_framework->exists() && $file_framework->isFile())
        {
            $this->readFile($file_framework,$map);  
            return true;
        }
        else
        {
            $file_framework->insertStringInFile("");
            if($file_framework->exists())
            {
                return true;
            }
        }
        return false;
    }
    
    public function initLanguage(string $language,bool $init_default=true) : bool
    {        
        if(!$this->initTemplateLanguageDir())
        {
            KDebugger::getInstance()->dump("cannot initTemplateLanguageDir","Error Language Manager");
        }
        $this->language=$language;
        $directory =new KFile(ParamManager::getInstance()->app_folder.KFile::separator().$this->folder);
           
        if($directory->exists()&&$directory->isDirectory())
        {            
            $file=new KFile($directory->getPath().KFile::separator().$this->language.".txt");
            if(!in_array($this->language, $this->language_accepted)|| !$file->exists() || !$file->isFile())
            {
                $this->language=self::$default_language;
                $file=new KFile($directory->getPath().KFile::separator().$this->language.".txt");
                if(!$file->exists())
                {
                    $file->insertStringInFile("");
                }
            }
                      
            $this->map=new HashMap();            
            if($init_default)
            {
                $this->initLanguageFramework($this->map); 
            }
                        
            if($file->exists() && $file->isFile())
            {
                $this->readFile($file,$this->map);               
            } 
            
            if($this->map->getSize())
            {
                $this->initLanguageTemplate($this->language,$this->map);
                return true;
            }
        }
        return false;
    }
    
    private function readFile(KFile $file,HashMap $map) : void
    {
        //KDebugger::getInstance()->dump($file->getPath(),"File");
        $handle = fopen($file->getPath(), "r");
        $tempToken = null;
        $tempContent = null;
        if ($handle)
        {
            while (($entire_line = fgets($handle)) !== false)
            {
                $line = trim($entire_line);
                if (!stringStartsWith($line, "#"))
                {
                    if (string_contains($this->separator, $line))
                    {
                        $tempToken = null;
                        $tempContent = null;
                        $array = explode($this->separator, $line, 2);
                        if ($array != null && count($array) == 2)
                        {
                            $tempToken = trim($array[0]);
                            $tempContent = trim($array[1]);
                            //echo "PUT =>".$array[0].".". $array[1]."<br />";
                            $map->putOrReplace(trim($array[0]), trim($array[1]));
                        }
                    }
                    else if ($line == "")
                    {
                        $tempToken = null;
                        $tempContent = null;
                    }
                    else if ($tempToken != null && $tempContent != null)
                    {
                        $tempContent .= $line;
                        $map->replace($tempToken, $tempContent);
                    }
                }
            }
        }
        else
        {
            // error opening the file.
        }
        fclose($handle);
    }
    public function get(string $name) : string
    {
        $string=$this->map->get(trim($name));
        if($name==NULL||$string==NULL)
        {
            return "NOT FOUND =>".$name;
        }
        if(self::$DEBUG_PRINT)
        {
            $string="<!-- ".$name." -->".$string;
        }        
        return $string;
    }
    
    /**
     * 
     * @param string $name
     * @param array<string,string>|null $converter
     * @return string
     */
    public function getAndTransform(string $name,?array $converter=null) : string
    {
        $string=strval($this->get($name));
        if(!empty($string)&&is_array($converter))
        {
            foreach ($converter as $key => $value)
            {
                $string=str_replace("{{".strval($key)."}}",strval($value), $string);
            }
        }
        return $string;
    }
    
    public function initByGet() : bool
    {
        if(isset($_GET[LanguageManager::$GET_PARAM])&&$_GET[LanguageManager::$GET_PARAM]!="")
        {
            if($_GET[LanguageManager::$GET_PARAM]==static::$LANGUAGE_FR)
            {
                $this->language=static::$LANGUAGE_FR;
            }
            else if($_GET[LanguageManager::$GET_PARAM]==static::$LANGUAGE_EN)
            {
                $this->language=static::$LANGUAGE_EN;
            } 
            return $this->initLanguage($this->language);
        }
        return false;
    }  
    
    /**
     * 
     * @param array<int,string> $available_languages_initial
     * @param string $http_accept_language
     * @return string|null
     */
    private function prefered_language(array $available_languages_initial,string $http_accept_language) : ?string
    {
        $available_languages=array_flip($available_languages_initial);
        $langs=array();
        $matches=array();
        preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~',strtolower($http_accept_language),$matches,PREG_SET_ORDER);
        foreach($matches as $match)
        {
            list($a,$b)=explode('-',$match[1])+array('','');
            $value=isset($match[2])?(float)$match[2]:1.0;
            if(isset($available_languages[$match[1]]))
            {
                $langs[$match[1]]=$value;
                continue;
            }
            if(isset($available_languages[$a]))
            {
                $langs[$a]=$value-0.1;
            }
        }
        if($langs)
        {
            arsort($langs);
            return "".key($langs); // We don't need the whole array of choices since we have a match
        }
        return null;
    }
    
    public function getMapOfItems() : HashMap
    {
        return $this->map;
    }
    
    public function initTemplateLanguageDir() : bool
    {
        $dirTemplate=KApp::getInstance()->getTemplateFolder();
        $kdir=new KFile($dirTemplate);
        $kdir->mkdir();
        //KDebugger::getInstance()->dump($dirTemplate,"dirTemplate");
        if($kdir->exists()&&$kdir->isDirectory())
        {
            //KDebugger::getInstance()->dump($dirTemplate,"dirTemplate 2)");
            $dirLangTemplate=$kdir->getPath().KFile::separator().$this->folder;
            $kdirTemplate=new KFile($dirLangTemplate);
            $kdirTemplate->mkdir();
            //KDebugger::getInstance()->dump($dirLangTemplate,"dirTemplate 2b)");
            if($kdirTemplate->exists()&&$kdirTemplate->isDirectory())
            {
                //KDebugger::getInstance()->dump($dirTemplate,"dirTemplate 3)");
                $this->templatePath=$kdirTemplate->getPath();
                return true;
            }
        }
        return false;
    }
    /**
     * 
     * @param array<string,array<string,string>> $arrayLang
     * @return bool
     */
    public function updateTemplateLang(array $arrayLang): bool
    {
        $this->initTemplateLanguageDir();
        foreach ($arrayLang as $lang =>$arrayItems)
        {
            $map=new HashMap();

            if($this->isLanguageSupported($lang)&&$this->initLanguageTemplate($lang,$map))
            {
                foreach ($arrayItems as $key=>$value)
                {
                    $map->putOrReplace($key, $value);
                }
                if($map->getSize() && !$this->saveMapToFileTemplate($lang, $map))    
                {
                    KDebugger::getInstance()->dump("saveMapToFileTemplate => ".$lang,"Error");
                    return false;
                }
            } 
            else
            {
                KDebugger::getInstance()->dump("isLanguageSupported => ".$lang,"Error");
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * @param string $lang
     * @param HashMap $map
     * @return bool
     */
    private function saveMapToFileTemplate(string $lang, HashMap $map) : bool
    {
        $string='';
        foreach ($map as $key=>$value)
        {
            $string.=$key." ".$this->separator." ".$value."\n";
        }
        $file=$this->getLanguageTemplateKFile($lang);
        
        return $file->insertStringInFile($string);
    }
    
    public function getLabelByLang(string $lang) : string
    {
        if(in_array($lang, $this->language_supported))
        {
            return $this->array_languages_label[$lang];
        }
        return "Language not supported";
    }
    
    public function toWebString() : string
    {
        return $this->toString("<br />");
    }

}

class Ki18
{
    public static function _(string $string) : string
    {
        return LanguageManager::getInstance()->get($string);
    } 
    /**
     * 
     * @param string $string
     * @param array<string,mixed>|null $converter
     * @return string
     */
    public static function _t(string $string,?array $converter=null) : string
    {
        return LanguageManager::getInstance()->getAndTransform($string,$converter);
    } 
}