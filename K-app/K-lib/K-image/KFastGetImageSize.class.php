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
//\K-composer\vendor\marc1706\fast-image-size\lib\Type
declare(strict_types=1);
require_once __ROOT__.'/K-composer/vendor/autoload.php';
use FastImageSize\FastImageSize;

class KFastGetImageSize
{
    private ?FastImageSize $imageSize = null;
    /**
     * 
     * @var array<string,mixed>|null
     */
    private ?array $arrayInfo = [];
    public function __construct()
    {
        $this->imageSize = new FastImageSize();
    }  
    
    public function getImageSize(string $path) : bool
    {
        $file=new KFile($path);
        if($file->isExtensions(["tif","tiff","png","jpg","jpeg"]))
        {
            $array=$this->imageSize->getImageSize($path);
            //var_dump($array);
            if(is_array($array)&&count($array)>0)
            {
                $this->arrayInfo=$array;
                return true;
            }
        }
        $array=getimagesize($path);
        if(is_array($array)&&count($array)>=3)
        {
            $this->arrayInfo['width']=$array[0];
            $this->arrayInfo['height']=$array[1];
            $this->arrayInfo['type']=$array[2];
            return true;
        }
        return false;
    }
    
    public function getWidth() : ?int
    {
        $width=null;
        if(count($this->arrayInfo)&& array_key_exists('width', $this->arrayInfo))
        {
            $width=$this->arrayInfo['width'];
        }
        return $width;
    }
    public function getHeight() : ?int
    {
        $height=null;
        if(count($this->arrayInfo)&& array_key_exists('height', $this->arrayInfo))
        {
            $height=$this->arrayInfo['height'];
        }
        return $height;        
    }    
    public function getType() : ?string
    {
        $type=null;
        if(count($this->arrayInfo)&& array_key_exists('type', $this->arrayInfo))
        {
            $type=$this->arrayInfo['type'];
        }
        return $type;        
    }  
    /**
     * 
     * @return array<int,int|null>
     */
    public function getSizeInArrayInt() : array
    {
        return [$this->getWidth(),$this->getHeight()];
    }     

    /**
     * 
     * @return array<string,mixed>|null
     */
    public function getArrayInfo() : ?array
    {
        return $this->arrayInfo;
    }
    /**
     * 
     * @param array<string,mixed>|null $arrayInfo
     * @return self
     */
    public function setArrayInfo(?array $arrayInfo) : self
    {
        $this->arrayInfo = $arrayInfo;
        return $this;
    } 
}