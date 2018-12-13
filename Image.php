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

class Image
{
    private $defaultWidth          = 800;
    private $defaultHight          = 600;
    private $defaultWattermarkText = array('R' => 255, 'G' => 255, 'B' => 255, 'ALPHA' => 80);
    private $defaultSquareColor    = array('R' => 255, 'G' => 255, 'B' => 255, 'ALPHA' => 80);
    private $imageTypes            = array('1'  => 'IMAGETYPE_GIF', '2'  => 'IMAGETYPE_JPEG', '3'  => 'IMAGETYPE_PNG', '4'  => 'IMAGETYPE_SWF',
        '5'  => 'IMAGETYPE_PSD', '6'  => 'IMAGETYPE_BMP', '7'  => 'IMAGETYPE_TIFF_II', '8'  => 'IMAGETYPE_TIFF_MM', '9'  => 'IMAGETYPE_JPC',
        '10' => 'IMAGETYPE_JP2', '11' => 'IMAGETYPE_JPX', '12' => 'IMAGETYPE_JB2', '13' => 'IMAGETYPE_SWC', '14' => 'IMAGETYPE_IFF',
        '15' => 'IMAGETYPE_WBMP', '16' => 'IMAGETYPE_XBM');
    private $supportedTypes        = array('IMAGETYPE_GIF' => 'image/gif', 'IMAGETYPE_JPEG' => 'image/jpeg', 'IMAGETYPE_PNG' => 'image/png');
    private $imageFile;
    private $realProperties;
    private $imageType             = '';
    private $image;
    private $quaity                = 100;

