<?php
/**
 * Xml Log Writer
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Log
 * @filesource
 */

namespace Luki\Log\Writer;

use Luki\Log\Writer\BasicFactory;
use Luki\Log\Writer\BasicInterface;

class Xml extends BasicFactory implements BasicInterface
{

    private $xml = null;

    public function __construct($file = '')
    {
        parent::__construct($file);

        if (is_file($this->file)) {
            $this->xml = new \SimpleXMLElement($this->file, LIBXML_NOERROR, true);
        } else {
            $file = '<?xml version="1.0" encoding="UTF-8"?><items></items>';
            $this->xml = new \SimpleXMLElement($file);
        }
    }

    public function Write($content)
    {
        if (is_array($content)) {
            $item = $this->xml->addChild('item');

            foreach ($content as $key => $value) {
                $item->addChild($key, $value);
            }
        } else {
            $this->xml->addChild('item', $content);
        }

        $this->xml->asXML($this->file);
    }
}
