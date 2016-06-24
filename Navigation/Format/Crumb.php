<?php

/**
 * Crumb Navigation Format
 *
 * Luki framework
 * Date 17.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Navigation\Format;

use Luki\Navigation\Format\basicInterface;

/**
 * Crumb Navigation Format
 * 
 * @package Luki
 */
class Crumb implements basicInterface
{

    private $_format = '<a href="%url%" title="%title%" class="%class%" target="%target%">%label%</a>';
    private $_navigation = NULL;
    private $_used = array(
      'label',
      'title',
      'class',
      'target'
    );

    public function __construct($navigation)
    {
        $this->_navigation = $navigation;

        unset($navigation);
    }

    public function setFormat($format)
    {
        $this->_format = $format;

        unset($format);
        return $this;
    }

    public function Format($options)
    {
        $itemName = $options['id'];
        $items = $this->_createArray($itemName);
        $crumb = '';
        $return = '';

        foreach ( $items as $item ) {
            $format = $this->_format;

            foreach ( $this->_used as $sKey ) {
                $format = preg_replace('/%' . $sKey . '%/', $item->$sKey, $format);
            }

            $crumb .= $item->crumb . '/';
            $return .= $this->_sanitize(preg_replace('/%url%/', $crumb, $format));
        }

        unset($options, $itemName, $item, $sKey, $crumb, $items, $format);
        return $return;
    }

    private function _createArray($itemName)
    {
        $items = array();

        do {
            $item = $this->_navigation->getItem($itemName);
            $items[] = $item;
            $itemName = $item->parent;
        }
        while ( $itemName > 0 );

        $return = array_reverse($items);

        unset($itemName, $item, $items);
        return $return;
    }

    private function _sanitize($text)
    {
        $sanitizeFrom = array('/ id=""/', '/ class=""/', '/ class=" "/', '/ title=""/');
        $sanitizeTo = array('', '', '', '');
        
        $output = preg_replace($sanitizeFrom, $sanitizeTo, $text);

        unset($text, $sanitizeFrom, $sanitizeTo);
        return $output;
    }
}

# End of file