    function __construct($file = '')
    {
        if (!empty($file)) {
            $this->open($file);
        } else {
            $this->create();
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
        if (is_file($file)) {
            $this->imageFile = $file;
            $this->getImageProperties();

            if ($this->isSupportedType()) {
                $this->readFile();
            }
        }
    }

    public function show()
    {
        header("Content-type: ".$this->getType(), true);
        $isShowed = $this->outputImage();

        return $isShowed;
    }

    public function getType()
    {
        $type = $this->supportedTypes[$this->imageType];

        return $type;
    }

    public function resizeByWidth($width)
    {
        if (strpos($width, '%') > 0) {
            $ratio    = ((int) $width / 100);
            $newWidth = floor($this->realProperties['width'] * $ratio);
        } else {
            $newWidth = (int) $width;
        }

        $ratio     = ($newWidth / $this->realProperties['width']);
        $newHeight = floor($this->realProperties['height'] * $ratio);
        $this->resize($newWidth, $newHeight);

        return $this;
    }

    public function resizeByHeight($height)
    {
        if (strpos($height, '%') > 0) {
            $ratio     = ((int) $height / 100);
            $newHeight = floor($this->realProperties['height'] * $ratio);
        } else {
            $newHeight = (int) $height;
        }

        $ratio    = ($newHeight / $this->realProperties['height']);
        $newWidth = floor($this->realProperties['width'] * $ratio);

        $this->resize($newWidth, $newHeight);

        return $this;
    }

    public function resizeToMax($width, $height)
    {
        if ($this->realProperties['height'] > $this->realProperties['width']) {
            $ratio     = ($height / $this->realProperties['height']);
            $newHeight = $height;
            $newWidth  = floor($this->realProperties['width'] * $ratio);

            if ($newWidth > $width) {
                $ratio     = ($width / $newWidth);
                $newWidth  = $width;
                $newHeight = floor($newHeight * $ratio);
            }
        } else {
            $ratio     = ($width / $this->realProperties['width']);
            $newWidth  = $width;
            $newHeight = floor($this->realProperties['height'] * $ratio);

            if ($newHeight > $height) {
                $ratio     = ($height / $newHeight);
                $newHeight = $height;
                $newWidth  = floor($newWidth * $ratio);
            }
        }

        $this->resize($newWidth, $newHeight);

        return $this;
    }

    public function resizeTo($width, $height)
    {
        $ratioWidth  = imagesx($this->image) / $width;
        $ratioHeight = imagesy($this->image) / $height;

        if ($ratioHeight < $ratioWidth) {
            $this->resizeByHeight($height);
            $this->cropToWidth($width);
        } else {
            $this->resizeByWidth($width);
            $this->cropToHeight($height);
        }

        return $this;
    }

    public function cropToWidth($width)
    {
        $actualWidth  = imagesx($this->image);
        $actualHeight = imagesy($this->image);
        $diff         = $actualWidth - $width;

        $newImage = imagecreatetruecolor($width, $actualHeight);
        $newImage = $this->makeTransparent($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, floor($diff / 2), 0, $width + $diff, $actualHeight,
            $actualWidth, $actualHeight);
        $this->image = $newImage;

        return $this;
    }

    public function cropToHeight($height)
    {
        $actualWidth  = imagesx($this->image);
        $actualHeight = imagesy($this->image);
        $diff         = $actualHeight - $height;

        $newImage = imagecreatetruecolor($actualWidth, $height);
        $newImage = $this->makeTransparent($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, 0, floor($diff / 2), $actualWidth, $height + $diff,
            $actualWidth, $actualHeight);
        $this->image = $newImage;

        return $this;
    }

    public function setWatterMark($sText = '', $nPositionX = 0, $nPositionY = 0, $aColor = array(), $nSize = 40,
                                  $nAngle = 0)
    {
        if (empty($aColor)) {
            $aColor = $this->defaultWattermarkText;
        }

        if ($aColor['ALPHA'] > 0) {
            $cTextColor = imagecolorallocatealpha($this->image, $aColor['R'], $aColor['G'], $aColor['B'],
                $aColor['ALPHA']);
        } else {
            $cTextColor = imagecolorallocate($this->image, $aColor['R'], $aColor['G'], $aColor['B']);
        }

        imagettftext($this->image, $nSize, $nAngle, $nPositionX, $nPositionY, $cTextColor, FONTS_DIR.'FreeSansBold.ttf',
            $sText);

        return $this;
    }

    public function saveAs($newName, $type = '')
    {
        $isSaved = $this->outputImage($newName, $type);

        return $isSaved;
    }

    public function squareImage($nTop = 0, $nRight = 0, $nBottom = 0, $nLeft = 0, $aColor = array())
    {
        if (empty($aColor)) {
            $aColor = $this->defaultSquareColor;
        }

        if ($aColor['ALPHA'] > 0) {
            imagefilledrectangle($this->image, $nRight, $nTop, $nLeft, $nBottom,
                imagecolorallocatealpha($this->image, $aColor['R'], $aColor['G'], $aColor['B'], $aColor['ALPHA']));
        } else {
            imagefilledrectangle($this->image, $nRight, $nTop, $nLeft, $nBottom,
                imagecolorallocate($this->image, $aColor['R'], $aColor['G'], $aColor['B']));
        }

        return $this;
    }

    private function readFile()
    {
        switch ($this->imageType) {
            case 'IMAGETYPE_GIF':
                $this->image = imagecreatefromgif($this->imageFile);
                break;
            case 'IMAGETYPE_JPEG':
                $this->image = imagecreatefromjpeg($this->imageFile);
                break;
            case 'IMAGETYPE_PNG':
                $this->image = imagecreatefrompng($this->imageFile);
                break;
            default:
                $this->image = imagecreatefromstring(file_get_contents($this->imageFile));
        }
    }

    private function outputImage($newImageName = null, $type = '')
    {
        $isShowed = false;

        if (empty($type)) {
            $type = $this->realProperties['type'];
        }

        switch ($type) {
            case 1:
                $isShowed = imagegif($this->image, $newImageName);
                break;
            case 2:
                $isShowed = imagejpeg($this->image, $newImageName, $this->quaity);
                break;
            case 3:
            default:
                $isShowed = imagepng($this->image, $newImageName, round($this->quaity / 10));
        }

        imagedestroy($this->image);

        return $isShowed;
    }

    private function getImageProperties()
    {
        $properties = getimagesize($this->imageFile);

        $this->realProperties = array('width'  => $properties[0]
            , 'height' => $properties[1]
            , 'type'   => $properties[2]
            , 'string' => $properties[3]
            , 'bits'   => $properties['bits']
            , 'mime'   => $properties['mime']
        );

        if (in_array($this->realProperties['type'], array_keys($this->imageTypes))) {
            $this->imageType = $this->imageTypes[$this->realProperties['type']];
        }
    }

    private function isSupportedType()
    {
        $isSupported = in_array($this->imageType, array_keys($this->supportedTypes));

        return $isSupported;
    }

    private function create()
    {
        $this->imageType      = 3;
        $this->realProperties = array('width'  => $this->defaultWidth
            , 'height' => $this->defaultHight
            , 'type'   => $this->imageType
            , 'string' => 'width="'.$this->defaultWidth.'" height="'.$this->defaultHight.'"'
            , 'bits'   => 8
            , 'mime'   => 'image/png'
        );
        $this->image          = imagecreate($this->defaultWidth, $this->defaultHight);
        imagecolorallocate($this->image, 255, 255, 2550);
    }

    private function resize($width, $height)
    {
        $image       = imagecreatetruecolor($width, $height);
        $image       = $this->makeTransparent($image);
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $width, $height, $this->realProperties['width'],
            $this->realProperties['height']);
        $this->image = $image;
    }

    private function makeTransparent($image)
    {
        if (($this->imageType == 'IMAGETYPE_GIF') || ($this->imageType == 'IMAGETYPE_PNG')) {
            $trnprt_indx = imagecolortransparent($this->image);
            if ($trnprt_indx >= 0) {
                $trnprt_color = imagecolorsforindex($this->image, $trnprt_indx);
                $trnprt_indx  = imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'],
                    $trnprt_color['blue']);
                imagefill($image, 0, 0, $trnprt_indx);
                imagecolortransparent($image, $trnprt_indx);
            } elseif ($this->imageType == 'IMAGETYPE_PNG') {
                imagealphablending($image, false);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $color);
                imagesavealpha($image, true);
            }
        }

        return $image;
    }

    public function setQuality($quality)
    {
        $this->quaity = (int) $quality;

        return $this;
    }

    public function getQuality()
    {
        return $this->quaity;
    }
}