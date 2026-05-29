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
abstract class AbstractKimageProcessor
{
    /**
     * 
     * @var array<int,string>
     */
    private array $formats=[];
    /**
     * 
     * @var array<int,int>
     */
    private array $colorSpaces=[];
    /**
     * 
     * @var array<int,string>
     */
    private array $errors=[];
    private string $src="";
    private string $engineName="";
    private bool $stripMetadata=false;
    private string $the_format="";
    
    public const string TIF="tif";
    public const string PNG="png";
    public const string JPG="jpg";
    public const string GIF="gif";
    public const string WEBP="webp";
    public const string BMP="bmp";
    public const string PDF="pdf";
    
    public const int COLORSPACE_SRGB=1;
    public const int COLORSPACE_GRAY=2;
    public const int COLORSPACE_BITONAL=3;
    public const int COLORSPACE_CMYK=4;
    
    public const int COMPOSITE_OVER=1;
    public const int COMPOSITE_MULTIPLY=2;
    public const int COMPOSITE_SCREEN=3;

    public const string VIPS_ENGINE="vips";
    public const string IMAGICK_ENGINE="imagick";
    public const string GD_ENGINE="gd";
    
    public function __construct()
    {
        $this->formats=self::makeFormats();
        $this->colorSpaces=self::makeColorSpaces();
    }
    
    protected function setEngineName(string $engineName) : void
    {
        $this->engineName=$engineName;
    }
    
    public function getEngineName() : string
    {
        return $this->engineName;
    }
    
    public static function isEngineValid(mixed $engine) : bool
    {
        $valid=match($engine) {
            self::VIPS_ENGINE => true,
            self::IMAGICK_ENGINE => true,
            self::GD_ENGINE => true,
            default => false,
        }; 
        return $valid;
    }
    
    /**
     * 
     * @return array<int,int>
     */
    public function getColorSpaces() : array
    {
        return $this->colorSpaces;
    }
    
    public function getThe_format(): string
    {
        return $this->the_format;
    }

    public function setThe_format(string $the_format): void
    {
        $this->the_format = $the_format;
    }

        
    /**
     * 
     * @return array<int,string>
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * 
     * @param array<int,string> $formats
     * @return void
     */
    public function setFormats(array $formats): void
    {
        $this->formats = $formats;
    }

        
    
    public function getSrc(): string
    {
        return $this->src;
    }
    public function setSrc(mixed $src): bool
    {
        if(!empty($src))
        {
            $this->src = strval($src);
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return array<int,int>
     */
    public function getFastWidthHeight() : array
    {
        $dim=[0,0];
        if(!empty($this->src))
        {
            KApp::getInstance()->includeKAppFile('/K-lib/K-image/KFastGetImageSize.class.php');
            $fast=new KFastGetImageSize();
            if($fast->getImageSize($this->src))
            {
                $height_i=$fast->getHeight();
                $width_i=$fast->getWidth();
                $dim=[$width_i,$height_i];
            }
            else
            {
                $height_i=$this->getHeight();
                $width_i=$this->getWidth();   
                $dim=[$width_i,$height_i];
            }
        }
        return $dim;
    }
    
     public function stripMetaData() : void
     {
         $this->stripMetadata=true;
     }
     
     public function keepMetaData() : void
     {
         $this->stripMetadata=false;
     }
     
     public function getStripMetadata(): bool
     {
         return $this->stripMetadata;
     }
         
    public function setError(mixed $s) : void
    {
        $this->errors[]=strval($s);
    }
    /**
     * 
     * @return array<int,string>
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
    public function getLastError() : string
    {
        return strval(end($this->errors));
    }
    public function getErrorsToString(string $delimiter="\n") : string
    {
        $string="";
        foreach ($this->errors as $error)
        {
            $string.=$error.$delimiter;
        }
        return $string;
    }
    public function cleanErrors() :void
    {
        $this->errors=[];
    }
    /**
     * 
     * @return array<int,string>
     */
    public function listFormats() : array
    {
        return $this->formats;
    }
    
    public function checkFormat(mixed $format) : bool
    {
        if(in_array($format, $this->formats))
        {
            return true;
        }
        return false;   
    }
    
    abstract public function openSrc(?string $src) : bool;
    abstract public function isImageReady() : bool;
    
    abstract public function getWidth() : int;
    abstract public function getHeight() : int;
    abstract public function scaleImage(int $width, int $heigth, bool $bestfit = false) : bool;
    abstract public function writeImage(string $dst,string $format="") : bool;
    
    abstract public function compositeImage(mixed $image, int $composite, int $x, int $y, ?int $channel=null): bool;
    abstract public function flattenImages() : bool;
    abstract public function cropImage(int $width, int $height,int $x, int $y): bool;
    abstract public function freeMemory() : bool;
    abstract public function flopImage(): bool;
    abstract public function transformColorSpace(int $colorSpace): bool;
    abstract public function rotateImage(mixed $angle=0,mixed $colorBackground=""): bool;
    abstract public function toBuffer() : string;
    abstract public function setFormat(mixed $format) : bool;

    /**
     * @param int $targetWidth
     * @param int $targetHeight
     * @param array<int,int> $background
     * @return bool
     */
    abstract public function padImage(int $targetWidth, int $targetHeight, array $background = [255, 255, 255]): bool;   
    
    /**
     * @return array<mixed,mixed>
     */
    abstract public function getEngineInformations() : array;
    abstract public function getEngineInformationsToString(string $delimiter="<br />") : string;
    
    /**
     * @return array<int,string>
     */
    public static function makeFormats() : array
    {
        $formats=[self::TIF,self::PNG,self::JPG,self::GIF,self::WEBP,self::BMP,self::PDF];
        return $formats;
    }
    
    /**
     * @return array<int,int>
     */
    private static function makeColorSpaces() : array
    {
        $colorSpaces=[self::COLORSPACE_SRGB,self::COLORSPACE_GRAY,self::COLORSPACE_BITONAL,self::COLORSPACE_CMYK];
        return $colorSpaces;
    }
    
    /**
     * @return array<int,int>
     */
    private static function makeCompositeOptions() : array
    {
        $compositeOptions=[self::COMPOSITE_OVER,self::COMPOSITE_MULTIPLY,self::COMPOSITE_SCREEN];
        return $compositeOptions;
    }    
    
    
    public static function isformatSupported(mixed $format) : bool
    {
        $the_format=strtolower(strval($format));
        if(!empty($the_format))
        {
             $formats=self::makeFormats();
             if(in_array($format, $formats))
             {
                 return true;
             }
        }
        return false;
    }
    
    public static function isColorSpaceSupported(mixed $colorSpace) : bool
    {
        $the_colorSpace=intval($colorSpace);
        if(!empty($the_colorSpace))
        {
             $colorSpaces=self::makeColorSpaces();
             if(in_array($the_colorSpace, $colorSpaces))
             {
                 return true;
             }
        }
        return false;
    }  
    
    public static function isCompositeOptionSupported(mixed $compositeOption) : bool
    {
        $the_compositeOption=intval($compositeOption);
        if(!empty($the_compositeOption))
        {
             $compositeOptions=self::makeColorSpaces();
             if(in_array($the_compositeOption, $compositeOptions))
             {
                 return true;
             }
        }
        return false;
    }      
    
}