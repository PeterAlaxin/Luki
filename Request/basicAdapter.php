<?php

/**
 * Basic request adapter
 *
 * Luki framework
 * Date 11.8.2013
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

namespace Luki\Request;

/**
 * Basic request adapter
 * 
 * @package Luki
 */
abstract class basicAdapter
{

    public $data = array();

    public function saveInputs($inputs)
    {
        if ( !empty($inputs) and is_array($inputs) ) {

            foreach ( $inputs as $key => $input ) {

                # Reverse automatic addslashes
                if ( get_magic_quotes_gpc() ) {
                    $input = stripslashes($input);
                }

                # HTML Special chars
                $input = htmlspecialchars($input, ENT_QUOTES);

                # Save input
                $this->data[$key] = $input;
            }
        }

        unset($inputs, $key, $input);
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

        unset($values);
    }

    public function replace(array $values = array())
    {
        $this->data = $values;

        unset($values);
    }

    public function get($key, $default = NULL)
    {
        if ( $this->has($key) ) {
            $default = $this->data[$key];
        }

        unset($key);
        return $default;
    }

    public function set($key, $value)
    {
        $this->add(array( $key => $value ));
    }

    public function has($key)
    {
        $has = array_key_exists($key, $this->data);

        unset($key);
        return $has;
    }

    public function remove($key)
    {
        if ( $this->has($key) ) {
            unset($this->data[$key]);
        }

        unset($key);
    }

    public function getAlpha($key, $default = NULL)
    {
        $default = preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));

        unset($key);
        return $default;
    }

    public function getAlnum($key, $default = NULL)
    {
        $default = preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));

        unset($key);
        return $default;
    }

    public function getDigits($key, $default = NULL)
    {
        $default = preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));

        unset($key);
        return $default;
    }

    public function getInt($key, $default = NULL)
    {
        $default = (int) $this->get($key, $default);

        unset($key);
        return $default;
    }

    public function filter($key, $default = NULL, $filter = FILTER_DEFAULT, $options = array())
    {
        $default = filter_var($this->get($key, $default), $filter, $options);

        unset($key, $filter, $options);
        return $default;
    }

}

# End of file