<?php

/**
 * Navigation Item
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

namespace Luki\Navigation;

use Luki\Url;

/**
 * Navigation Item
 * 
 * @package Luki
 */
class Item
{

    private $_item = array(
      'id' => 0,
      'parent' => 0,
      'label' => '',
      'crumb' => '',
      'title' => '',
      'url' => '',
      'target' => '_self',
      'class' => '',
      'hidden' => '',
      'active' => '',
      'controller' => '',
      'action' => '',
    );
    private $_navigation = array();

    public function __construct($id, $label = '', $url = '')
    {
        $this->_item['id'] = (int) $id;

        if ( !empty($label) ) {
            $this->label($label);
        }

        if ( !empty($url) ) {
            $this->crumb($url);
        }

        unset($id, $label, $url);
    }

    public function __call($method, $parameters)
    {
        if ( !empty($parameters[0]) or in_array($parameters[0], array( 0, FALSE, '' )) ) {
            $this->_item[$method] = $parameters[0];
        }

        if ( 'label' == $method ) {
            $this->_item['crumb'] = Url::makeLink($parameters[0]);
        }

        unset($method, $parameters);
        return $this;
    }

    public function __get($name)
    {
        $value = NULL;

        if ( isset($this->_item[$name]) ) {
            $value = $this->_item[$name];
        }

        unset($name);
        return $value;
    }

    public function addItem($item)
    {
        $this->_navigation[] = $item;
    }

    public function getItem($id)
    {
        $foundItem = NULL;

        foreach ( $this->_navigation as $item ) {
            if ( $id == $item->id ) {
                $foundItem = $item;
                break;
            } else {
                $foundItem = $item->getItem($id);

                if ( !empty($foundItem) ) {
                    break;
                }
            }
        }

        unset($id, $item);
        return $foundItem;
    }

    public function getNavigation()
    {
        return $this->_navigation;
    }

}

# End of file