<?php

/**
 * Image class
 *
 * Luki framework
 * Date 7.3.2010
 *
 * @version 2.1.1
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Image class
 *
 * @package Luki
 *
 */
class Image
{

    private $_defaultWidth = 800;
    private $_defaultHight = 600;
    private $_defaultWattermarkText = array( 'R' => 255, 'G' => 255, 'B' => 255, 'ALPHA' => 80 );
    private $_defaultSquareColor = array( 'R' => 255, 'G' => 255, 'B' => 255, 'ALPHA' => 80 );
    private $_imageTypes = array( '1' => 'IMAGETYPE_GIF', '2' => 'IMAGETYPE_JPEG', '3' => 'IMAGETYPE_PNG', '4' => 'IMAGETYPE_SWF',
      '5' => 'IMAGETYPE_PSD', '6' => 'IMAGETYPE_BMP', '7' => 'IMAGETYPE_TIFF_II', '8' => 'IMAGETYPE_TIFF_MM', '9' => 'IMAGETYPE_JPC',
      '10' => 'IMAGETYPE_JP2', '11' => 'IMAGETYPE_JPX', '12' => 'IMAGETYPE_JB2', '13' => 'IMAGETYPE_SWC', '14' => 'IMAGETYPE_IFF',
      '15' => 'IMAGETYPE_WBMP', '16' => 'IMAGETYPE_XBM' );
    private $_supportedTypes = array( 'IMAGETYPE_GIF' => 'image/gif', 'IMAGETYPE_JPEG' => 'image/jpeg', 'IMAGETYPE_PNG' => 'image/png' );
    private $_imageFile;
    private $_realProperties;
    private $_imageType = '';
    private $_image;

    function __construct($file = '')
    {
        if ( !empty($file) ) {
            $this->open($file);
        } else {
            $this->_create();
        }

        unset($file);
    }

    public function open($file)
    {
        if ( is_file($file) ) {
            $this->_imageFile = $file;
            $this->_getImageProperties();

            if ( $this->_isSupportedType() ) {
                $this->_readFile();
            }
        }
    }

    public function show()
    {
        header("Content-type: " . $this->_supportedTypes[$this->_imageType], TRUE);
        $isShowed = $this->_outputImage();

        return $isShowed;
    }

    public function resizeByWidth($width)
    {
        if ( strpos($width, '%') > 0 ) {
            $ratio = ((int) $width / 100);
            $newWidth = floor($this->_realProperties['width'] * $ratio);
        } else {
            $newWidth = (int) $width;
        }

        $ratio = ($newWidth / $this->_realProperties['width']);
        $newHeight = floor($this->_realProperties['height'] * $ratio);
        $this->_resize($newWidth, $newHeight);

        unset($width, $ratio, $newWidth, $newHeight);
    }

    public function resizeByHeight($height)
    {
        if ( strpos($height, '%') > 0 ) {
            $ratio = ((int) $height / 100);
            $newHeight = floor($this->_realProperties['height'] * $ratio);
        } else {
            $newHeight = (int) $height;
        }

        $ratio = ($newHeight / $this->_realProperties['height']);
        $newWidth = floor($this->_realProperties['width'] * $ratio);

        $this->_resize($newWidth, $newHeight);

        unset($height, $ratio, $newHeight, $newWidth);
    }

    public function resizeToMax($width, $height)
    {
        if ( $this->_realProperties['height'] > $this->_realProperties['width'] ) {
            $ratio = ($height / $this->_realProperties['height']);
            $newHeight = $height;
            $newWidth = floor($this->_realProperties['width'] * $ratio);

            if ( $newWidth > $width ) {
                $ratio = ($width / $newWidth);
                $newWidth = $width;
                $newHeight = floor($newHeight * $ratio);
            }
        } else {
            $ratio = ($width / $this->_realProperties['width']);
            $newWidth = $width;
            $newHeight = floor($this->_realProperties['height'] * $ratio);

            if ( $newHeight > $height ) {
                $ratio = ($height / $newHeight);
                $newHeight = $height;
                $newWidth = floor($newWidth * $ratio);
            }
        }

        $this->_resize($newWidth, $newHeight);

        unset($width, $height, $ratio, $newWidth, $newHeight);
    }

    public function resizeTo($width, $height)
    {
        $ratioWidth = imagesx($this->_image) / $width;
        $ratioHeight = imagesy($this->_image) / $height;

        if ( $ratioHeight < $ratioWidth ) {
            $this->resizeByHeight($height);
            $this->cropToWidth($width);
        } else {
            $this->resizeByWidth($width);
            $this->cropToHeight($height);
        }

        unset($width, $height, $ratioWidth, $ratioHeight);
    }

    public function cropToWidth($width)
    {
        $actualWidth = imagesx($this->_image);
        $actualHeight = imagesy($this->_image);
        $diff = $actualWidth - $width;

        $newImage = imagecreatetruecolor($width, $actualHeight);
        $newImage = $this->_makeTransparent($newImage);

        imagecopyresampled($newImage, $this->_image, 0, 0, floor($diff / 2), 0, $width + $diff, $actualHeight, $actualWidth, $actualHeight);
        $this->_image = $newImage;

        unset($width, $actualWidth, $actualHeight, $diff, $newImage);
    }

    public function cropToHeight($height)
    {
        $actualWidth = imagesx($this->_image);
        $actualHeight = imagesy($this->_image);
        $diff = $actualHeight - $height;

        $newImage = imagecreatetruecolor($actualWidth, $height);
        $newImage = $this->_makeTransparent($newImage);

        imagecopyresampled($newImage, $this->_image, 0, 0, 0, floor($diff / 2), $actualWidth, $height + $diff, $actualWidth, $actualHeight);
        $this->_image = $newImage;

        unset($height, $actualWidth, $actualHeight, $diff, $newImage);
    }

