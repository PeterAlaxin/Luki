<?php
/**
 * Cookie class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Cookie
 * @filesource
 */

namespace Luki;

class Cookie
{
    private $name;
    private $value    = '';
    private $expires  = '';
    private $path     = '/';
    private $domain   = '';
    private $secure   = false;
    private $httponly = true;
    private $options;
    private $exist;

    public function __construct($name)
    {
        $this->name  = $name;
        $this->exist = !empty($_COOKIE[$name]);

        if ($this->exist) {
            $this->value = $_COOKIE[$name];
        }

        $this->options = array(
            'path'     => $this->path,
            'secure'   => $this->secure,
            'httponly' => $this->httponly
        );
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function exist()
    {
        return $this->exist;
    }

    public function setValue($value)
    {
        $this->value = (string) $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setExpiration($expiration)
    {
        $this->expires            = strtotime($expiration);
        $this->options['expires'] = $this->expires;

        return $this;
    }

    public function getExpiration()
    {
        return $this->expires;
    }

    public function setPath($path)
    {
        $this->path            = $path;
        $this->options['path'] = $this->path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setDomain($domain)
    {
        $this->domain            = $domain;
        $this->options['domain'] = $this->domain;

        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setSecure($secure)
    {
        $this->secure            = (bool) $secure;
        $this->options['secure'] = $this->secure;

        return $this;
    }

    public function getSecure()
    {
        return $this->secure;
    }

    public function setHttponly($httponly)
    {
        $this->httponly            = (bool) $httponly;
        $this->options['httponly'] = $this->httponly;

        return $this;
    }

    public function getHttponly()
    {
        return $this->httponly;
    }

    public function save()
    {
        return setcookie($this->name, $this->value, $this->expires, $this->path, $this->domain, $this->secure,
            $this->httponly);
    }

    public function delete()
    {
        unset($_COOKIE[$this->name]);
        return setcookie($this->name, '', time() - 3600, $this->path);
    }
}