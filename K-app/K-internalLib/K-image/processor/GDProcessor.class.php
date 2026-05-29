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
class GdProcessor extends AbstractKimageProcessor
{
    private mixed $image = null;
    private int $width = 0;
    private int $height = 0;     

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

        $info = getimagesize($this->getSrc());
        if ($info === false)
        {
            $this->setError("Could not read image information from ".$this->getSrc());
            return false;
        }
        
        [$this->width, $this->height, $type] = $info;

        try
        {
            switch ($type)
            {
                case IMAGETYPE_JPEG:
                    $this->setFormat(self::JPG);
                    $this->image = imagecreatefromjpeg($this->getSrc());
                    break;
                case IMAGETYPE_PNG:
                    $this->setFormat(self::PNG);
                    $this->image = imagecreatefrompng($this->getSrc());
                    break;
                case IMAGETYPE_GIF:
                    $this->setFormat(self::GIF);
                    $this->image = imagecreatefromgif($this->getSrc());
                    break;
                case IMAGETYPE_BMP:
                    $this->setFormat(self::BMP);
                    $this->image = imagecreatefrombmp($this->getSrc());
                    break;
                case IMAGETYPE_WEBP:
                    $this->setFormat(self::WEBP);
                    $this->image = imagecreatefromwebp($this->getSrc());
                    break;
                case IMAGETYPE_TIFF_II:
                    $this->openTifImage();
                    break; 
                case IMAGETYPE_TIFF_MM:
                    $this->openTifImage();
                    break;   
                default:
                    $this->setError("Unsupported image type.");
                    return false;
            }
            
            if(!is_null($this->image))
            {
                return true;
            }
            return false;
        } 
        catch (\Throwable $e)
        {
            $this->setError("Error When opending =>".$e->getMessage());
            return false;
        }
    }
    
    private function openTifImage() : bool
    {
        $status=false;
        $kimage=new KImage($this->getSrc(),KImage::IMAGICK_ENGINE);

        if($kimage->isImageReady())
        {
            $dest="/tmp/temp_image_".rand().".jpg";
            if($kimage->writeImage($dest))
            {
                $this->setFormat(self::JPG);
                $this->image = imagecreatefromjpeg($dest);
                unlink($dest);
                $status=true;
            }
            else
            {
                $this->setError("Unsupported image type.(2)");
            }
        }
        else
        {
            $this->setError("Unsupported image type.(3)");
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
    /**
     * 
     * @return array<string,mixed>
     */
    public function getEngineInformations(): array
    {
        //var_dump($this->image);
        $version='unknown';
        $gdInfo=gd_info();
        if(array_key_exists('GD Version', $gdInfo))
        {
            $version=strval($gdInfo['GD Version']);
        }
        return [
            'GD Version' => $version,
        ];
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
        return $this->height;
    }

    #[\Override]
    public function getWidth(): int
    {
        return $this->width;
    }

    #[\Override]
    public function scaleImage(int $width, int $height, bool $bestfit = false): bool
    {
        if (!is_null($this->image))
        {
            $scaleX = $width / $this->width;
            $scaleY = $height / $this->height;
            $scale = min($scaleX, $scaleY);
            
            $width=intval($scale*$this->width);
            $height=intval($scale*$this->height);
            
            $dst = imagecreatetruecolor($width, $height);
            if ($dst === false)
                return false;
            
   
            imagecopyresampled($dst, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
            imagedestroy($this->image);
            $this->image = $dst;
            $this->width = $width;
            $this->height = $height;
            return true;
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

            $sourceWidth = imagesx($this->image);
            $sourceHeight = imagesy($this->image);

            $scale = min(
                    $targetWidth / $sourceWidth,
                    $targetHeight / $sourceHeight
            );

            $newWidth = (int) round($sourceWidth * $scale);
            $newHeight = (int) round($sourceHeight * $scale);

            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

            if (!$canvas)
            {
                throw new \RuntimeException('Could not create GD canvas.');
            }

            $hasAlpha = count($background) === 4;

            if ($hasAlpha)
            {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);

                // GD alpha is 0 = opaque, 127 = transparent.
                // Your input alpha is 0 = transparent, 255 = opaque.
                $alpha = max(0, min(255, (int) $background[3]));
                $gdAlpha = 127 - (int) round(($alpha / 255) * 127);

                $bgColor = imagecolorallocatealpha(
                        $canvas,
                        (int) $background[0],
                        (int) $background[1],
                        (int) $background[2],
                        $gdAlpha
                );
            }
            else
            {
                $bgColor = imagecolorallocate(
                        $canvas,
                        (int) $background[0],
                        (int) $background[1],
                        (int) $background[2]
                );
            }

            if ($bgColor === false)
            {
                throw new \RuntimeException('Could not allocate background color.');
            }

            imagefilledrectangle(
                    $canvas,
                    0,
                    0,
                    $targetWidth,
                    $targetHeight,
                    $bgColor
            );

            $left = (int) floor(($targetWidth - $newWidth) / 2);
            $top = (int) floor(($targetHeight - $newHeight) / 2);

            if ($hasAlpha)
            {
                imagealphablending($canvas, true);
                imagesavealpha($canvas, true);

                imagealphablending($this->image, true);
                imagesavealpha($this->image, true);
            }

            $ok = imagecopyresampled(
                    $canvas,
                    $this->image,
                    $left,
                    $top,
                    0,
                    0,
                    $newWidth,
                    $newHeight,
                    $sourceWidth,
                    $sourceHeight
            );

            if (!$ok)
            {
                throw new \RuntimeException('GD imagecopyresampled failed.');
            }

            $this->image = $canvas;

            return true;
        } catch (\Throwable $e)
        {
            $this->setError('Pad image failed: ' . $e->getMessage());
            return false;
        }
    }

    #[\Override]
    public function compositeImage(mixed $image, int $composite, int $x, int $y, ?int $channel = null): bool
    {

        imagealphablending($this->image, true);
        imagesavealpha($this->image, true);

        imagealphablending($this->image, true);
        imagesavealpha($image, true);

        // Composite the overlay image onto the base
        $success = imagecopy($this->image, $this->image, $x, $y, 0, 0, imagesx($image),imagesy($image));

        return $success;

    }

    #[\Override]
    public function flattenImages(): bool
    {
        // GD does not support alpha flattening over a background directly.
        if (!is_null($this->image))
        {
            $flattened = imagecreatetruecolor($this->width, $this->height);
            $white = imagecolorallocate($flattened, 255, 255, 255);
            imagefill($flattened, 0, 0, $white);
            imagecopy($flattened, $this->image, 0, 0, 0, 0, $this->width, $this->height);
            imagedestroy($this->image);
            $this->image = $flattened;
            return true;
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

        // Ensure crop region is within bounds
        if ($x < 0 || $y < 0 || $x + $width > $this->width || $y + $height > $this->height)
        {
            $this->setError("Crop region is out of bounds.");
            return false;
        }

        // Crop the image
        $cropped = imagecrop($this->image, [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        ]);

        if ($cropped === false)
        {
            $this->setError("GD crop failed.");
            return false;
        }

        // Replace the original image with the cropped one
        imagedestroy($this->image);
        $this->image = $cropped;
        $this->width = $width;
        $this->height = $height;

        return true;
    }

    #[\Override]
    public function freeMemory(): bool
    {
        if (!is_null($this->image))
        {
            imagedestroy($this->image);
            $this->image = null;
            return true;
        }
        return false;
    }

    #[\Override]
    public function writeImage(string $dst, string $format = ""): bool
    {
        if(!is_null($this->image))
        {
            $ext = strtolower(pathinfo($dst, PATHINFO_EXTENSION));
            if ($this->getStripMetadata())
            {
                // GD has no metadata embedding, so nothing to strip
            }

            switch ($ext)
            {
                case 'jpg':
                case 'jpeg':
                    return imagejpeg($this->image, $dst, 90);
                case 'png':
                    return imagepng($this->image, $dst);
                case 'gif':
                    return imagegif($this->image, $dst);
                default:
                    $this->setError("Unsupported output format: $ext");
                    return false;
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
    public function flopImage(): bool
    {
        if (is_null($this->image)) 
        {
            $this->setError("No image loaded.");
            return false;
        }

        $width = imagesx($this->image);
        $height = imagesy($this->image);
        $flipped = imagecreatetruecolor($width, $height);

        if ($flipped === false) 
        {
            $this->setError("Failed to create GD image for flop.");
            return false;
        }

        for ($x = 0; $x < $width; $x++) 
        {
            imagecopy($flipped, $this->image, $width - $x - 1, 0, $x, 0, 1, $height);
        }

        imagedestroy($this->image);
        $this->image = $flipped;

        return true;
    } 
    
    #[\Override]
    public function rotateImage(mixed $angle=0,mixed $colorBackground=""): bool
    {
        if (is_null($this->image)) 
        {
            $this->setError("No image loaded.");
            return false;
        }

        $bgColor = imagecolorallocate($this->image, 255, 255, 255); // white background

        $rotated = imagerotate($this->image, -$angle, $bgColor); // Negative for clockwise

        if ($rotated === false) 
        {
            $this->setError("GD rotate failed.");
            return false;
        }

        imagedestroy($this->image);
        $this->image = $rotated;

        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);

        return true;
    }
    
    #[\Override]
    public function transformColorSpace(int $colorSpace): bool
    {
        if (is_null($this->image)) 
        {
            $this->setError("No image loaded.");
            return false;
        }
        
//        $the_colorSpace=match($colorSpace)
//        {
//            self::COLORSPACE_SRGB => 'rgb',
//            self::COLORSPACE_GRAY => 'gray',
//            default => null
//            //self::COLORSPACE_BITONAL,self::COLORSPACE_CMYK => null,
//        };
        
        if($colorSpace==self::COLORSPACE_GRAY)
        {
            imagefilter($this->image, IMG_FILTER_GRAYSCALE);
            return true;
        }
        elseif ($colorSpace==self::COLORSPACE_SRGB)
        {
            return true;
        }
        $this->setError("GD only supports grayscale/rgb conversion.");
        return false;
    }

    #[\Override]
    public function toBuffer(): string
    {
        //var_dump($this->image);
        if(!is_null($this->image))
        {
            $format = $this->getThe_format();
            ob_start();
            switch ($format) {
                case self::JPG:
                    imagejpeg($this->image, null, 90);
                    break;
                case self::PNG:
                    imagepng($this->image, null);
                    break;
                case self::GIF:
                    imagegif($this->image, null);
                    break;
                case self::WEBP:
                    imagewebp($this->image, null, 90);
                    break;
                default:
                    imagejpeg($this->image, null, 90);
                    break;
            }            
            return ob_get_clean();
        }
        return "";
    }
}