<?php

/**
 * Xml Log Writer
 *
 * Luki framework
 * Date 16.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Log\Writer;

use Luki\Log\Writer\basicInterface;

/**
 * Xml Log Writer
 * 
 * @package Luki
 */
class Xml implements basicInterface
{

    private $_fileName = NULL;
    private $_content = NULL;

    public function __construct($fileName = '')
    {
        $this->_fileName = $fileName;

        if ( is_file($fileName) ) {
            $this->_content = new \SimpleXMLElement($fileName, LIBXML_NOERROR, TRUE);
        } else {
            $fileName = '<?xml version="1.0" encoding="UTF-8"?><items></items>';
            $this->_content = new \SimpleXMLElement($fileName);
        }

        unset($fileName);
    }

    public function __destruct()
    {
        file_put_contents($this->_fileName, $this->_content->asXML());
    }

    public function Write($content)
    {
        if ( is_array($content) ) {
            $item = $this->_content->addChild('item');

            foreach ( $content as $key => $value ) {
                $item->addChild($key, $value);
            }
        } else {
            $this->_content->addChild('item', $content);
        }

        unset($content, $key, $value);
    }

}

# End of file