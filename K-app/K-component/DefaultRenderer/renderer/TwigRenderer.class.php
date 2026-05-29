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
class TwigRenderer extends AbstractKRenderer
{
    private mixed $loader;
    private mixed $twig;
    private mixed $loaderTemplate;
    private mixed $twigTemplate;
    private bool $debug=false;
    private string $pathTemplate="";
    private string $pathView="";
    public function __construct()
    {
        require_once __ROOT__.'/K-composer/vendor/autoload.php';
        $dir=realpath(KApp::getInstance()->getAppFolder().KFile::separator().KApp::$FOLDER_VIEW).KFile::separator();
        //KDebugger::_($dir,"TwigRenderer 1");
        $dirTwig=new KFile($dir);
        if(!$dirTwig->exists())
        {
            $dirTwig->mkdir();
        }
        $this->pathView=$dir;

        $dirTemplate=realpath(KApp::getInstance()->getAppFolder().KFile::separator().KApp::$FOLDER_TEMPLATE).KFile::separator();
        $template=TemplateManager::getInstance()->getActiveTemplateName();
        //KDebugger::_($dirTemplate,"TwigRenderer 2");
        //KDebugger::_($template,"TwigRenderer 3");
        if(!empty($template))
        {
            $template_path=$dirTemplate.$template.KFile::separator();   
            $dirTwigTemplate=new KFile($template_path);
            if($dirTwigTemplate->exists())
            {
                $this->pathTemplate=$template_path;
            }
        } 

        $this->debug=ParamManager::getInstance()->debug;
        
        $cache_path=KApp::getInstance()->getAppFolder().KFile::separator().KCache::$FOLDER_CACHE.KFile::separator()."twig-template".KFile::separator();
        $dirTwigCache=new KFile($cache_path);
        //KDebugger::_($cache_path,"TwigRenderer 4");
        if(!$dirTwigCache->exists())
        {
            $dirTwigCache->mkdir();
        }        
        
        if(TemplateManager::getInstance()->isTemplateEnable())
        {
            $templatePath=$dirTwigCache->getPath().KFile::separator().TemplateManager::getInstance()->getActiveTemplateName();
            $dirTwigTemplateCache=new KFile($templatePath);
            if(!$dirTwigTemplateCache->exists())
            {
                $dirTwigTemplateCache->mkdir();
            } 
            $cache_path=$templatePath;
        }
        
        $this->loader = new \Twig\Loader\FilesystemLoader($dirTwig->getPath());
        $this->twig = new \Twig\Environment($this->loader,
                [
                    'debug' => $this->debug,
                    'cache' => $cache_path
                ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        
        if(!empty($this->pathTemplate))
        {
            $this->loaderTemplate = new \Twig\Loader\FilesystemLoader($this->pathTemplate);
            $this->twigTemplate = new \Twig\Environment($this->loaderTemplate,
                    [
                        'debug' => $this->debug,
                        'cache' => $cache_path
                    ]);
            $this->twigTemplate->addExtension(new \Twig\Extension\DebugExtension());     
        }

    }

    /**
     * 
     * @param string $phpFile
     * @param array<mixed,mixed>|null $the_data
     * @return string
     */
    #[\Override]
    public function renderFile(string $phpFile,?array $the_data=null) : string
    {
        $data=[];
        if(!is_null($the_data))
        {            
            $data=$the_data;//["data"=>$the_data];
        }
      
//        KDebugger::getInstance()->dump($this->pathTemplate,'pathTemplate');
//        KDebugger::getInstance()->dump($this->pathView,'pathView');
//        KDebugger::getInstance()->dump($phpFile,'phpFile');
        if(!empty($this->pathTemplate)&&str_starts_with($phpFile,$this->pathTemplate))
        {
            $phpFile=str_replace($this->pathTemplate,"/", $phpFile);
            return $this->twigTemplate->render($phpFile,$data);
        }                
        else if(str_starts_with($phpFile,$this->pathView))
        {
            $phpFile=str_replace($this->pathView,"/", $phpFile);        
            return $this->twig->render($phpFile,$data);
        }
        
        return "TWIG ERROR -- NO RENDERER";
    }
}