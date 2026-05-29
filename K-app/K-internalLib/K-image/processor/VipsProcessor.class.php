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
/*
 * 
 * 
Add to php.ini
ffi.enable=true
zend.max_allowed_stack_size=-1
 */
require_once __ROOT__.'/K-composer/vendor/autoload.php';  
use Jcupitt\Vips;
class VipsProcessor extends AbstractKimageProcessor
{

    private ?Vips\Image $image = null;
      
    
    
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
        
        $status=true;
        try
        {
            // create new Imagick object
            $this->image = Vips\Image::newFromFile($this->getSrc(),[
                        'access' => 'sequential',
                        'unlimited' => true,
                    ]);
        }
        catch(Throwable $e)
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
        $version_info = [Vips\Config::version()];//"";// \Imagick::getVersion();     
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
            return $this->image->height;
        }
        return 0;
    }

    #[\Override]
    public function getWidth(): int
    {
        if(!is_null($this->image))
        {
            return $this->image->width;
        }       
        return 0;
    }

    #[\Override]
    public function scaleImage(int $width, int $height, bool $bestfit = false): bool
    {
        if($width<=0&&$height<=0)
        {
            return false;
        }        
        
        if(!is_null($this->image))
        {
            $scaleX = $width / $this->image->width;
            $scaleY = $height / $this->image->height;
            $scale = min($scaleX, $scaleY);
            $this->image=$this->image->resize($scale);
//            if(!is_null($this->image))
//            {
                return true;
//            }
        }
        return false;
    }
    
    
    /**
     * @param int $targetWidth
     * @param int $targetHeight
     * @param array<int,int> $background
     * @return bool
     */
    public function padImage(
            int $targetWidth,
            int $targetHeight,
            array $background = [255, 255, 255]): bool
    {
        try
        {
            $image = $this->image;

            // Optional: normalize to sRGB for predictable RGB background behavior
            if ($image->bands !== 3 && $image->bands !== 4)
            {
                $image = $image->colourspace(\Jcupitt\Vips\Interpretation::SRGB);
            }
            
            if(count($background)>3)
            {
                $background=[$background[0],$background[1],$background[2]];
                $image = $image->colourspace(\Jcupitt\Vips\Interpretation::SRGB);
            }

            // Compute scale to fit inside target box while preserving aspect ratio
            $scale = min(
                    $targetWidth / $image->width,
                    $targetHeight / $image->height
            );

            // Prevent enlargement unless explicitly allowed

            $scale = min($scale, 1.0);
            

            if ($scale !== 1.0)
            {
                $image = $image->resize($scale);
            }

            // Center position
            $left = (int) floor(($targetWidth - $image->width) / 2);
            $top = (int) floor(($targetHeight - $image->height) / 2);

            // Put image on background canvas
            $this->image = $image->embed(
                    $left,
                    $top,
                    $targetWidth,
                    $targetHeight,
                    [
                        'extend' => \Jcupitt\Vips\Extend::BACKGROUND,
                        'background' => $background
                    ]
            );

            return true;
        } catch (\Throwable $e)
        {
            $this->setError('Pad image failed: ' . $e->getMessage());
            return false;
        }
    }

    public function compositeImage(mixed $image, int $composite, int $x, int $y, ?int $channel=null): bool
    {

        try
        {
            // Ensure overlay has alpha
                if (!$image->hasAlpha())
            {
                    $image = $image->bandjoin(255); // Add full alpha
            }

            // Create transparent canvas the same size as the base image
            $canvas = \Jcupitt\Vips\Image::black($this->image->width, $this->image->height)->copy(['bands' => 4]);
                $canvas = $canvas->cast($image->interpretation); // Match color model
            // Embed overlay into the canvas at position (x, y)
                $overlayPadded = $image->embed($x, $y, $this->image->width, $this->image->height, [
                'extend' => \Jcupitt\Vips\Extend::BACKGROUND,
                'background' => [0, 0, 0, 0]
            ]);

            // Composite overlay onto base image
            $this->image = $this->image->composite([$overlayPadded], [\Jcupitt\Vips\BlendMode::OVER]);
            return true;
        } 
        catch (\Throwable $e)
        {
            $this->setError("Composite failed: ".$e->getMessage());
            return false;
        }     
        return false;
    }  
    
    #[\Override]
    public function flattenImages(): bool
    {
        if(!is_null($this->image))
        {
            try
            {
                $this->image = $this->image->flatten(['background' => [255, 255, 255] /*white*/ ]);
                /* @phpstan-ignore-next-line */
                if(!is_null($this->image))
                {
                    return true;
                }
            }
            catch (\Throwable $e)
            {
                $this->setError("flattenImages failed: ".$e->getMessage());
                return false;
            }
        }
        return false;          
    }
    
    #[\Override]
    public function cropImage(int $width, int $height,int $x, int $y): bool
    {
        if (is_null($this->image))
        {
            $this->setError("No image loaded.");
            return false;
        }

        try
        {
            $this->image = $this->image->crop($x, $y, $width, $height);
            return true;
        } catch (\Throwable $e)
        {
            $this->setError("Vips crop failed: ".$e->getMessage());
            return false;
        }
    }

    #[\Override]
    public function freeMemory(): bool
    {
        if(!is_null($this->image))
        {
            unset($this->image);
            return true;
        }
        return false;             
    }
 
    
    #[\Override]
    public function writeImage(string $dst,string $format=""): bool
    {
        //echo "Init :: image->writeImage ".$dst;
        if(!is_null($this->image))
        {
            if(!empty($format)&&$this->checkFormat($format))
            {
                $buffer = $this->image->writeToBuffer('.'.$format);
                file_put_contents($dst, $buffer);
                return file_exists($dst);
            }
            else
            {              
                //echo "after :: image->writeImage ".$dst;
                $this->image->writeToFile($dst);
                return file_exists($dst);
            }
        }
        return false;
    }
    
    #[\Override]
    public function setFormat(mixed $format): bool
    {
        if(!is_null($this->image)&&!empty($format))
        {
            $this->setThe_format($format);
            return true;
        }
        return false;
    } 
    
    #[\Override]
    public function rotateImage(mixed $angle=0,mixed $colorBackground=""): bool
    {
        if (is_null($this->image)) 
        {
            $this->setError("No image loaded.");
            return false;
        }

        try 
        {
            $this->image = $this->image->rotate($angle, [
                'background' => [0, 0, 0] // [255, 255, 255] // white
            ]);
            return true;
            
        } 
        catch (\Throwable $e) 
        {
            $this->setError("Vips rotate failed: " . $e->getMessage());
            return false;
        }
    }
    
    #[\Override]
    public function transformColorSpace(int $colorSpace): bool
    {
        if (is_null($this->image)) 
        {
            $this->setError("No image loaded.");
            return false;
        }

        $the_colorSpace=match($colorSpace)
        {
            self::COLORSPACE_SRGB => \Jcupitt\Vips\Interpretation::SRGB,
            self::COLORSPACE_GRAY => \Jcupitt\Vips\Interpretation::B_W,
            self::COLORSPACE_BITONAL => \Jcupitt\Vips\Interpretation::B_W,
            self::COLORSPACE_CMYK => \Jcupitt\Vips\Interpretation::CMYK,
            default => \Jcupitt\Vips\Interpretation::SRGB        
        };
       
        try 
        {
            $this->image = $this->image->colourspace($the_colorSpace);
            if($colorSpace==self::COLORSPACE_BITONAL)
            {
                $this->image = $this->image->more(128);
            }
            return true;
        } 
        catch (\Throwable $e) 
        {
            $this->setError("Vips colorspace transform failed: " . $e->getMessage());
            return false;
        }
    }    
    
    #[\Override]
    public function flopImage(): bool
    {
        if (is_null($this->image)) 
        {
            $this->setError("No image loaded.");
            return false;
        }

        try {
            $this->image = $this->image->flip(\Jcupitt\Vips\Direction::HORIZONTAL);
            return true;
        } 
        catch (\Throwable $e) 
        {
            $this->setError("Vips flop failed: " . $e->getMessage());
            return false;
        }
    }    
    
    #[\Override]
    public function toBuffer(): string
    {
        if(!is_null($this->image))
        {
            $format='.jpg';
            if(!empty($this->getThe_format()))
            {
                $format='.'.$this->getThe_format();
            }
            return $this->image->writeToBuffer($format);
        }        
        return "";
    }
}