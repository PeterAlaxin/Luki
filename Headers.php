<?php
/**
 * Headers class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Headers
 * @filesource
 */

namespace Luki;

class Headers
{
    private $headers = array();
    private $cachingDirective = 'public';
    private $maxAge = 3600;
    private $sMaxAge = 0;
    private $mustRevalidate = false;
    private $proxyRevalidate = false;
    private $date = null;

    public function clearHeaders()
    {
        $this->headers = array();

        return $this;
    }

    public function setHeaders()
    {
        if (!empty($this->headers)) {
            if (is_null($this->date)) {
                $this->date = new \DateTime;
            }
            $this->createDateHeaders();
        }

        foreach ($this->headers as $header => $value) {
            header($header.': '.$value);
        }
        return $this;
    }

    public function setPublic()
    {
        $this->cachingDirective = 'public';
        $this->createCacheControl();

        return $this;
    }

    public function setPrivate()
    {
        $this->cachingDirective = 'private';
        $this->createCacheControl();

        return $this;
    }

    public function setNoCache()
    {
        $this->cachingDirective = 'no-cache';
        $this->createCacheControl();

        return $this;
    }

    public function setNoStore()
    {
        $this->cachingDirective = 'no-store';
        $this->createCacheControl();

        return $this;
    }

    public function setMaxAge($interval)
    {
        $this->maxAge = (int) $interval;
        $this->createCacheControl();

        return $this;
    }

    public function setSMaxAge($interval)
    {
        $this->sMaxAge = (int) $interval;
        $this->createCacheControl();

        return $this;
    }

    public function setMustRevalidate()
    {
        $this->mustRevalidate = true;
        $this->createCacheControl();

        return $this;
    }

    public function setProxyRevalidate()
    {
        $this->proxyRevalidate = true;
        $this->createCacheControl();

        return $this;
    }

    public function setDate(\DateTime $datetime)
    {
        $this->date = $datetime;
        $this->createDateHeaders();

        return $this;
    }

    public function setVary($vary)
    {
        $this->headers['Vary'] = (string) $vary;

        return $this;
    }

    private function createCacheControl()
    {
        $cacheControl = $this->cachingDirective;
        if (in_array($this->cachingDirective,
                        array('public', 'private'))) {
            if ($this->maxAge > 0) {
                $cacheControl .= ', max-age='.$this->maxAge;
            }
            if ($this->sMaxAge > 0) {
                $cacheControl .= ', s-maxage='.$this->sMaxAge;
            }
            if ($this->mustRevalidate) {
                $cacheControl .= ', must-revalidate';
            }
            if ($this->proxyRevalidate) {
                $cacheControl .= ', proxy-revalidate';
            }
        }

        $this->headers['Cache-Control'] = $cacheControl;
        $this->headers['Pragma'] = $this->cachingDirective;
    }

    private function createDateHeaders()
    {
        $interval = new \DateInterval('PT'.$this->maxAge.'S');

        $this->headers['Date'] = $this->date->format('r');
        $this->headers['Last-Modified'] = $this->date->format('r');

        $this->date->add($interval);

        $this->headers['Expires'] = $this->date->format('r');
    }
}