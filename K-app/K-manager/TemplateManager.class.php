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
class TemplateManager extends HashMapSingleton
{
    public const string TEMPLATE_DIR="template";
    public const string CSS_DIR="css";
    public const string CSS_PUBLIC_FILE="public.css";
    private string $activeTemplate="";
    private string $configTemplate="";
    private ?KFile $templateDir=null;
    public function init() : void
    {
        $this->activateTemplate($this->getConfigTemplate());
    }
    
    public function getConfigTemplate() :string
    {
        if(empty($this->configTemplate))
        {
            $this->updateConfigTemplate();
        }
        return $this->configTemplate;
    }
    
    public function updateConfigTemplate() :string
    {
        $this->configTemplate=ParamManager::getInstance()->get("TEMPLATE_STATIC_HTML");
        return $this->configTemplate;
    }    
    
    public function activateTemplate(mixed $s) : bool
    {
        $template= strval($s);
        if(!empty($template))
        {
            $templatePath=KFile::separator().self::TEMPLATE_DIR.KFile::separator().$template.KFile::separator();
            $appTemplatePath=ParamManager::getInstance()->app_folder.$templatePath;
            $dir=new KFile($appTemplatePath);
            if($dir->exists()&&$dir->isDirectory())
            {
                $this->templateDir=$dir;
                $this->activeTemplate=$template;
                return true;
            }
        }
        return false;
    }
    
    public function isTemplateEnable() : bool
    {
        if(!empty($this->activeTemplate))
        {
            return true;
        }
        return false;
    }
    
    public function getActiveTemplateName(): string
    {
        return $this->activeTemplate;
    }

    public function getTemplateDir(): ?KFile
    {
        return $this->templateDir;
    }
    
    public function getTemplateDirFullPath() : string
    {
        if(!is_null($this->templateDir))
        {
            if($this->templateDir->exists()&&$this->templateDir->isDirectory())
            {
                return $this->templateDir->getPath();
            }
        }
        return "";
    }

    public function getPublicCSS() : string
    {
        $templatePath=$this->getTemplateDirFullPath();
        if(!empty($templatePath))
        {
            $publicCssFile=$templatePath.KFile::separator().self::CSS_DIR.KFile::separator().self::CSS_PUBLIC_FILE;
            $cssFile=new KFile($publicCssFile);
               if($cssFile->exists()&&$cssFile->isFile())     
               {
                   return $cssFile->getPath();
               }
           
        }
        return "";
    }  
}