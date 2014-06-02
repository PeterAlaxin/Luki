<?php

/**
 * Config xml adapter
 *
 * Luki framework
 * Date 19.9.2012
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

namespace Luki\Config;

use Luki\Config\basicAdapter;

/**
 * Config xml adapter
 * 
 * @package Luki
 */
class xmlAdapter extends basicAdapter
{

    public function __construct($fileName, $allowCreate = FALSE)
    {
        parent::__construct($fileName, $allowCreate);

        libxml_use_internal_errors(TRUE);
        $xml = simplexml_load_file($this->fileName, 'SimpleXMLElement', LIBXML_NOERROR);
        $this->configuration = json_decode(json_encode($xml), TRUE);

        unset($fileName, $xml, $allowCreate);
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $content = new DOMDocument('1.0', 'UTF-8');
        $content->preserveWhiteSpace = false;
        $content->formatOutput = true;
        $element = $content->createElement('configuration');
        $content->appendChild($element);

        foreach ( $this->configuration as $section => $values ) {
            $newSection = $content->createElement($section);
            $content->documentElement->appendChild($newSection);

            foreach ( $values as $key => $value ) {
                $newSection->appendChild($content->createElement($key, $value));
            }
        }
        $isSaved = $this->saveToFile($content->saveXML());

        unset($content, $element, $section, $values, $newSection, $key, $value);
        return $isSaved;
    }

}

# End of file