<?php

/**
 * Navigation class
 *
 * Luki framework
 * Date 17.12.2012
 *
 * @version 3.0.0
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

use Luki\Navigation\Item;

/**
 * Navigation class
 *
 * @package Luki
 */
class Navigation
{

    private $_navigations = array();

    public function addItem(Item $item)
    {
        $parent = $item->parent;

        if ( 0 == $parent ) {
            $this->_navigations[] = $item;
        } else {
            $parentItem = $this->getItem($parent);

            if ( !empty($parentItem) ) {
                $parentItem->addItem($item);
            }
        }

        unset($item, $parent, $parentItem);
        return $this;
    }

    public function getItem($itemId)
    {
        $foundItem = NULL;

        foreach ( $this->_navigations as $item ) {
            if ( $itemId == $item->id ) {
                $foundItem = $item;
                break;
            } else {
                $foundItem = $item->getItem($itemId);

                if ( !empty($foundItem) ) {
                    break;
                }
            }
        }

        unset($itemId, $item);
        return $foundItem;
    }

    public function getNavigation()
    {
        return $this->_navigations;
    }

}

# End of file