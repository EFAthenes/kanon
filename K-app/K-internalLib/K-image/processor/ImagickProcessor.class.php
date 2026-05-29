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
class ImagickProcessor extends AbstractKimageProcessor
{
    private ?\Imagick $image = null;
    
    
    #[\Override]
    public function openSrc(?string $src): bool
    {
        if(!empty($src))
        {
            if(!$this->setSrc($src))
            {
                return false;
            }
        }
        
        if(empty($this->getSrc()))
        {
            return false;
        }
        //echo $this->getSrc();
        $status=true;
        try
        {
            // create new Imagick object
            $this->image = new Imagick($this->getSrc());
        }
        catch(ImagickException $e)
        {
            $this->setError($e->getMessage());
            $status=false;
        }  
        return $status;
    }
    
    
    #[\Override]
    public function isImageReady(): bool
    {
        if(!is_null($this->image))
        {
            return true;
        }    
        return false;
    }

    #[\Override]
    public function getEngineInformations(): array
    {
        $version_info = \Imagick::getVersion();     
        return $version_info;
    }

    #[\Override]
    public function getEngineInformationsToString(string $delimiter = "<br />"): string
    {
        $version_info=$this->getEngineInformations();
        $string="";
        foreach ($version_info as $key => $value) {
            $string .= $key.": ".$value.$delimiter;
        }
        return $string;   
    }

    
    #[\Override]
    public function getHeight(): int
    {
        if(!is_null($this->image))
        {
            return $this->image->getImageHeight();
        }
        return 0;
    }

    #[\Override]
    public function getWidth(): int
    {
        if(!is_null($this->image))
        {
            return $this->image->getImageWidth();
        }       
        return 0;
    }

