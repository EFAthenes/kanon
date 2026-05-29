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
class PHPFileComponent extends KComponent
{
    private string $phpFile="";
    /**
     * 
     * @var array<mixed,mixed>|null
     */
    private ?array $data=null;
    private ?string $defaultRootPath=null;
    private string $path="";
    private string $errorString="-EMPTY FILE-";
    private bool $isInit=false;
    private bool $twigOutput=false;
    
    /**
     * 
     * @param string $path
     * @param array<mixed,mixed>|null $data
     * @param string|null $defaultRootPath
     * @param bool $twigOutput
     */
    function __construct(string $path="not_set",?array $data=null,?string $defaultRootPath=null,bool $twigOutput=false)
    {
        parent::__construct();
        $this->setNone();
        if(LanguageManager::$IS_ACTIVE)
        {
            $data['current_lang']=LanguageManager::getInstance()->getLanguage();
        }
        $this->setData($data);
        $this->setDefaultRootPath($defaultRootPath);
        $this->path=$path;
        $this->twigOutput=$twigOutput;
    }
    
    public function init() : bool
    {
        //echo "init";
        $this->isInit=true;
        $status=false;
        $tempPhpFile="";
        if(is_null($this->defaultRootPath))
        {      
            // Test in Kapp
            $this->defaultRootPath=__ROOT__.KFile::separator()."K-view".KFile::separator();
            $tempPhpFile=$this->defaultRootPath.$this->path;
            if($this->setPhpFile($tempPhpFile))
            {
                $status=true;
            }  
            //echo "1".$tempPhpFile."=> ".convertBoolToString($status)."\n";

            $temp=ParamManager::getInstance()->app_folder;
            $tempPhpFile=$temp.$this->path;
            if($this->setPhpFile($tempPhpFile))
            {
                $this->defaultRootPath=$temp;
                $status=true;
            }
            //echo "2".$tempPhpFile."=> ".convertBoolToString($status)."\n";;
            if($status)
            {
                return true;
            }
        }        
        else
        {
            $tempPhpFile=$this->defaultRootPath.$this->path;
            if($this->setPhpFile($tempPhpFile))
            {
                return true;
            }                 
        }
        $this->errorString=$tempPhpFile; 
        return false;
    }
    
    public function setPhpFile(string $path): bool
    {
        $newPath=$this->normalizePath($path);
        $file=new KFile($newPath);
        if($file->exists()&&$file->isFile()&&$file->getExtension()=="php")
        {
            $this->phpFile=$newPath;  
            return true;
        }
        return false;
    }
    
    public function getPhpFile(): string
    {
        return $this->phpFile;
    }

    /**
     * 
     * @param array<mixed,mixed>|null $data
     * @return void
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }
    
    public function setDefaultRootPath(?string $defaultRootPath): void
    {
        $this->defaultRootPath = $defaultRootPath;
    }
        
    public function draw() : string
    {
        if(!$this->isInit)
        {
            $this->init();
        }
        $print="";
        if(!empty($this->phpFile))
        {
//            KDebugger::getInstance()->dump($this->phpFile, "PhpFile");
//            KDebugger::getInstance()->dump($this->twigOutput,"twigOutput");
            if($this->twigOutput)
            {
                $print=KTemplateRenderers::renderTwig($this->phpFile,$this->data);
            }
            else
            {
                $print=KTemplateRenderers::renderPHP($this->phpFile,$this->data);
            }
        }
        else
        {
            $this->addHTML("<b> CANNOT FOUND VIEW ==> ".$this->errorString."</b> <br />");
            $print.=parent::draw();
        }
        return $print;
    }
    
    public function setIsInit(bool $isInit): void
    {
        $this->isInit = $isInit;
    }
    
    public function getTwigOutput(): bool
    {
        return $this->twigOutput;
    }

    public function setTwigOutput(bool $twigOutput): void
    {
        $this->twigOutput = $twigOutput;
    }
    
    private function normalizePath(string $path) : string
    {
        return preg_replace('#[\\\\/]+#', DIRECTORY_SEPARATOR, $path);
    }
}