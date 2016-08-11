<?php
/**
 * Navigation Item
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

namespace Luki\Navigation;

use Luki\Url;

class Item
{

    private $item = array(
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
    private $navigation = array();

    public function __construct($id, $label = '', $url = '')
    {
        $this->item['id'] = (int) $id;

        if (!empty($label)) {
            $this->label($label);
        }

        if (!empty($url)) {
            $this->crumb($url);
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function __call($method, $parameters)
    {
        if (!empty($parameters[0]) or in_array($parameters[0], array(0, false, ''))) {
            $this->item[$method] = $parameters[0];
        }

        if ('label' == $method) {
            $this->item['crumb'] = Url::makeLink($parameters[0]);
        }

        return $this;
    }

    public function __get($name)
    {
        if (isset($this->item[$name])) {
            $value = $this->item[$name];
        } else {
            $value = null;
        }

        return $value;
    }

    public function addItem($item)
    {
        $this->navigation[] = $item;
    }

    public function getItem($id)
    {
        $foundItem = null;

        foreach ($this->navigation as $item) {
            if ($id == $item->id) {
                $foundItem = $item;
                break;
            } else {
                $foundItem = $item->getItem($id);

                if (!empty($foundItem)) {
                    break;
                }
            }
        }

        return $foundItem;
    }

    public function getNavigation()
    {
        return $this->navigation;
    }
}
