<?php
/**
 * Request class
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

namespace Luki;

use Luki\Request\CookieAdapter;
use Luki\Request\FilesAdapter;
use Luki\Request\GetAdapter;
use Luki\Request\PostAdapter;
use Luki\Request\ServerAdapter;

class Request
{
    public $cookie;
    public $files;
    public $get;
    public $post;
    public $server;
    private $ajax           = null;
    private $baseUrl        = null;
    private $clientIP       = null;
    private $crumb          = null;
    private $crumbCount     = null;
    private $fullUrl        = null;
    private $httpHost       = null;
    private $httpUserAgent  = null;
    private $languages      = null;
    private $pathInfo       = null;
    private $protocol       = null;
    private $queryString    = null;
    private $redirectStatus = null;
    private $requestMethod  = null;
    private $requestTime    = null;
    private $requestUri     = null;
    private $safe           = null;
    private $scriptName     = null;
    private $serverName     = null;
    private $shortUrl       = null;
    private $url            = null;

    public function __construct()
    {
        $this->cookie = new CookieAdapter();
        $this->files  = new FilesAdapter();
        $this->get    = new GetAdapter();
        $this->post   = new PostAdapter();
        $this->server = new ServerAdapter();

        $this->getFullUrl();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function reset()
    {
        $this->ajax           = null;
        $this->baseUrl        = null;
        $this->clientIP       = null;
        $this->crumb          = null;
        $this->crumbCount     = null;
        $this->fullUrl        = null;
        $this->httpHost       = null;
        $this->httpUserAgent  = null;
        $this->languages      = null;
        $this->pathInfo       = null;
        $this->protocol       = null;
        $this->queryString    = null;
        $this->redirectStatus = null;
        $this->requestMethod  = null;
        $this->requestTime    = null;
        $this->requestUri     = null;
        $this->safe           = null;
        $this->scriptName     = null;
        $this->serverName     = null;
        $this->shortUrl       = null;
        $this->url            = null;

        $this->getFullUrl();
    }

    public function getRequestTime()
    {
        if (is_null($this->requestTime)) {
            $this->requestTime = $this->server->get('REQUEST_TIME');
        }

        return $this->requestTime;
    }

    public function getRequestUri()
    {
        if (is_null($this->requestUri)) {
            $this->requestUri = $this->server->get('REQUEST_URI');
        }

        return $this->requestUri;
    }

    public function getRequestMethod()
    {
        if (is_null($this->requestMethod)) {
            $this->requestMethod = $this->server->get('REQUEST_METHOD');
        }

        return $this->requestMethod;
    }

    public function getClientIP()
    {
        if (is_null($this->clientIP)) {
            $this->clientIP = $this->server->get('REMOTE_ADDR');
        }

        return $this->clientIP;
    }

    public function getScriptName()
    {
        if (is_null($this->scriptName)) {
            $this->scriptName = $this->server->get('SCRIPT_NAME');
        }

        return $this->scriptName;
    }

    public function getPathInfo()
    {
        if (is_null($this->pathInfo)) {
            $this->pathInfo = $this->server->get('PATH_INFO');
        }

        return $this->pathInfo;
    }

    public function getRedirectStatus()
    {
        if (is_null($this->redirectStatus)) {
            $this->redirectStatus = $this->server->get('REDIRECT_STATUS');
        }

        return $this->redirectStatus;
    }

    public function getHost()
    {
        if (is_null($this->httpHost)) {
            $this->httpHost = $this->server->get('HTTP_HOST');
        }

        return $this->httpHost;
    }

    public function getUserAgent()
    {
        if (is_null($this->httpUserAgent)) {
            $this->httpUserAgent = $this->server->get('HTTP_USER_AGENT');
        }

        return $this->httpUserAgent;
    }

    public function getLanguages()
    {
        if (is_null($this->languages)) {
            $languages = explode(',', $this->server->get('HTTP_ACCEPT_LANGUAGE'));

            foreach ($languages as $language) {
                $languageExploded  = explode(';', $language);
                $this->languages[] = $languageExploded[0];
            }
        }

        return $this->languages;
    }

    public function getProtocol()
    {
        if (is_null($this->protocol)) {
            $this->protocol = 'http';

            if ($this->isSafe()) {
                $this->protocol .= 's';
            }

            $this->protocol .= '://';
        }

        return $this->protocol;
    }

    public function getServerName()
    {
        if (is_null($this->serverName)) {
            $this->serverName = $this->server->get('SERVER_NAME');
        }

        return $this->serverName;
    }

    public function getQueryString()
    {
        if (is_null($this->queryString)) {
            $this->queryString = $this->server->get('QUERY_STRING');
        }

        return $this->queryString;
    }

    public function getBaseUrl()
    {
        if (is_null($this->baseUrl)) {
            $file          = '/'.basename($this->getScriptName()).'/';
            $this->baseUrl = ltrim(preg_replace($file, '', $this->scriptName), '/');
        }

        return $this->baseUrl;
    }

    public function getFullUrl()
    {
        if (is_null($this->fullUrl)) {
            $this->fullUrl = $this->getShortUrl().
                $this->getURL();
        }

        return $this->fullUrl;
    }

    public function getShortUrl()
    {
        if (is_null($this->shortUrl)) {
            $this->shortUrl = $this->getProtocol().
                $this->getHost().'/'.
                $this->getBaseUrl();
        }

        return $this->shortUrl;
    }

    public function getURL()
    {
        if (is_null($this->url)) {
            $from = array('?'.$this->getQueryString());
            $this->getBaseUrl();
            if (!empty($this->baseUrl)) {
                $from[] = '/'.$this->baseUrl;
            }
            $this->url = urldecode($this->getRequestUri());

            foreach ($from as $item) {
                $this->url = str_replace($item, '', $this->url);
            }

            if ('/' == substr($this->url, 0, 1)) {
                $this->url = substr($this->url, 1);
            }
        }

        return $this->url;
    }

    public function getCrumb($index = null)
    {
        if (is_null($this->crumb)) {
            $this->crumb = explode('/', $this->getURL());
        }

        $crumb = null;

        if (is_null($index)) {
            $crumb = $this->crumb;
        } else {
            $index = (int) $index;
            if (!empty($this->crumb[$index])) {
                $crumb = $this->crumb[$index];
            }
        }

        return $crumb;
    }

    public function getCrumbCount()
    {
        if (is_null($this->crumbCount)) {
            $this->crumbCount = count($this->getCrumb());
        }

        return $this->crumbCount;
    }

    public function isAjax()
    {
        if (is_null($this->ajax)) {
            $this->ajax = (strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest');
        }

        return $this->ajax;
    }

    public function isSafe()
    {
        if (is_null($this->safe)) {
            if ($this->server->get('HTTPS') === null) {
                $this->safe = false;
            } else {
                $this->safe = true;
            }
        }

        return $this->safe;
    }
}