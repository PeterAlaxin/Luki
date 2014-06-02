<?php

/**
 * File chache adapter
 *
 * Luki framework
 * Date 24.9.2012
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

namespace Luki\Cache;

use Luki\Cache\basicInterface;

/**
 * File chache adapter
 * 
 * @package Luki
 */
class fileAdapter implements basicInterface
{

    private $_path;

    public function __construct($options = array())
    {
        if ( empty($options) or ! is_array($options) ) {
            $options = array( 'path' => '/tmp/' );
        }
        $this->_path = $options['path'];

        unset($options);
    }

    public function Set($key, $value = '', $expirationInSeconds = 0)
    {
        $isSet = FALSE;
        $content = array( 'expiration' => $expirationInSeconds,
          'created' => time(),
          'value' => $value );

        if ( FALSE !== file_put_contents($this->_path . $key, serialize($content), LOCK_EX) ) {
            $isSet = TRUE;
        }

        unset($key, $value, $expirationInSeconds, $content);
        return $isSet;
    }

    public function Get($key)
    {
        $value = FALSE;

        if ( is_file($this->_path . $key) ) {
            $content = unserialize(file_get_contents($this->_path . $key));
            if ( !$this->isExpired($content) ) {
                $value = $content['value'];
            } else {
                $this->Delete($key);
            }
        }

        unset($key, $content);
        return $value;
    }

    public function Delete($key)
    {
        $isDeleted = FALSE;

        if ( is_file($this->_path . $key) ) {
            $isDeleted = unlink($this->_path . $key);
        }

        unset($key);
        return $isDeleted;
    }

    private function isExpired($content)
    {
        $isExpired = TRUE;

        if ( $content['expiration'] == 0 or time() < $content['created'] + $content['expiration'] ) {
            $isExpired = FALSE;
        }

        unset($content);
        return $isExpired;
    }

}

# End of file