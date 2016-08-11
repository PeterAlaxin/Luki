<?php
/**
 * Crumb Navigation Format
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Navigation
 * @filesource
 */

namespace Luki\Navigation\Format;

use Luki\Navigation\Format\BasicFactory;
use Luki\Navigation\Format\BasicInterface;

class Crumb extends BasicFactory implements BasicInterface
{

    public $format = '<a href="%url%" title="%title%" class="%class%" target="%target%">%label%</a>';
    private $used = array('label', 'title', 'class', 'target');

    public function Format($options)
    {
        $itemName = $options['id'];
        $items = $this->createArray($itemName);
        $crumb = '';
        $return = '';

        foreach ($items as $item) {
            $format = $this->format;

            foreach ($this->used as $sKey) {
                $format = preg_replace('/%' . $sKey . '%/', $item->$sKey, $format);
            }

            $crumb .= $item->crumb . '/';
            $return .= $this->sanitizeText(preg_replace('/%url%/', $crumb, $format));
        }

        return $return;
    }

    private function createArray($itemName)
    {
        $items = array();

        do {
            $item = $this->_navigation->getItem($itemName);
            $items[] = $item;
            $itemName = $item->parent;
        } while ($itemName > 0);

        $return = array_reverse($items);

        return $return;
    }

    private function sanitizeText($text)
    {
        $sanitizeFrom = array('/ id=""/', '/ class=""/', '/ class=" "/', '/ title=""/');
        $sanitizeTo = array('', '', '', '');

        $output = preg_replace($sanitizeFrom, $sanitizeTo, $text);

        return $output;
    }
}
