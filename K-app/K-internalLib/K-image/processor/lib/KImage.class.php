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
class KImage extends AbstractKimageProcessor
{
    private ?AbstractKimageProcessor $engine = null;

    public function __construct(?string $src,string $engine = 'imagick')
    {
        parent::__construct();
        $this->setSrc($src);       
        $this->setEngine($engine); 
    }
    
    public function setEngine(string $engine) : bool
    {
        $this->engine=match($engine) {
            self::VIPS_ENGINE => new VipsProcessor(),
            self::IMAGICK_ENGINE => new ImagickProcessor(),
            self::GD_ENGINE => new GdProcessor(),
            default => null,
        }; 
        
        if(!is_null($this->engine)&&!empty($this->getSrc()))
        {
            $this->setEngineName($engine);
            $this->engine->openSrc($this->getSrc());
            //echo "NOT NULL".$engine."<br />";
        }
        return $this->isEngineReady();
    }
    
    #[\Override]
    public function isImageReady(): bool
    {
        $this->testEngine();
        return $this->engine->isImageReady();        
    }

    public function isEngineReady() : bool
    {
        if(!is_null($this->engine))
        {
            return true;
        }
        return false;        
    }
    
    public function testEngine() : void
    {
        if(!$this->isEngineReady())
        {
            throw new Exception("Unsupported engine:");
        }
    }
    
    #[\Override]
    public function getWidth() : int
    {
        $this->testEngine();
        return $this->engine->getWidth();
    }
    
    #[\Override]
    public function getHeight() : int
    {
        $this->testEngine();
        return $this->engine->getHeight();
    }    
    
    #[\Override]
    /**
     * 
     * @return  array<int,int>
     */
    public function getFastWidthHeight() : array
    {
        $this->testEngine();
        return $this->engine->getFastWidthHeight();
    }
    
    #[\Override]
    public function getEngineInformationsToString(string $delimiter = "<br />") : string
    {
        $this->testEngine();
        return $this->engine->getEngineInformationsToString($delimiter);
    }
    
    #[\Override]
    /**
     * 
     * @return  array<int,string>
     */
    public function getEngineInformations() : array
    {
        $this->testEngine();
        return $this->engine->getEngineInformations();
    }
    
    #[\Override]
    public function scaleImage(int $width, int $height, bool $bestfit = true) : bool
    {
        $this->testEngine();
        return $this->engine->scaleImage($width, $height,$bestfit);
    }
    
    #[\Override]
    public function writeImage(string $dst,string $format="") : bool
    {
        $this->testEngine();
        if($this->engine->writeImage($dst,$format))
        {
            return true;
        }
        $this->setError("writeImage() failed for ".$dst."||".$this->getSrc());
        return false;
    }
    
    #[\Override]
    public function toBuffer(): string
    {
        $this->testEngine();
        return $this->engine->toBuffer();
    }
    
    #[\Override]
    public function setFormat(mixed $format): bool
    {
        $this->testEngine();
        if(self::isformatSupported($format))
        {
            return $this->engine->setFormat($format);
        }
        return false;
    }
    /**
     * @param int $targetWidth
     * @param int $targetHeight
     * @param array<int,int> $background
     * @return bool
     */
    #[\Override]
    public function padImage(int $targetWidth, int $targetHeight, array $background = [255, 255, 255]): bool
    {
        $this->testEngine();
        return $this->engine->padImage( $targetWidth, $targetHeight, $background);        
    }
    
    
    #[\Override]
    public function compositeImage(mixed $image, int $composite, int $x, int $y, ?int $channel=null): bool
    {
        // NOT TESTED
        return false;
//        $this->testEngine();
//        return $this->engine->compositeImage($image, $composite, $x, $y, $channel);
    }
    
    #[\Override]
    public function cropImage(int $width, int $height,int $x, int $y): bool
    {
        $this->testEngine();
        return $this->engine->cropImage( $width, $height, $x,  $y);
    }
    
    #[\Override]
    public function flattenImages(): bool
    {
        $this->testEngine();
        return $this->engine->flattenImages();
    }

    #[\Override]
    public function freeMemory(): bool
    {
        $this->testEngine();
        return $this->engine->freeMemory();        
    }

    #[\Override]
    public function openSrc(?string $src): bool
    {
        $this->testEngine();
        return $this->engine->openSrc($src);        
    }
    
    #[\Override]
    public function flopImage(): bool
    {
        $this->testEngine();
        return $this->engine->flopImage(); 
    }
    
    #[\Override]
    public function transformColorSpace(int $colorSpace): bool
    {
        $this->testEngine();
        if(self::isColorSpaceSupported($colorSpace))
        {
            return $this->engine->transformColorSpace($colorSpace);         
        }
        return false;
    }
    
    #[\Override]
    public function rotateImage(mixed $angle=0,mixed $colorBackground=""): bool
    {
        $this->testEngine();
        $new_angle=intval($angle);
        if($new_angle==0)
        {
            return true;
        }
        return $this->engine->rotateImage($new_angle,$colorBackground); 
    }   
 
    public function toString(string $delimitor="<br />") : string
    {
        $s='';
        $s.='src='.$this->engine->getSrc().$delimitor;
        $s.=$this->engine->getEngineInformationsToString($delimitor).$delimitor;
        return $s;
    }
    
    /**
     * 
     * @return array<int,string>
     */
    public function getErrors() : array
    {
        return $this->engine->getErrors();
    }
    public function getLastError() : string
    {
        return $this->engine->getLastError();
    }
    public function getErrorsToString(string $delimiter="\n") : string
    {
        return $this->engine->getErrorsToString($delimiter);
    }
    public function cleanErrors() :void
    {
        $this->engine->cleanErrors();
    }    

}