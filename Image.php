<?php
/**
 * Image class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Image
 * @filesource
 */

namespace Luki;

use Luki\Image\Filter\BasicInterface as FilterInterface;
use Luki\Image\Convolution\BasicInterface as ConvolutionInterface;

class Image
{
    const GIF = 'image/gif';
    const JPG = 'image/jpeg';
    const PNG = 'image/png';

    private $file;
    private $image;
    private $type;
    private $exif;
    private $font;
    private $quality   = 100;
    private $imageType = array(self::GIF, self::JPG, self::PNG);
    private $color     = array(255, 255, 255, 80);

    function __construct($file = '')
    {
        if (!empty($file)) {
            $this->open($file);
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function open($file)
    {
        if ($this->checkFile($file)) {
            $this->read($file);
        }

        return $this;
    }

    public function createPng($width, $height)
    {
        $this->image = $this->create($width, $height, true);
        $this->type  = self::PNG;

        return $this;
    }

    public function createJpg($width, $height)
    {
        $this->image = $this->create($width, $height);
        $this->type  = self::JPG;

        return $this;
    }

    public function createGif($width, $height)
    {
        $this->image = $this->create($width, $height);
        $this->type  = self::GIF;

        return $this;
    }

    public function fill($red = 0, $green = 0, $blue = 0)
    {
        $color = imagecolorallocate($this->image, $red, $green, $blue);
        imagefill($this->image, 0, 0, $color);

        return $this;
    }

    public function show()
    {
        header("Content-type: ".$this->type, true);
        $result = $this->outputImage();

        return $result;
    }

    public function saveAs($name, $type = '')
    {
        $result = $this->outputImage($name, $type);

        return $result;
    }

    public function setQuality($quality)
    {
        $this->quality = (int) $quality;

        return $this;
    }

    public function getQuality()
    {
        return $this->quality;
    }

    public function getExif()
    {
        return $this->exif;
    }

    public function rotate($dg)
    {
        if ($dg > 0 and $dg < 360) {
            $bgColor = imageColorAllocateAlpha($this->image, 0, 0, 0, 127);
            $rotated = imagerotate($this->image, $dg, $bgColor);
            if ($rotated !== false) {
                imagedestroy($this->image);
                $this->image = $rotated;
            }

            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
        }

        return $this;
    }

    public function autoRotate()
    {
        if (!empty($this->exif) and ! empty($this->exif['Orientation'])) {
            switch ($this->exif['Orientation']) {
                case 8:
                    $this->rotate(90);
                    break;
                case 3:
                    $this->rotate(180);
                    break;
                case 6:
                    $this->rotate(270);
                    break;
            }
        }

        return $this;
    }

    public function autoCrop()
    {
        $cropped = imagecropauto($this->image, IMG_CROP_TRANSPARENT);
        if ($cropped !== false) {
            imagedestroy($this->image);
            $this->image = $cropped;
        }

        return $this;
    }

    public function resizeTo($width, $height)
    {
        list($realWidth, $realHeight) = $this->getSize();
        $ratioWidth  = $realWidth / $width;
        $ratioHeight = $realHeight / $height;

        if ($ratioHeight < $ratioWidth) {
            $this->resizeByHeight($height);
            $this->cropToWidth($width);
        } else {
            $this->resizeByWidth($width);
            $this->cropToHeight($height);
        }

        return $this;
    }

    public function resizeToMax($width, $height)
    {
        list($realWidth, $realHeight) = $this->getSize();
        if ($realHeight > $realWidth) {
            $ratio     = ($height / $realHeight);
            $newHeight = $height;
            $newWidth  = floor($realWidth * $ratio);

            if ($newWidth > $width) {
                $ratio     = ($width / $newWidth);
                $newWidth  = $width;
                $newHeight = floor($newHeight * $ratio);
            }
        } else {
            $ratio     = ($width / $realWidth);
            $newWidth  = $width;
            $newHeight = floor($realHeight * $ratio);

            if ($newHeight > $height) {
                $ratio     = ($height / $newHeight);
                $newHeight = $height;
                $newWidth  = floor($newWidth * $ratio);
            }
        }

        $this->resize($newWidth, $newHeight);

        return $this;
    }

    public function resizeByWidth($width)
    {
        list($realWidth, $realHeight) = $this->getSize();

        if (strpos($width, '%') > 0) {
            $newWidth = floor($realWidth * ((int) $width / 100));
        } else {
            $newWidth = (int) $width;
        }

        $ratio     = $newWidth / $realWidth;
        $newHeight = floor($realHeight * $ratio);

        $this->resize($newWidth, $newHeight);

        return $this;
    }

    public function resizeByHeight($height)
    {
        list($realWidth, $realHeight) = $this->getSize();

        if (strpos($height, '%') > 0) {
            $newHeight = floor($realHeight * ((int) $height / 100));
        } else {
            $newHeight = (int) $height;
        }

        $ratio    = $newHeight / $realHeight;
        $newWidth = floor($realWidth * $ratio);

        $this->resize($newWidth, $newHeight);

        return $this;
    }

    public function cropToWidth($width)
    {
        list($realWidth, $realHeight) = $this->getSize();
        $diff   = $realWidth - $width;
        $image  = $this->create($width, $realHeight, true);
        $newPos = floor($diff / 2);

        imagecopyresampled($image, $this->image, 0, 0, $newPos, 0, $width + $diff, $realHeight, $realWidth, $realHeight);

        $this->image = $image;

        return $this;
    }

    public function cropToHeight($height)
    {
        list($realWidth, $realHeight) = $this->getSize();
        $diff   = $realHeight - $height;
        $image  = $this->create($realWidth, $height, true);
        $newPos = floor($diff / 2);

        imagecopyresampled($image, $this->image, 0, 0, 0, $newPos, $realWidth, $height + $diff, $realWidth, $realHeight);

        $this->image = $image;

        return $this;
    }

    public function fitTo($width, $height)
    {
        $this->resizeToMax($width, $height);

        list($realWidth, $realHeight) = $this->getSize();
        $newY = ($height > $realHeight) ? floor(($height - $realHeight) / 2) : 0;
        $newX = ($width > $realWidth) ? floor(($width - $realWidth) / 2) : 0;

        $image = $this->create($width, $height, true);

        imagecopymerge($image, $this->image, $newX, $newY, 0, 0, $realWidth, $realHeight, 100);

        $this->image = $image;

        return $this;
    }

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function getSize()
    {
        return array(imagesx($this->image), imagesy($this->image));
    }

    public function crop($posX, $posY, $width, $height)
    {
        $croped = imagecrop($this->image, ['x' => $posX, 'y' => $posY, 'width' => $width, 'height' => $height]);
        if ($croped !== false) {
            imagedestroy($this->image);
            $this->image = $croped;
        }

        return $this;
    }

    public function setFont($font)
    {
        $this->font = $font;

        return $this;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function setWatterMark($text, $posX = 0, $posY = 0, $color = array(), $size = 40, $angle = 0)
    {
        if (empty($color)) {
            $color = $this->color;
        }

        list($red, $green, $blue, $alpha) = $color;

        if ($alpha > 0) {
            $textColor = imagecolorallocatealpha($this->image, $red, $green, $blue, $alpha);
        } else {
            $textColor = imagecolorallocate($this->image, $red, $green, $blue);
        }

        imagettftext($this->image, $size, $angle, $posX, $posY, $textColor, $this->font, $text);

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function flipHorizontal()
    {
        imageflip($this->image, IMG_FLIP_HORIZONTAL);

        return $this;
    }

    public function flipVertical()
    {
        imageflip($this->image, IMG_FLIP_VERTICAL);

        return $this;
    }

    public function flipBoth()
    {
        imageflip($this->image, IMG_FLIP_BOTH);

        return $this;
    }

    public static function updateExtension($file, $type)
    {
        $path    = pathinfo($file);
        $newName = '';

        if (!empty($path['dirname']) and '.' !== $path['dirname']) {
            $newName .= $path['dirname'].'/';
        }

        $newName .= $path['filename'];

        switch ($type) {
            case self::GIF:
                $newName .= '.gif';
                break;
            case self::JPG:
                $newName .= '.jpg';
                break;
            case self::PNG:
                $newName .= '.png';
                break;
        }

        return $newName;
    }

    public function setResolution($resX, $resY = '')
    {
        if (empty($resY)) {
            imageresolution($this->image, $resX);
        } else {
            imageresolution($this->image, $resX, $resY);
        }

        return $this;
    }

    public function getResolution()
    {
        return imageresolution($this->image);
    }

    public function setInterpolation($method = '')
    {
        imagesetinterpolation($this->image, $method);

        return $this;
    }

    public function filter(FilterInterface $filter)
    {
        $this->image = $filter->filter($this->image);

        return $this;
    }

    public function convolution(ConvolutionInterface $convolution)
    {
        $this->image = $convolution->convolution($this->image);

        return $this;
    }

    private function outputImage($file = null, $type = '')
    {
        $result = false;

        if (empty($type)) {
            $type = $this->type;
        }

        switch ($type) {
            case self::GIF:
                $result  = imagegif($this->image, $file);
                break;
            case self::JPG:
                $result  = imagejpeg($this->image, $file, $this->quality);
                break;
            case self::PNG:
                $quality = ($this->quality - 100) / 11.111111;
                $quality = round(abs($quality));
                $result  = imagepng($this->image, $file, $quality);
        }

        imagedestroy($this->image);

        return $result;
    }

    private function checkFile($file)
    {
        $result = false;

        if (is_file($file) and is_readable($file) and in_array($this->getMime($file), $this->imageType)) {
            $result = true;
        }

        return $result;
    }

    private function getMime($file)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
    }

    private function read($file)
    {
        $this->file = $file;
        $this->type = $this->getMime($file);

        switch ($this->type) {
            case self::GIF:
                $this->image = imagecreatefromgif($file);
                break;
            case self::JPG:
                $this->exif  = exif_read_data($file);
                $this->image = imagecreatefromjpeg($file);
                break;
            case self::PNG:
                $this->image = imagecreatefrompng($file);
                break;
        }
    }

    private function create($width, $height, $alpha = false)
    {
        if ($alpha) {
            $image = imagecreatetruecolor($width, $height);
            imagesavealpha($image, true);
            $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $color);
        } else {
            $image = imagecreate($width, $height);
        }

        return $image;
    }

    private function resize($width, $height)
    {
        $image = $this->create($width, $height, true);
        list($realWidth, $realHeight) = $this->getSize();
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $width, $height, $realWidth, $realHeight);

        $this->image = $image;
    }
}