    #[\Override]
    public function scaleImage(int $width, int $height, bool $bestfit = false): bool
    {
        if(!is_null($this->image))
        {
            return $this->image->scaleImage($width,$height,$bestfit);
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
    public function padImage(
            int $targetWidth,
            int $targetHeight,
            array $background = [255, 255, 255]
    ): bool
    {


        try
        {
            if (!$this->image instanceof \Imagick)
            {
                throw new \RuntimeException('No Imagick image loaded.');
            }

            $sourceWidth = $this->image->getImageWidth();
            $sourceHeight = $this->image->getImageHeight();

            $scale = min(
                    $targetWidth / $sourceWidth,
                    $targetHeight / $sourceHeight
            );


            $scale = min($scale, 1.0);
            

            $newWidth = (int) round($sourceWidth * $scale);
            $newHeight = (int) round($sourceHeight * $scale);

            $resized = clone $this->image;

            if ($newWidth !== $sourceWidth || $newHeight !== $sourceHeight)
            {
                $resized->resizeImage(
                        $newWidth,
                        $newHeight,
                        \Imagick::FILTER_LANCZOS,
                        1
                );
            }

            $hasAlpha = count($background) === 4;

            if ($hasAlpha)
            {
                $alpha = max(0, min(255, (int) $background[3]));
                $opacity = 1 - ($alpha / 255);

                $bgColor = new \ImagickPixel(sprintf(
                                'rgba(%d,%d,%d,%F)',
                                $background[0],
                                $background[1],
                                $background[2],
                                $alpha / 255
                        ));
            }
            else
            {
                $bgColor = new \ImagickPixel(sprintf(
                                'rgb(%d,%d,%d)',
                                $background[0],
                                $background[1],
                                $background[2]
                        ));
            }

            $canvas = new \Imagick();
            $canvas->newImage($targetWidth, $targetHeight, $bgColor);

            if ($hasAlpha)
            {
                $canvas->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
                $resized->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
            }

            $left = (int) floor(($targetWidth - $newWidth) / 2);
            $top = (int) floor(($targetHeight - $newHeight) / 2);

            $canvas->compositeImage(
                    $resized,
                    \Imagick::COMPOSITE_OVER,
                    $left,
                    $top
            );

            $canvas->setImageFormat($this->image->getImageFormat());

            $this->image = $canvas;

            return true;
        } catch (\Throwable $e)
        {
            $this->setError('Pad image failed: ' . $e->getMessage());
            return false;
        }
    }

    #[\Override]
    public function compositeImage(mixed $image,int $composite, int $x, int $y, ?int $channel=null): bool
    {
        if(is_null($channel))
        {
            $channel = Imagick::CHANNEL_DEFAULT;
        }     
        $channelValues=[0,1,2,4,8,15,32,47,64,128,256,134217719,134217727];   
        if(!array_key_exists($channel, $channelValues))
        {
            $channel = Imagick::CHANNEL_DEFAULT;
        }
        
        
        if($composite<0 || $composite>67)
        {
            $composite=0;
        }
        
        
        return $this->image->compositeImage($image,$composite,$x, $y,$channel);
    }
    
    #[\Override]
    public function transformColorSpace(int $colorSpace): bool
    {
        $the_colorSpace=match($colorSpace)
        {
            self::COLORSPACE_SRGB => Imagick::COLORSPACE_SRGB,
            self::COLORSPACE_GRAY => Imagick::COLORSPACE_SRGB,
            self::COLORSPACE_BITONAL => self::COLORSPACE_BITONAL,
            self::COLORSPACE_CMYK => Imagick::COLORSPACE_SRGB,
            default => Imagick::COLORSPACE_SRGB
        };
        
        if($the_colorSpace==self::COLORSPACE_BITONAL)
        {
            return $this->image->thresholdImage(0.5 * \Imagick::getQuantum());
        }
        else
        {
            return $this->image->transformImageColorspace($the_colorSpace);
        }
    }    

    
    #[\Override]
    public function flattenImages(): bool
    {
        if(!is_null($this->image))
        {
            $this->image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $this->image=$this->image->flattenImages();
            return true;  
        }
        return false;          
    }
    
    #[\Override]
    public function cropImage(int $width, int $height,int $x, int $y): bool
    {
        if (!is_null($this->image))
        {
            try
            {
                //echo "<br />CROP width = ".intval($width)."// height= ". intval($height)."// x = ".$x."// y = ".$y;
                // Crop the image in place
                $result = $this->image->cropImage($width, $height, $x, $y);

                if ($result)
                {
                    // Reset the image canvas to reflect the new dimensions
                    $this->image->setImagePage(0, 0, 0, 0);
                    return true;
                }
                else
                {
                    $this->setError("Imagick cropImage returned false.");
                    return false;
                }
            } catch (\ImagickException $e)
            {
                $this->setError("Imagick crop failed: ".$e->getMessage());
                return false;
            }
        }

        $this->setError("No image loaded.");
        return false;
    }

    #[\Override]
    public function freeMemory(): bool
    {
        if(!is_null($this->image))
        {
            return $this->image->destroy(); 
        }
        return false;             
    }
 
    
    #[\Override]
    public function writeImage(string $dst, string $format=""): bool
    {
        //echo "Init :: image->writeImage ".$dst;
        if(!is_null($this->image))
        {
            if($this->getStripMetadata())
            {
                $this->image->stripImage();
            }
            $this->image->setImageCompression(Imagick::COMPRESSION_JPEG);
            // Set compression level (1 lowest quality, 100 highest quality)
            $this->image->setImageCompressionQuality(90);
            //echo "image->writeImage ".$dst;
            return $this->image->writeImage($dst);
        }
        return false;
    }

    #[\Override]
    public function toBuffer(): string
    {
        if(!is_null($this->image))
        {
            return $this->image->getImageBlob();
        }        
        return "";
    }
    
    #[\Override]
    public function flopImage(): bool
    {
        if (!is_null($this->image))
        {
            try
            {
                return $this->image->flopImage();
            } 
            catch (\ImagickException $e)
            {
                $this->setError("Imagick flop failed: ".$e->getMessage());
            }
        }
        return false;
    }
    
        #[\Override]
    public function rotateImage(mixed $angle=0,mixed $colorBackground=""): bool
    {
        if (!is_null($this->image))
        {
            try
            {
                if(!empty($colorBackground))
                {
                    $background = new \ImagickPixel($colorBackground);
                    return $this->image->rotateImage($background,$angle);
                }
                else
                {
                    return $this->image->rotateImage("",$angle);
                }
            } 
            catch (\ImagickException $e)
            {
                $this->setError("Imagick flop failed: ".$e->getMessage());
            }
        }
        return false;
    }   
    
    #[\Override]
    public function setFormat(mixed $format): bool
    {
        if(!is_null($this->image))
        {
            if($this->image->setFormat($format))
            {
                $this->setThe_format($format);
                return true;
            }
        }
        return false;
    }    

}