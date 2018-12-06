<?php
/**
 * Basic request adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Request
 * @filesource
 */

namespace Luki\Request;

abstract class BasicAdapter
{
    public $data = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function saveInputs($inputs)
    {
        if (!empty($inputs) and is_array($inputs)) {
            foreach ($inputs as $key => $input) {
                if (!is_array($input)) {
                    if (get_magic_quotes_gpc()) {
                        $input = stripslashes($input);
                    }

                    #$input = htmlspecialchars($input, ENT_QUOTES);
                    #$input = htmlspecialchars_decode(htmlspecialchars($input, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'), ENT_NOQUOTES);
                    $input = htmlspecialchars($input, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8');
                }
                $this->data[$key] = $input;
            }
        }

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function keys()
    {
        $keys = array_keys($this->data);

        return $keys;
    }

    public function add(array $values = array())
    {
        $this->data = array_replace($this->data, $values);

        return $this;
    }

    public function replace(array $values = array())
    {
        $this->data = $values;

        return $this;
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $default = $this->data[$key];
        }

        return $default;
    }

    public function set($key, $value)
    {
        $this->add(array($key => $value));

        return $this;
    }

    public function has($key)
    {
        $has = array_key_exists($key, $this->data);

        return $has;
    }

    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }

        return $this;
    }

    public function getAlpha($key, $default = null)
    {
        $default = preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));

        return $default;
    }

    public function getAlnum($key, $default = null)
    {
        $default = preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));

        return $default;
    }

    public function getDigits($key, $default = null)
    {
        $default = preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));

        return $default;
    }

    public function getInt($key, $default = null)
    {
        $default = (int) $this->get($key, $default);

        return $default;
    }

    public function filter($key, $default = null, $filter = FILTER_DEFAULT, $options = array())
    {
        $default = filter_var($this->get($key, $default), $filter, $options);

        return $default;
    }

    public function hasData()
    {
        $hasData = !empty($this->data);

        return $hasData;
    }

    public function clear()
    {
        $this->data = array();

        return $this;
    }
}