    /**
     * Add watermark to image
     * @param string $sText
     * @param int $nPositionX
     * @param int $nPositionY
     * @param array $aColor
     * @param int $nSize
     * @param int $nAngle
     */
    public function setWatterMark($sText = '', $nPositionX = 0, $nPositionY = 0, $aColor = array(), $nSize = 40, $nAngle = 0)
    {
        # Check color
        if ( empty($aColor) ) {
            $aColor = $this->_defaultWattermarkText;
        }

        # Set text color transparent
        if ( $aColor['ALPHA'] > 0 ) {
            $cTextColor = imagecolorallocatealpha($this->_image, $aColor['R'], $aColor['G'], $aColor['B'], $aColor['ALPHA']);
        } else {
            $cTextColor = imagecolorallocate($this->_image, $aColor['R'], $aColor['G'], $aColor['B']);
        }

        # Generate text
        imagettftext($this->_image, $nSize, $nAngle, $nPositionX, $nPositionY, $cTextColor, FONTS_DIR . 'FreeSansBold.ttf', $sText);
    }

    public function saveAs($newName)
    {
        $isSaved = $this->_outputImage($newName);

        unset($newName);
        return $isSaved;
    }

    /**
     * Add rectangle to image
     * 
     * @param int $nTop
     * @param int $nRight
     * @param int $nBottom
     * @param int $nLeft
     * @param array $aColor
     */
    public function squareImage($nTop = 0, $nRight = 0, $nBottom = 0, $nLeft = 0, $aColor = array())
    {
        # Check color
        if ( empty($aColor) ) {
            $aColor = $this->_defaultSquareColor;
        }

        if ( $aColor['ALPHA'] > 0 ) {
            imagefilledrectangle($this->_image, $nRight, $nTop, $nLeft, $nBottom, imagecolorallocatealpha($this->_image, $aColor['R'], $aColor['G'], $aColor['B'], $aColor['ALPHA']));
        } else {
            imagefilledrectangle($this->_image, $nRight, $nTop, $nLeft, $nBottom, imagecolorallocate($this->_image, $aColor['R'], $aColor['G'], $aColor['B']));
        }
    }

    private function _readFile()
    {
        switch ( $this->_imageType ) {
            case 'IMAGETYPE_GIF':
                $this->_image = imagecreatefromgif($this->_imageFile);
                break;
            case 'IMAGETYPE_JPEG':
                $this->_image = imagecreatefromjpeg($this->_imageFile);
                break;
            case 'IMAGETYPE_PNG':
                $this->_image = imagecreatefrompng($this->_imageFile);
                break;
            default:
                $this->_image = imagecreatefromstring(file_get_contents($this->_imageFile));
        }
    }

    private function _outputImage($newImageName = NULL)
    {
        $isShowed = FALSE;

        switch ( $this->_realProperties['type'] ) {
            case 1:
                $isShowed = imagegif($this->_image, $newImageName);
                break;
            case 2:
                $isShowed = imagejpeg($this->_image, $newImageName, 95);
                break;
            case 3:
            default:
                $isShowed = imagepng($this->_image, $newImageName, 9);
        }

        imagedestroy($this->_image);

        unset($newImageName);
        return $isShowed;
    }

    private function _getImageProperties()
    {
        $properties = getimagesize($this->_imageFile);

        $this->_realProperties = array( 'width' => $properties[0]
          , 'height' => $properties[1]
          , 'type' => $properties[2]
          , 'string' => $properties[3]
          , 'bits' => $properties['bits']
          , 'mime' => $properties['mime']
        );

        if ( in_array($this->_realProperties['type'], array_keys($this->_imageTypes)) ) {
            $this->_imageType = $this->_imageTypes[$this->_realProperties['type']];
        }

        unset($properties);
    }

    private function _isSupportedType()
    {
        $supported = array_keys($this->_supportedTypes);
        $isSupported = FALSE;

        if ( in_array($this->_imageType, $supported) ) {
            $isSupported = TRUE;
        }

        unset($supported);
        return $isSupported;
    }

    private function _create()
    {
        $this->_imageType = 3;
        $this->_realProperties = array( 'width' => $this->_defaultWidth
          , 'height' => $this->_defaultHight
          , 'type' => $this->_imageType
          , 'string' => 'width="' . $this->_defaultWidth . '" height="' . $this->_defaultHight . '"'
          , 'bits' => 8
          , 'mime' => 'image/png'
        );
        $this->_image = imagecreate($this->_defaultWidth, $this->_defaultHight);
        imagecolorallocate($this->_image, 255, 255, 2550);
    }

    private function _resize($width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        $image = $this->_makeTransparent($image);
        imagecopyresampled($image, $this->_image, 0, 0, 0, 0, $width, $height, $this->_realProperties['width'], $this->_realProperties['height']);
        $this->_image = $image;

        unset($width, $height, $image);
    }

    private function _makeTransparent($image)
    {
        if ( ($this->_imageType == 'IMAGETYPE_GIF') || ($this->_imageType == 'IMAGETYPE_PNG') ) {
            $trnprt_indx = imagecolortransparent($this->_image);
            if ( $trnprt_indx >= 0 ) {
                $trnprt_color = imagecolorsforindex($this->_image, $trnprt_indx);
                $trnprt_indx = imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($image, 0, 0, $trnprt_indx);
                imagecolortransparent($image, $trnprt_indx);
            } elseif ( $this->_imageType == 'IMAGETYPE_PNG' ) {
                imagealphablending($image, false);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $color);
                imagesavealpha($image, true);
            }
        }

        unset($trnprt_indx, $trnprt_color, $color);
        return $image;
    }

}

# End of file