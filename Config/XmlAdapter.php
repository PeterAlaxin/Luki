<?php
/**
 * Config XML adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Config
 * @filesource
 */

namespace Luki\Config;

use Luki\Config\BasicAdapter;

class XmlAdapter extends BasicAdapter
{
    private $xml;

    public function __construct($fileName, $allowCreate = false)
    {
        parent::__construct($fileName, $allowCreate);

        libxml_use_internal_errors(true);
        $this->xml           = simplexml_load_file($this->fileName, 'SimpleXMLElement', LIBXML_NOERROR);
        $this->configuration = json_decode(json_encode($this->xml), true);
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $content                     = new DOMDocument('1.0', 'UTF-8');
        $content->preserveWhiteSpace = false;
        $content->formatOutput       = true;
        $element                     = $content->createElement('configuration');
        $content->appendChild($element);

        foreach ($this->configuration as $section => $values) {
            $newSection = $content->createElement($section);
            $content->documentElement->appendChild($newSection);

            foreach ($values as $key => $value) {
                $newSection->appendChild($content->createElement($key, $value));
            }
        }
        $isSaved = $this->saveToFile($content->saveXML());

        return $isSaved;
    }

    public function getAttributes($section, $node, $key)
    {
        $attributes = [];

        foreach ($this->xml->$section->$node[$key]->attributes() as $name => $value) {
            $attributes[$name] = (string) $value;
        }

        return $attributes;
    }
}