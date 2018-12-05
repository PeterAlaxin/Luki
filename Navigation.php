<?php
/**
 * Navigation class
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

namespace Luki;

use Luki\Navigation\Item;

class Navigation
{
    private $navigations = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function addItem(Item $item)
    {
        $parent = $item->parent;

        if (0 == $parent) {
            $this->navigations[] = $item;
        } else {
            $parentItem = $this->getItem($parent);

            if (!empty($parentItem)) {
                $parentItem->addItem($item);
            }
        }

        return $this;
    }

    public function getItem($itemId)
    {
        $foundItem = null;

        foreach ($this->navigations as $item) {
            if ($itemId == $item->id) {
                $foundItem = $item;
                break;
            } else {
                $foundItem = $item->getItem($itemId);

                if (!empty($foundItem)) {
                    break;
                }
            }
        }

        return $foundItem;
    }

    public function getNavigation()
    {
        return $this->navigations;
    }
}